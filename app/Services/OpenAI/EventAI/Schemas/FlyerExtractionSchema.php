<?php

namespace App\Services\OpenAI\EventAI\Schemas;

class FlyerExtractionSchema
{
  public const NAME = 'event_flyer_analysis_v3';

  public const VERSION = '2026-08-21-v3';

  public static function toArray(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => [
        'summary', 'extracted_fields', 'date_candidates', 'time_candidates',
        'price_candidates', 'venue_candidates', 'artists', 'hosts', 'djs',
        'genres_written', 'promotions', 'age_restrictions', 'dress_code',
        'contact_information', 'sponsors', 'ticketing_platforms', 'urls',
        'social_handles', 'event_format_clues', 'visual_theme',
        'found_information', 'complementary_information', 'optional_suggestions',
        'critical_differences', 'conflicts', 'missing_information',
        'sensitive_fields', 'warnings',
      ],
      'properties' => [
        'summary' => ['type' => 'string'],
        'extracted_fields' => ['type' => 'array', 'items' => self::fieldSchema()],
        'date_candidates' => ['type' => 'array', 'items' => self::candidateSchema('YYYY-MM-DD')],
        'time_candidates' => ['type' => 'array', 'items' => self::candidateSchema('HH:MM (24h)')],
        'price_candidates' => ['type' => 'array', 'items' => self::candidateSchema('número decimal + moneda ISO 4217')],
        'venue_candidates' => ['type' => 'array', 'items' => self::candidateSchema('nombre del lugar')],
        'artists' => ['type' => 'array', 'items' => self::namedEntitySchema()],
        'hosts' => ['type' => 'array', 'items' => self::namedEntitySchema()],
        'djs' => ['type' => 'array', 'items' => self::namedEntitySchema()],
        'genres_written' => ['type' => 'array', 'items' => self::namedEntitySchema()],
        'promotions' => ['type' => 'array', 'items' => self::candidateSchema('texto de la promoción')],
        'age_restrictions' => ['type' => 'array', 'items' => self::candidateSchema('texto de la restricción')],
        'dress_code' => ['type' => ['array', 'null'], 'items' => self::candidateSchema('texto del dress code')],
        'contact_information' => ['type' => 'array', 'items' => self::candidateSchema('teléfono/email/contacto')],
        'sponsors' => ['type' => 'array', 'items' => self::fieldSchema()],
        'ticketing_platforms' => ['type' => 'array', 'items' => self::candidateSchema('nombre de plataforma de venta')],
        'urls' => ['type' => 'array', 'items' => ['type' => 'string']],
        'social_handles' => ['type' => 'array', 'items' => self::candidateSchema('handle visible (@usuario)')],
        'event_format_clues' => ['type' => 'array', 'items' => ['type' => 'string']],
        'visual_theme' => ['type' => 'array', 'items' => ['type' => 'string']],
        'found_information' => ['type' => 'array', 'items' => ['type' => 'string']],
        'complementary_information' => ['type' => 'array', 'items' => ['type' => 'string']],
        'optional_suggestions' => ['type' => 'array', 'items' => ['type' => 'string']],
        'critical_differences' => ['type' => 'array', 'items' => ['type' => 'string']],
        'conflicts' => ['type' => 'array', 'items' => ['type' => 'string']],
        'missing_information' => ['type' => 'array', 'items' => ['type' => 'string']],
        'sensitive_fields' => ['type' => 'array', 'items' => ['type' => 'string']],
        'warnings' => ['type' => 'array', 'items' => ['type' => 'string']],
      ],
    ];
  }

  private static function fieldSchema(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => [
        'key', 'label', 'value', 'raw_text', 'normalized_value', 'confidence',
        'source_type', 'source_image', 'needs_review', 'warning_code',
        'sensitive', 'category', 'evidence_type',
      ],
      'properties' => [
        'key' => ['type' => 'string'],
        'label' => ['type' => 'string'],
        'value' => ['type' => ['string', 'null']],
        'raw_text' => ['type' => ['string', 'null']],
        'normalized_value' => ['type' => ['string', 'null']],
        'confidence' => ['type' => 'number'],
        'source_type' => ['type' => 'string'],
        'source_image' => ['type' => ['string', 'null']],
        'needs_review' => ['type' => 'boolean'],
        'warning_code' => ['type' => ['string', 'null']],
        'sensitive' => ['type' => 'boolean'],
        'category' => ['type' => 'string'],
        'evidence_type' => ['type' => 'string', 'enum' => ['visible_fact', 'interpretation']],
      ],
    ];
  }

  private static function candidateSchema(string $format): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => ['raw_text', 'normalized_value', 'confidence', 'needs_review'],
      'properties' => [
        'raw_text' => ['type' => 'string'],
        'normalized_value' => ['type' => ['string', 'null'], 'description' => 'Formato esperado: ' . $format],
        'confidence' => ['type' => 'number'],
        'needs_review' => ['type' => 'boolean'],
      ],
    ];
  }

  private static function namedEntitySchema(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => ['name', 'source', 'confidence'],
      'properties' => [
        'name' => ['type' => 'string'],
        'source' => ['type' => 'string', 'enum' => ['written', 'interpreted']],
        'confidence' => ['type' => 'number'],
      ],
    ];
  }
}
