<?php

namespace App\Services\OpenAI\EventAI\Schemas;

class EventAuditSchema
{
  public const NAME = 'event_audit_v1';

  public const VERSION = '2026-08-21-v1';

  public static function toArray(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => [
        'factuality', 'specificity', 'persuasion', 'genericness',
        'brand_voice', 'locale_consistency', 'seo_quality',
        'unsupported_claims', 'contradictions', 'stale_fact_risks',
        'blocking_failures', 'repair_instructions', 'status',
      ],
      'properties' => [
        'factuality' => ['type' => 'number'],
        'specificity' => ['type' => 'number'],
        'persuasion' => ['type' => 'number'],
        'genericness' => ['type' => 'number'],
        'brand_voice' => ['type' => 'number'],
        'locale_consistency' => ['type' => 'number'],
        'seo_quality' => ['type' => 'number'],
        'unsupported_claims' => ['type' => 'array', 'items' => ['type' => 'string']],
        'contradictions' => ['type' => 'array', 'items' => ['type' => 'string']],
        'stale_fact_risks' => ['type' => 'array', 'items' => ['type' => 'string']],
        'blocking_failures' => ['type' => 'array', 'items' => ['type' => 'string']],
        'repair_instructions' => ['type' => 'array', 'items' => ['type' => 'string']],
        'status' => ['type' => 'string', 'enum' => ['pass', 'repair', 'blocked']],
      ],
    ];
  }
}
