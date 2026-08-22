<?php

namespace App\Services\EventAi;

use App\Models\BasicSettings\Basic;
use App\Models\Event;
use App\Models\Event\EventContent;
use App\Models\Language;
use Illuminate\Support\Str;

/**
 * Construye el contrato de hechos canónicos V3 de forma DETERMINISTA.
 * La IA no decide qué dato de negocio es la verdad: este builder lo hace.
 */
class CanonicalEventFactsBuilder
{
  /**
   * @param Event $event
   * @param array|null $analysis resultado del análisis de flyer (schema V2 o V3)
   * @param EventContent|null $content contenido por idioma (default si es null)
   */
  public function build(Event $event, ?array $analysis = null, ?EventContent $content = null): CanonicalEventFacts
  {
    $facts = new CanonicalEventFacts(
      (string) config('app.locale', 'es-AR'),
      (string) config('app.timezone', 'America/Argentina/Buenos_Aires'),
    );

    $content ??= $this->defaultContent($event);

    $this->addEventCore($facts, $event, $content);
    $this->addVenue($facts, $event, $content);
    $this->addTickets($facts, $event);
    $this->addPlatform($facts);
    $this->addFlyerEvidence($facts, $analysis);

    return $facts;
  }

  private function addEventCore(CanonicalEventFacts $facts, Event $event, ?EventContent $content): void
  {
    $facts
      ->add('event_type', $event->event_type, CanonicalEventFacts::SOURCE_DATABASE, ['sensitive' => false])
      ->add('date_type', $event->date_type, CanonicalEventFacts::SOURCE_DATABASE)
      ->add('start_date', $event->start_date, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('start_time', $event->start_time, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('end_date', $event->end_date, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('end_time', $event->end_time, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('duration', $event->duration, CanonicalEventFacts::SOURCE_DATABASE)
      ->add('countdown_status', $event->countdown_status, CanonicalEventFacts::SOURCE_DATABASE)
      ->add('meeting_url', $event->meeting_url, CanonicalEventFacts::SOURCE_DATABASE, ['sensitive' => true]);

    if ($content) {
      $facts
        ->add('title', $content->title, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
        ->add('description', Str::limit(strip_tags((string) $content->description), 2000, ''), CanonicalEventFacts::SOURCE_ORGANIZER_TEXT)
        ->add('category', $this->categoryName($content), CanonicalEventFacts::SOURCE_DATABASE)
        ->add('meta_keywords', $content->meta_keywords, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => false])
        ->add('meta_description', $content->meta_description, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => false]);
    }

    if ($organizer = $event->organizer()->first()) {
      $facts->add('organizer', $organizer->username, CanonicalEventFacts::SOURCE_DATABASE, ['sensitive' => false]);
    }
  }

  private function addVenue(CanonicalEventFacts $facts, Event $event, ?EventContent $content): void
  {
    if ($event->event_type !== 'venue' || !$content) {
      return;
    }

    $facts
      ->add('address', $content->address, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('city', $content->city, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('state', $content->state, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('country', $content->country, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('zip_code', $content->zip_code, CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true])
      ->add('latitude', $event->latitude, CanonicalEventFacts::SOURCE_DATABASE, ['sensitive' => true])
      ->add('longitude', $event->longitude, CanonicalEventFacts::SOURCE_DATABASE, ['sensitive' => true]);
  }

  private function addTickets(CanonicalEventFacts $facts, Event $event): void
  {
    $tickets = $event->tickets()->where('pricing_type', '!=', 'variation')->get();

    if ($tickets->isEmpty()) {
      return;
    }

    $prices = $tickets->pluck('price')->map(fn ($p) => (float) $p)->filter(fn ($p) => $p > 0);

    if ($prices->isNotEmpty()) {
      $volatile = ['sensitive' => true, 'copy_safe' => false, 'source' => CanonicalEventFacts::SOURCE_TICKET_DB];
      $facts->add('price', $prices->first(), CanonicalEventFacts::SOURCE_TICKET_DB, $volatile);
      if ($prices->min() !== $prices->max()) {
        $facts->add('price_min', $prices->min(), CanonicalEventFacts::SOURCE_TICKET_DB, $volatile);
        $facts->add('price_max', $prices->max(), CanonicalEventFacts::SOURCE_TICKET_DB, $volatile);
      }
    }

    if ($tickets->contains(fn ($t) => $t->pricing_type === 'free')) {
      $facts->add('pricing_type', 'free', CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true]);
    } elseif ($prices->isNotEmpty()) {
      $facts->add('pricing_type', 'normal', CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true]);
    }

    $firstPaid = $tickets->firstWhere('pricing_type', 'normal');
    if ($firstPaid && $firstPaid->early_bird_discount === 'enable') {
      $facts->add('early_bird_discount', [
        'type' => $firstPaid->early_bird_discount_type,
        'amount' => $firstPaid->early_bird_discount_amount,
        'ends_at' => $firstPaid->early_bird_discount_date . ' ' . $firstPaid->early_bird_discount_time,
      ], CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true, 'copy_safe' => false]);
    }

    $limited = $tickets->firstWhere('ticket_available_type', 'limited');
    if ($limited) {
      $facts->add('ticket_available', (int) $limited->ticket_available, CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true, 'copy_safe' => false]);
    }
  }

  private function addPlatform(CanonicalEventFacts $facts): void
  {
    try {
      $currency = optional(Basic::select('base_currency_text')->first())->base_currency_text;
      if ($currency) {
        $facts->add('currency', $currency, CanonicalEventFacts::SOURCE_PLATFORM, ['sensitive' => false]);
      }
    } catch (\Throwable $e) {
      // sin DB disponible (tests unitarios): la moneda se omite, no es crítica
    }
  }

  private function addFlyerEvidence(CanonicalEventFacts $facts, ?array $analysis): void
  {
    if (!$analysis) {
      return;
    }

    $imageAnalysis = $analysis['image_analysis'] ?? $analysis;

    foreach (($imageAnalysis['extracted_fields'] ?? []) as $field) {
      $key = $field['key'] ?? '';
      $value = $field['value'] ?? $field['raw_text'] ?? null;
      $confidence = (float) ($field['confidence'] ?? 0);

      if (!$key || !$value || $confidence < 0.5) {
        continue;
      }

      $facts->add(
        'flyer:' . $key,
        $value,
        CanonicalEventFacts::SOURCE_FLYER,
        [
          'sensitive' => (bool) ($field['sensitive'] ?? false),
          'confidence' => $confidence,
          'copy_safe' => !(bool) ($field['needs_review'] ?? false),
        ]
      );
    }
  }

  /**
   * Flujo temporal (creación): construye facts desde formFacts V2 + tickets del request.
   *
   * @param array $formFacts con forma EventFactsBuilder::fromEvent() (V2)
   * @param array $ticketFacts ['price' => ?, 'pricing_type' => ?]
   */
  public function buildFromTemporary(array $formFacts, ?array $analysis = null, array $ticketFacts = []): CanonicalEventFacts
  {
    $facts = new CanonicalEventFacts(
      (string) config('app.locale', 'es-AR'),
      (string) config('app.timezone', 'America/Argentina/Buenos_Aires'),
    );

    $map = [
      'event_type' => 'event_type', 'date_type' => 'date_type',
      'start_date' => 'start_date', 'start_time' => 'start_time',
      'end_date' => 'end_date', 'end_time' => 'end_time',
      'duration' => 'duration', 'title' => 'title', 'description' => 'description',
      'address' => 'address', 'city' => 'city', 'state' => 'state',
      'country' => 'country', 'zip_code' => 'zip_code',
      'category' => 'category', 'meeting_url' => 'meeting_url',
    ];

    foreach ($map as $factKey => $formKey) {
      $value = $formFacts[$formKey] ?? null;
      if ($value === null || $value === '') {
        continue;
      }
      $sensitive = in_array($factKey, CanonicalEventFacts::SENSITIVE_KEYS, true);
      $source = in_array($factKey, ['title', 'description', 'address', 'city', 'state', 'country', 'zip_code', 'start_date', 'start_time', 'end_date', 'end_time'], true)
        ? CanonicalEventFacts::SOURCE_FORM
        : CanonicalEventFacts::SOURCE_DATABASE;
      $facts->add($factKey, $value, $source, ['sensitive' => $sensitive]);
    }

    if (!empty($ticketFacts['price'])) {
      $facts->add('price', (float) $ticketFacts['price'], CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true, 'copy_safe' => false]);
    }
    if (!empty($ticketFacts['pricing_type'])) {
      $facts->add('pricing_type', 'free', CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true]);
    } elseif (!empty($ticketFacts['price'])) {
      $facts->add('pricing_type', 'normal', CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true]);
    }

    $this->addPlatform($facts);
    $this->addFlyerEvidence($facts, $analysis);

    return $facts;
  }

  private function categoryName(EventContent $content): ?string
  {
    $category = \DB::table('event_categories')->where('id', $content->event_category_id)->first();

    return $category->name ?? null;
  }

  private function defaultContent(Event $event): ?EventContent
  {
    $defaultLanguageId = optional(Language::where('is_default', 1)->first())->id;

    if (!$defaultLanguageId) {
      return $event->contents()->first();
    }

    return $event->contents()
      ->orderByRaw('language_id = ? desc', [$defaultLanguageId])
      ->first();
  }
}
