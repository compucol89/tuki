<?php

namespace App\Services\OpenAI;

use App\Exceptions\OpenAiNonRetryableException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class EventAiAssistantService
{
  public function __construct(private EventAiPromptFactory $prompts)
  {
  }

  public function analyzeFlyer(string $imagePath, array $formFacts): array
  {
    return $this->createStructuredResponse(
      config('openai.event_assistant.models.extract', 'gpt-5.6-luna'),
      $this->prompts->extractionInstructions(),
      [
        [
          'role' => 'user',
          'content' => [
            ['type' => 'input_text', 'text' => $this->prompts->extractionPrompt($formFacts)],
            ['type' => 'input_image', 'image_url' => $this->imageDataUrl($imagePath), 'detail' => 'high'],
          ],
        ],
      ],
      'event_flyer_analysis',
      $this->analysisSchema()
    );
  }

  public function generateContent(array $canonicalFacts, array $preferences): array
  {
    return $this->createStructuredResponse(
      config('openai.event_assistant.models.generate', 'gpt-5.6-terra'),
      $this->prompts->generationInstructions(),
      [
        [
          'role' => 'user',
          'content' => [
            ['type' => 'input_text', 'text' => $this->prompts->generationPrompt($canonicalFacts, $preferences)],
          ],
        ],
      ],
      'event_content_generation',
      $this->generationSchema()
    );
  }

  public function moderateImageAndText(string $imagePath, string $text): array
  {
    $apiKey = config('openai.api_key');
    if (empty($apiKey)) {
      throw new OpenAiNonRetryableException('OPENAI_API_KEY is not configured');
    }

    $response = Http::withToken($apiKey)
      ->timeout(config('openai.timeout', 150))
      ->post(config('openai.base_url') . '/moderations', [
        'model' => config('openai.event_assistant.models.moderation', 'omni-moderation-latest'),
        'input' => [
          ['type' => 'text', 'text' => mb_substr($text, 0, 4000)],
          [
            'type' => 'image_url',
            'image_url' => [
              'url' => $this->imageDataUrl($imagePath),
            ],
          ],
        ],
      ]);

    if ($response->failed()) {
      throw $this->apiException('OpenAI moderation failed', $response->status(), $response->json('error.message') ?? $response->body());
    }

    return $response->json() ?? [];
  }

  public function moderateText(string $text): array
  {
    $apiKey = config('openai.api_key');
    if (empty($apiKey)) {
      throw new OpenAiNonRetryableException('OPENAI_API_KEY is not configured');
    }

    $response = Http::withToken($apiKey)
      ->timeout(config('openai.timeout', 150))
      ->post(config('openai.base_url') . '/moderations', [
        'model' => config('openai.event_assistant.models.moderation', 'omni-moderation-latest'),
        'input' => mb_substr($text, 0, 8000),
      ]);

    if ($response->failed()) {
      throw $this->apiException('OpenAI moderation failed', $response->status(), $response->json('error.message') ?? $response->body());
    }

    return $response->json() ?? [];
  }

  private function createStructuredResponse(string $model, string $instructions, array $input, string $schemaName, array $schema): array
  {
    $apiKey = config('openai.api_key');
    if (empty($apiKey)) {
      throw new OpenAiNonRetryableException('OPENAI_API_KEY is not configured');
    }

    $response = Http::withToken($apiKey)
      ->timeout(config('openai.timeout', 150))
      ->post(config('openai.base_url') . '/responses', [
        'model' => $model,
        'store' => (bool) config('openai.event_assistant.store_responses', false),
        'instructions' => $instructions,
        'input' => $input,
        'text' => [
          'format' => [
            'type' => 'json_schema',
            'name' => $schemaName,
            'strict' => true,
            'schema' => $schema,
          ],
        ],
      ]);

    if ($response->failed()) {
      throw $this->apiException('OpenAI response failed', $response->status(), $response->json('error.message') ?? $response->body());
    }

    $json = $this->extractOutputText($response->json() ?? []);
    $decoded = json_decode($json, true);

    if (!is_array($decoded)) {
      throw new OpenAiNonRetryableException('OpenAI returned invalid JSON for event assistant');
    }

    return $decoded;
  }

  private function extractOutputText(array $payload): string
  {
    if (!empty($payload['output_text']) && is_string($payload['output_text'])) {
      return $payload['output_text'];
    }

    foreach (($payload['output'] ?? []) as $item) {
      foreach (($item['content'] ?? []) as $content) {
        if (isset($content['text']) && is_string($content['text'])) {
          return $content['text'];
        }
      }
    }

    throw new OpenAiNonRetryableException('OpenAI returned empty event assistant response');
  }

  private function apiException(string $label, int $status, string $message): RuntimeException
  {
    Log::error($label, ['status' => $status, 'error' => $message]);

    if ($status >= 500 || $status === 429) {
      return new RuntimeException("OpenAI API error ({$status}): {$message}");
    }

    return new OpenAiNonRetryableException("OpenAI API error ({$status}): {$message}");
  }

  private function imageDataUrl(string $path): string
  {
    if (!is_file($path) || !is_readable($path)) {
      throw new OpenAiNonRetryableException('Event image is not readable');
    }

    $mime = mime_content_type($path) ?: 'image/jpeg';
    $bytes = file_get_contents($path);
    if ($bytes === false) {
      throw new OpenAiNonRetryableException('Event image could not be read');
    }

    return 'data:' . $mime . ';base64,' . base64_encode($bytes);
  }

  private function fieldSchema(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => ['key', 'label', 'value', 'raw_text', 'confidence', 'source_type', 'source_image', 'needs_review', 'warning_code', 'sensitive', 'category'],
      'properties' => [
        'key' => ['type' => 'string'],
        'label' => ['type' => 'string'],
        'value' => ['type' => ['string', 'null']],
        'raw_text' => ['type' => ['string', 'null']],
        'confidence' => ['type' => 'number'],
        'source_type' => ['type' => 'string'],
        'source_image' => ['type' => ['string', 'null']],
        'needs_review' => ['type' => 'boolean'],
        'warning_code' => ['type' => ['string', 'null']],
        'sensitive' => ['type' => 'boolean'],
        'category' => ['type' => 'string'],
      ],
    ];
  }

  private function analysisSchema(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => ['summary', 'extracted_fields', 'found_information', 'complementary_information', 'optional_suggestions', 'critical_differences', 'conflicts', 'missing_information', 'sensitive_fields', 'sponsors', 'warnings'],
      'properties' => [
        'summary' => ['type' => 'string'],
        'extracted_fields' => ['type' => 'array', 'items' => $this->fieldSchema()],
        'found_information' => ['type' => 'array', 'items' => ['type' => 'string']],
        'complementary_information' => ['type' => 'array', 'items' => ['type' => 'string']],
        'optional_suggestions' => ['type' => 'array', 'items' => ['type' => 'string']],
        'critical_differences' => ['type' => 'array', 'items' => ['type' => 'string']],
        'conflicts' => ['type' => 'array', 'items' => ['type' => 'string']],
        'missing_information' => ['type' => 'array', 'items' => ['type' => 'string']],
        'sensitive_fields' => ['type' => 'array', 'items' => ['type' => 'string']],
        'sponsors' => ['type' => 'array', 'items' => $this->fieldSchema()],
        'warnings' => ['type' => 'array', 'items' => ['type' => 'string']],
      ],
    ];
  }

  private function generationSchema(): array
  {
    return [
      'type' => 'object',
      'additionalProperties' => false,
      'required' => ['content', 'seo', 'social', 'faq', 'missing_information', 'audit'],
      'properties' => [
        'content' => [
          'type' => 'object',
          'additionalProperties' => false,
          'required' => ['public_title', 'subtitle', 'short_description', 'main_description', 'what_you_will_experience', 'important_information', 'cta', 'alternative_version'],
          'properties' => [
            'public_title' => ['type' => 'string'],
            'subtitle' => ['type' => 'string'],
            'short_description' => ['type' => 'string'],
            'main_description' => ['type' => 'string'],
            'what_you_will_experience' => ['type' => 'array', 'items' => ['type' => 'string']],
            'important_information' => ['type' => 'array', 'items' => ['type' => 'string']],
            'cta' => ['type' => 'string'],
            'alternative_version' => ['type' => 'string'],
          ],
        ],
        'seo' => [
          'type' => 'object',
          'additionalProperties' => false,
          'required' => ['seo_title', 'google_short_description', 'meta_description', 'primary_keyword', 'secondary_keywords', 'local_search_variants', 'tags', 'suggested_slug', 'image_alt_text', 'schema_event_description', 'ai_search_summary'],
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
        ],
        'social' => [
          'type' => 'object',
          'additionalProperties' => false,
          'required' => ['open_graph_title', 'open_graph_description', 'meta_ad_safe_copy', 'instagram_caption', 'whatsapp_share_text'],
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
        'missing_information' => ['type' => 'array', 'items' => ['type' => 'string']],
        'audit' => [
          'type' => 'object',
          'additionalProperties' => false,
          'required' => ['status', 'needs_human_review', 'warnings', 'policy_notes'],
          'properties' => [
            'status' => ['type' => 'string'],
            'needs_human_review' => ['type' => 'boolean'],
            'warnings' => ['type' => 'array', 'items' => ['type' => 'string']],
            'policy_notes' => ['type' => 'array', 'items' => ['type' => 'string']],
          ],
        ],
      ],
    ];
  }
}
