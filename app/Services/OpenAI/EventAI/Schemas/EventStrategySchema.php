<?php

namespace App\Services\OpenAI\EventAI\Schemas;

class EventStrategySchema
{
  public const NAME = 'event_strategy_v1';

  public const VERSION = '2026-08-21-v1';

  public static function toArray(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => [
        'event_archetype', 'core_proposition', 'primary_motivation',
        'secondary_motivations', 'audience_hypotheses', 'proof_points',
        'objections', 'creative_angles', 'recommended_angle',
        'tone_profile', 'cta_strategy', 'avoid',
      ],
      'properties' => [
        'event_archetype' => ['type' => 'string'],
        'core_proposition' => ['type' => 'string'],
        'primary_motivation' => ['type' => 'string'],
        'secondary_motivations' => ['type' => 'array', 'items' => ['type' => 'string']],
        'audience_hypotheses' => [
          'type' => 'array',
          'items' => [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['segment', 'supported_by'],
            'properties' => [
              'segment' => ['type' => 'string'],
              'supported_by' => ['type' => 'array', 'items' => ['type' => 'string']],
            ],
          ],
        ],
        'proof_points' => ['type' => 'array', 'items' => ['type' => 'string']],
        'objections' => [
          'type' => 'array',
          'items' => [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['objection', 'answerable', 'evidence'],
            'properties' => [
              'objection' => ['type' => 'string'],
              'answerable' => ['type' => 'boolean'],
              'evidence' => ['type' => ['string', 'null']],
            ],
          ],
        ],
        'creative_angles' => [
          'type' => 'array',
          'items' => [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['angle', 'strength'],
            'properties' => [
              'angle' => ['type' => 'string'],
              'strength' => ['type' => 'number'],
            ],
          ],
        ],
        'recommended_angle' => ['type' => 'string'],
        'tone_profile' => [
          'type' => 'object',
          'additionalProperties' => false,
          'required' => ['tone', 'intensity', 'language_style'],
          'properties' => [
            'tone' => ['type' => 'string'],
            'intensity' => ['type' => 'string'],
            'language_style' => ['type' => 'string'],
          ],
        ],
        'cta_strategy' => ['type' => 'string'],
        'avoid' => ['type' => 'array', 'items' => ['type' => 'string']],
      ],
    ];
  }
}
