<?php

namespace App\Services\EventAi;

use App\Models\Event;
use App\Models\Event\EventCategory;
use App\Models\Event\EventContent;
use App\Models\Language;

class EventFactsBuilder
{
  public function fromEvent(Event $event): array
  {
    $content = $this->defaultContent($event);
    $category = $content?->event_category_id ? EventCategory::find($content->event_category_id) : null;

    return [
      'event_id' => $event->id,
      'event_type' => $event->event_type,
      'date_type' => $event->date_type,
      'start_date' => $event->start_date,
      'start_time' => $event->start_time,
      'end_date' => $event->end_date,
      'end_time' => $event->end_time,
      'duration' => $event->duration,
      'category' => $category?->name,
      'title' => $content?->title,
      'description' => $content?->description ? trim(strip_tags($content->description)) : null,
      'address' => $content?->address,
      'city' => $content?->city,
      'state' => $content?->state,
      'country' => $content?->country,
      'zip_code' => $content?->zip_code,
      'has_thumbnail' => !empty($event->thumbnail),
      'timezone' => config('app.timezone', 'America/Argentina/Buenos_Aires'),
    ];
  }

  public function canonicalFromAnalysis(Event $event, array $analysis): array
  {
    return [
      'source_priority' => [
        'confirmed_form_fields',
        'organizer_free_text',
        'organizer_notes',
        'organizer_review',
        'accepted_image_fields',
        'marketing_inference',
      ],
      'form_facts' => $this->fromEvent($event),
      'image_analysis' => [
        'summary' => $analysis['summary'] ?? '',
        'extracted_fields' => $analysis['extracted_fields'] ?? [],
        'found_information' => $analysis['found_information'] ?? [],
        'complementary_information' => $analysis['complementary_information'] ?? [],
        'optional_suggestions' => $analysis['optional_suggestions'] ?? [],
        'critical_differences' => $analysis['critical_differences'] ?? [],
        'conflicts' => $analysis['conflicts'] ?? [],
        'missing_information' => $analysis['missing_information'] ?? [],
        'sensitive_fields' => $analysis['sensitive_fields'] ?? [],
        'sponsors' => $analysis['sponsors'] ?? [],
        'warnings' => $analysis['warnings'] ?? [],
      ],
      'confirmed_fields' => [],
      'ignored_fields' => [],
      'locale' => 'es-AR',
      'timezone' => config('app.timezone', 'America/Argentina/Buenos_Aires'),
    ];
  }

  private function defaultContent(Event $event): ?EventContent
  {
    $defaultLanguageId = Language::where('is_default', 1)->value('id');

    $query = EventContent::where('event_id', $event->id);
    if ($defaultLanguageId) {
      $query->orderByRaw('language_id = ? desc', [$defaultLanguageId]);
    }

    return $query->orderBy('id')->first();
  }
}
