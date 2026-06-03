<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Event\Ticket;
use App\Models\Event\TicketContent;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeedColombiaWorldCupEvents extends Command
{
  protected $signature = 'events:seed-colombia-worldcup
    {--template-id=118 : Evento base para copiar sede, organizador y configuracion}
    {--dry-run : Muestra lo que haria sin escribir en base}
    {--force : Actualiza eventos existentes encontrados por slug}
    {--include-template : Permite actualizar tambien el evento base si coincide por slug}
    {--publish : Crea/publica eventos con status=1}';

  protected $description = 'Crea los eventos de Colombia en fase de grupos del Mundial 2026 usando un evento existente como plantilla';

  public function handle(): int
  {
    $template = Event::query()->find($this->option('template-id'));
    if (!$template) {
      $this->error('No existe el evento plantilla.');
      return self::FAILURE;
    }

    $language = Language::query()->where('is_default', 1)->first() ?? Language::query()->first();
    if (!$language) {
      $this->error('No hay idiomas configurados.');
      return self::FAILURE;
    }

    $templateContent = EventContent::query()
      ->where('event_id', $template->id)
      ->where('language_id', $language->id)
      ->first();

    if (!$templateContent) {
      $this->error('El evento plantilla no tiene contenido para el idioma principal.');
      return self::FAILURE;
    }

    $rows = [];

    foreach ($this->matches() as $match) {
      $title = $this->title($match);
      $slug = Str::slug($title);
      $existingContent = EventContent::query()->where('slug', $slug)->first();
      $existingEvent = $existingContent ? Event::query()->find($existingContent->event_id) : null;
      $isTemplateEvent = $existingEvent && (int) $existingEvent->id === (int) $template->id;

      if ($existingEvent && !$this->option('force')) {
        $rows[] = [$existingEvent->id, $title, 'existe - omitido'];
        continue;
      }

      if ($isTemplateEvent && !$this->option('include-template')) {
        $rows[] = [$existingEvent->id, $title, 'plantilla - omitida'];
        continue;
      }

      $action = $existingEvent ? 'actualizar' : 'crear';
      $rows[] = [$existingEvent?->id ?? 'nuevo', $title, $action];

      if ($this->option('dry-run')) {
        continue;
      }

      DB::transaction(function () use ($template, $templateContent, $language, $match, $title, $slug, $existingEvent) {
        $event = $existingEvent ?: new Event();
        $isNew = !$existingEvent;

        $event->fill($this->eventAttributes($template, $match, $isNew));
        $event->save();

        $content = EventContent::query()
          ->where('event_id', $event->id)
          ->where('language_id', $language->id)
          ->first() ?: new EventContent();

        $content->language_id = $language->id;
        $content->event_id = $event->id;
        $content->event_category_id = $templateContent->event_category_id;
        $content->title = $title;
        $content->slug = $slug;
        $content->description = $this->description($match);
        $content->meta_keywords = $this->metaKeywords($match);
        $content->meta_description = $this->metaDescription($match);
        $content->address = $templateContent->address;
        $content->country = $templateContent->country;
        $content->state = $templateContent->state;
        $content->city = $templateContent->city;
        $content->zip_code = $templateContent->zip_code;
        $content->google_calendar_id = $templateContent->google_calendar_id;
        $content->refund_policy = $templateContent->refund_policy;
        $content->save();

        $this->syncTickets($event, $language);
      });
    }

    $this->table(['Evento', 'Titulo', 'Accion'], $rows);

    if ($this->option('dry-run')) {
      $this->info('Dry-run: no se modifico la base de datos.');
    }

    return self::SUCCESS;
  }

  private function eventAttributes(Event $template, array $match, bool $isNew): array
  {
    $start = Carbon::parse($match['date'] . ' ' . $match['doors_time']);
    $end = Carbon::parse($match['date'])->addDay()->setTime(7, 59);
    $minutes = $start->diffInMinutes($end);
    $hours = intdiv($minutes, 60);
    $remainingMinutes = $minutes % 60;

    $attributes = [
      'organizer_id' => $template->organizer_id,
      'date_type' => 'single',
      'countdown_status' => $template->countdown_status,
      'start_date' => $start->toDateString(),
      'start_time' => $start->format('H:i'),
      'duration' => "{$hours}h {$remainingMinutes}m",
      'end_date' => $end->toDateString(),
      'end_time' => $end->format('H:i'),
      'end_date_time' => $end->toDateTimeString(),
      'event_type' => 'venue',
      'event_addons_enabled' => $template->event_addons_enabled,
      'is_featured' => $template->is_featured,
      'latitude' => $template->latitude,
      'longitude' => $template->longitude,
      'instructions' => $template->instructions,
      'meeting_url' => $template->meeting_url,
      'meta_pixel_id' => $template->meta_pixel_id,
      'google_analytics_id' => $template->google_analytics_id,
      'tiktok_pixel_id' => $template->tiktok_pixel_id,
      'spotify_url' => $template->spotify_url,
      'youtube_url' => $template->youtube_url,
      'manual_badge' => $template->manual_badge,
    ];

    if ($isNew) {
      $attributes['status'] = $this->option('publish') ? 1 : 0;
      $attributes['thumbnail'] = $template->thumbnail;
      $attributes['ticket_image'] = null;
      $attributes['ticket_logo'] = null;
      $attributes['views_count'] = 0;
      $attributes['views_last_24h'] = 0;
      $attributes['views_last_reset'] = null;
    } elseif ($this->option('publish')) {
      $attributes['status'] = 1;
    }

    return $attributes;
  }

  private function syncTickets(Event $event, Language $language): void
  {
    if (!$event->wasRecentlyCreated && !$this->option('force')) {
      return;
    }

    $bookingsCount = Booking::query()->where('event_id', $event->id)->count();
    if ($bookingsCount > 0) {
      $this->warn("Evento {$event->id}: tiene {$bookingsCount} reservas, no se reemplazaron entradas.");
      return;
    }

    $ticketIds = Ticket::query()->where('event_id', $event->id)->pluck('id');
    if ($ticketIds->isNotEmpty()) {
      TicketContent::query()->whereIn('ticket_id', $ticketIds)->delete();
      DB::table('variation_contents')->whereIn('ticket_id', $ticketIds)->delete();
      Ticket::query()->whereIn('id', $ticketIds)->delete();
    }

    foreach ($this->tickets() as $ticketData) {
      $ticket = Ticket::query()->create([
        'event_id' => $event->id,
        'event_type' => 'venue',
        'title' => null,
        'ticket_available_type' => 'limited',
        'ticket_available' => $ticketData['stock'],
        'max_ticket_buy_type' => $ticketData['max_buy'] ? 'limited' : 'unlimited',
        'max_buy_ticket' => $ticketData['max_buy'],
        'description' => null,
        'pricing_type' => $ticketData['pricing_type'],
        'price' => $ticketData['price'],
        'f_price' => $ticketData['price'],
        'early_bird_discount' => 'disable',
        'early_bird_discount_amount' => null,
        'early_bird_discount_type' => 'fixed',
        'early_bird_discount_date' => null,
        'early_bird_discount_time' => null,
        'variations' => null,
      ]);

      TicketContent::query()->where('ticket_id', $ticket->id)->delete();
      DB::table('variation_contents')->where('ticket_id', $ticket->id)->delete();

      TicketContent::query()->create([
        'language_id' => $language->id,
        'ticket_id' => $ticket->id,
        'title' => $ticketData['title'],
        'description' => $ticketData['description'],
      ]);
    }
  }

  private function matches(): array
  {
    return [
      [
        'opponent' => 'Uzbekistán',
        'date' => '2026-06-17',
        'doors_time' => '21:00',
        'match_time' => '23:00',
        'opener' => 'Colombia debuta en el Mundial 2026 y lo vivimos juntos en una noche llena de fútbol, rumba y orgullo colombiano.',
      ],
      [
        'opponent' => 'Congo DR',
        'date' => '2026-06-23',
        'doors_time' => '21:00',
        'match_time' => '23:00',
        'opener' => 'Colombia juega su segundo partido del Mundial 2026 y lo vivimos juntos con fútbol, rumba y orgullo colombiano.',
      ],
      [
        'opponent' => 'Portugal',
        'date' => '2026-06-27',
        'doors_time' => '18:30',
        'match_time' => '20:30',
        'opener' => 'Colombia cierra la fase de grupos del Mundial 2026 frente a Portugal y lo vivimos juntos en una tarde-noche de fútbol, rumba y orgullo colombiano.',
      ],
    ];
  }

  private function tickets(): array
  {
    return [
      [
        'title' => 'Mujeres Entrada Gratis',
        'description' => 'Entrada sin costo para las primeras 100 mujeres.',
        'pricing_type' => 'free',
        'price' => 0,
        'stock' => 100,
        'max_buy' => 1,
      ],
      [
        'title' => 'Entrada General',
        'description' => 'Entrada general anticipada.',
        'pricing_type' => 'normal',
        'price' => 10000,
        'stock' => 400,
        'max_buy' => null,
      ],
      [
        'title' => 'Mesa VIP 4 personas',
        'description' => 'Mesa VIP para 4 personas.',
        'pricing_type' => 'normal',
        'price' => 60000,
        'stock' => 10,
        'max_buy' => 1,
      ],
      [
        'title' => 'Mesa VIP 6 personas',
        'description' => 'Mesa VIP para 6 personas.',
        'pricing_type' => 'normal',
        'price' => 80000,
        'stock' => 10,
        'max_buy' => 1,
      ],
      [
        'title' => 'Mesa VIP 10 personas',
        'description' => 'Mesa VIP para 10 personas.',
        'pricing_type' => 'normal',
        'price' => 120000,
        'stock' => 10,
        'max_buy' => 1,
      ],
    ];
  }

  private function title(array $match): string
  {
    return "Colombia vs {$match['opponent']}: Rumba y Fiesta Fan Fest Mundial 2026 en Palermo";
  }

  private function description(array $match): string
  {
    $date = Carbon::parse($match['date'])->locale('es')->translatedFormat('l j \d\e F \d\e Y');
    $date = Str::ucfirst($date);
    $opponent = $match['opponent'];

    return <<<HTML
<p>{$match['opener']}</p>
<p>Te esperamos en Honduras Club, Palermo, para alentar a la Selección Colombia frente a {$opponent} en pantalla gigante Full HD, con transmisión de Gol Caracol, ambiente mundialista, música, fiesta y toda la energía de la hinchada colombiana en Buenos Aires.</p>
<p>La entrada incluye acceso al evento, transmisión del partido en pantalla gigante y participación en la previa mundialista con rumba antes del encuentro.</p>
<p>Fecha: {$date}<br />Apertura de puertas: {$match['doors_time']} hs<br />Partido: Colombia vs {$opponent} - {$match['match_time']} hs Argentina<br />Transmisión: Gol Caracol<br />Lugar: Honduras Club<br />Dirección: Honduras 5535, Palermo, CABA</p>
<p>Recomendamos reservar la entrada anticipada para asegurar tu lugar. Cupos limitados.</p>
<p>Venite con la camiseta de Colombia, tu bandera y toda la actitud para vivir a la Selección como se debe: con fútbol, fiesta y comunidad colombiana.</p>
<p>Transmisión del partido Colombia vs {$opponent}<br />Pantalla gigante Full HD<br />Gol Caracol en vivo<br />Rumba y fiesta antes del partido<br />Ambiente colombiano en Palermo<br />Evento presencial en Honduras Club<br />Entrada anticipada recomendada</p>
HTML;
  }

  private function metaKeywords(array $match): string
  {
    return "Colombia vs {$match['opponent']}, Mundial 2026, Gol Caracol, Fan Fest Colombia, Honduras Club, Palermo, entradas Colombia Mundial, rumba colombiana Buenos Aires";
  }

  private function metaDescription(array $match): string
  {
    return "Viví Colombia vs {$match['opponent']} por el Mundial 2026 en Honduras Club, Palermo, con pantalla gigante Full HD, Gol Caracol, rumba y ambiente colombiano.";
  }
}
