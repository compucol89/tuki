<?php

namespace App\Services\OpenAI\EventAI\Schemas;

/**
 * Schema V3 del copywriter. Compatible con los campos públicos del schema V2
 * (content/social/faq/review_checklist/missing_information) — seo y audit
 * se generan en etapas separadas.
 */
class EventCopySchema
{
  public const NAME = 'event_copy_v3';

  public const VERSION = '2026-08-21-v3';

  public static function toArray(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => ['content', 'social', 'faq', 'review_checklist', 'missing_information'],
      'properties' => [
        'content' => [
          'type' => 'object',
          'additionalProperties' => false,
          'required' => [
            'public_title', 'title_options', 'subtitle', 'short_description',
            'main_description', 'what_you_will_experience',
            'important_information', 'cta', 'alternative_version',
          ],
          'properties' => [
            'public_title' => ['type' => 'string'],
            'title_options' => ['type' => 'array', 'items' => ['type' => 'string']],
            'subtitle' => ['type' => 'string'],
            'short_description' => ['type' => 'string'],
            'main_description' => ['type' => 'string'],
            'what_you_will_experience' => ['type' => 'array', 'items' => ['type' => 'string']],
            'important_information' => ['type' => 'array', 'items' => ['type' => 'string']],
            'cta' => ['type' => 'string'],
            'alternative_version' => ['type' => ['string', 'null']],
          ],
        ],
        'social' => [
          'type' => 'object',
          'additionalProperties' => false,
          'required' => [
            'open_graph_title', 'open_graph_description', 'meta_ad_safe_copy',
            'instagram_caption', 'whatsapp_share_text',
          ],
          'properties' => [
            'open_graph_title' => ['type' => 'string'],
            'open_graph_description' => ['type' => 'string'],
            'meta_ad_safe_copy' => ['type' => 'string'],
            'instagram_caption' => ['type' => 'string'],
            'whatsapp_share_text' => ['type' => 'string'],
          ],
        ],
        'faq' => [
          'type' => 'array',
          'items' => [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['question', 'answer'],
            'properties' => [
              'question' => ['type' => 'string'],
              'answer' => ['type' => 'string'],
            ],
          ],
        ],
        'review_checklist' => [
          'type' => 'array',
          'items' => [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => ['label', 'status', 'note'],
            'properties' => [
              'label' => ['type' => 'string'],
              'status' => ['type' => 'string'],
              'note' => ['type' => 'string'],
            ],
          ],
        ],
        'missing_information' => ['type' => 'array', 'items' => ['type' => 'string']],
      ],
    ];
  }
}
