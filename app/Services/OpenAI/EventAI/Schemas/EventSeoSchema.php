<?php

namespace App\Services\OpenAI\EventAI\Schemas;

class EventSeoSchema
{
  public const NAME = 'event_seo_v1';

  public const VERSION = '2026-08-21-v1';

  public static function toArray(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => [
        'seo_title', 'google_short_description', 'meta_description',
        'primary_keyword', 'secondary_keywords', 'local_search_variants',
        'tags', 'suggested_slug', 'image_alt_text',
        'schema_event_description', 'ai_search_summary',
      ],
      'properties' => [
        'seo_title' => ['type' => 'string'],
        'google_short_description' => ['type' => 'string'],
        'meta_description' => ['type' => 'string'],
        'primary_keyword' => ['type' => 'string'],
        'secondary_keywords' => ['type' => 'array', 'items' => ['type' => 'string']],
        'local_search_variants' => ['type' => 'array', 'items' => ['type' => 'string']],
        'tags' => ['type' => 'array', 'items' => ['type' => 'string']],
        'suggested_slug' => ['type' => ['string', 'null']],
        'image_alt_text' => ['type' => 'string'],
        'schema_event_description' => ['type' => 'string'],
        'ai_search_summary' => ['type' => 'string'],
      ],
    ];
  }
}
