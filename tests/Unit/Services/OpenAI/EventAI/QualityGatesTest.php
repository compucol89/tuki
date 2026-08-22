<?php

namespace Tests\Unit\Services\OpenAI\EventAI;

use App\Services\EventAi\CanonicalEventFacts;
use App\Services\OpenAI\EventAI\Quality\EventContentGenericnessGate;
use App\Services\OpenAI\EventAI\Quality\EventContentPolicyGate;
use App\Services\OpenAI\EventAI\Quality\EventContentQualityGate;
use Tests\TestCase;

class QualityGatesTest extends TestCase
{
  private function validPayload(): array
  {
    return [
      'content' => [
        'public_title' => 'Fiesta de cumbia y reggaetón en Palermo',
        'title_options' => ['Fiesta de cumbia y reggaetón en Palermo', 'La noche de cumbia que esperabas en Palermo', 'Sábado de cumbia en el corazón de Palermo', 'Cumbia, reggaetón y los clásicos en Palermo'],
        'subtitle' => 'Una noche para bailar sin parar',
        'short_description' => 'Cumbia, reggaetón y los clásicos que no pueden faltar, en el corazón de Palermo.',
        'main_description' => str_repeat('Este sábado, Palermo se prende fuego con la mezcla que más bailás: cumbia, reggaetón y los clásicos. ', 5) . 'Entrada anticipada disponible.',
        'what_you_will_experience' => ['Pista llena desde temprano', 'DJ en vivo', 'El mejor ambiente con tu grupo'],
        'important_information' => ['Apertura de puertas 23:00', 'Acceso por la entrada principal', 'Mayores de 18 años'],
        'cta' => 'Reservá tu entrada',
        'alternative_version' => null,
      ],
      'social' => [
        'open_graph_title' => 'Fiesta de cumbia y reggaetón en Palermo',
        'open_graph_description' => 'Este sábado en Palermo: cumbia, reggaetón y los clásicos que más bailás. Reservá tu entrada anticipada.',
        'meta_ad_safe_copy' => 'Fiesta de cumbia en Palermo',
        'instagram_caption' => 'Este sábado nos vemos en la pista',
        'whatsapp_share_text' => 'Fiesta de cumbia este sábado en Palermo',
      ],
      'seo' => [
        'seo_title' => 'Fiesta de cumbia y reggaetón en Palermo | Entradas',
        'google_short_description' => str_repeat('Fiesta de cumbia y reggaetón en Palermo con entrada anticipada. ', 3),
        'meta_description' => 'Fiesta de cumbia y reggaetón en Palermo. Entradas anticipadas disponibles para el sábado.',
        'primary_keyword' => 'fiesta cumbia palermo',
        'secondary_keywords' => ['reggaetón palermo', 'fiesta sábado palermo'],
        'local_search_variants' => ['fiesta cumbia buenos aires'],
        'tags' => ['fiesta', 'cumbia', 'reggaetón', 'palermo', 'sábado', 'música', 'entradas', 'buenos aires'],
        'suggested_slug' => 'fiesta-cumbia-reggaeton-palermo',
        'image_alt_text' => 'Fiesta de cumbia y reggaetón en Palermo',
        'schema_event_description' => 'Fiesta de cumbia y reggaetón en Palermo.',
        'ai_search_summary' => str_repeat('Fiesta de cumbia y reggaetón en Palermo el sábado, con entrada anticipada disponible. ', 3),
      ],
      'faq' => [
        ['question' => '¿A qué hora abre la puerta?', 'answer' => 'La apertura de puertas es a las 23:00.'],
      ],
      'review_checklist' => [
        ['label' => 'Título', 'status' => 'revisar', 'note' => ''],
        ['label' => 'Fecha y horario', 'status' => 'revisar', 'note' => ''],
        ['label' => 'Dirección', 'status' => 'revisar', 'note' => ''],
        ['label' => 'Acceso o precio', 'status' => 'revisar', 'note' => ''],
        ['label' => 'Descripción', 'status' => 'revisar', 'note' => ''],
        ['label' => 'SEO y Google', 'status' => 'revisar', 'note' => ''],
      ],
      'missing_information' => [],
    ];
  }

  public function test_quality_gate_passes_valid_payload(): void
  {
    $this->assertSame([], app(EventContentQualityGate::class)->failures($this->validPayload()));
  }

  public function test_quality_gate_detects_ticket_terminology(): void
  {
    $payload = $this->validPayload();
    $payload['content']['cta'] = 'Comprá tu ticket ahora';

    $failures = app(EventContentQualityGate::class)->failures($payload);

    $this->assertNotEmpty($failures);
    $this->assertStringContainsString('ticket', implode(' ', $failures));
  }

  public function test_quality_gate_detects_short_title(): void
  {
    $payload = $this->validPayload();
    $payload['content']['public_title'] = 'Fiesta';

    $failures = app(EventContentQualityGate::class)->failures($payload);

    $this->assertContains('El título público es demasiado corto o genérico.', $failures);
  }

  public function test_policy_gate_blocks_false_scarcity_without_evidence(): void
  {
    $payload = $this->validPayload();
    $payload['content']['main_description'] = 'Últimas entradas disponibles, se está agotando todo.';

    $failures = app(EventContentPolicyGate::class)->failures($payload, new CanonicalEventFacts());

    $this->assertContains('Falsa escasez sin evidencia canónica.', $failures);
  }

  public function test_policy_gate_blocks_internal_note_in_faq(): void
  {
    $payload = $this->validPayload();
    $payload['faq'][] = ['question' => '¿Cuál es el precio?', 'answer' => 'El precio no fue informado.'];

    $failures = app(EventContentPolicyGate::class)->failures($payload, new CanonicalEventFacts());

    $this->assertNotEmpty($failures);
  }

  public function test_policy_gate_blocks_volatile_price_in_copy(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('price', 15000, CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true, 'copy_safe' => false]);

    $payload = $this->validPayload();
    $payload['content']['main_description'] = 'Entrada general a 15000 pesos por persona.';

    $failures = app(EventContentPolicyGate::class)->failures($payload, $facts);

    $this->assertNotEmpty($failures);
    $this->assertStringContainsString('Precio volátil', implode(' ', $failures));
  }

  public function test_policy_gate_blocks_gratis_when_not_free(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('pricing_type', 'normal', CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true]);

    $payload = $this->validPayload();
    $payload['content']['short_description'] = 'Entrada gratis para todos.';

    $failures = app(EventContentPolicyGate::class)->failures($payload, $facts);

    $this->assertNotEmpty($failures);
  }

  public function test_genericness_gate_scores_generic_copy_high(): void
  {
    $payload = $this->validPayload();
    $payload['content']['main_description'] = 'Prepárate para una experiencia única e increíble. Una noche inolvidable con música, energía y diversión. No te lo puedes perder.';

    $result = app(EventContentGenericnessGate::class)->score($payload, new CanonicalEventFacts());

    $this->assertGreaterThan(3, $result['score']);
    $this->assertNotEmpty($result['signals']);
  }

  public function test_genericness_gate_scores_specific_copy_low(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('title', 'Fiesta de cumbia y reggaetón en Palermo', CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true]);
    $facts->add('city', 'Palermo', CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true]);
    $facts->add('start_time', '23:00', CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true]);

    $payload = $this->validPayload();

    $result = app(EventContentGenericnessGate::class)->score($payload, $facts);

    $this->assertLessThanOrEqual(3, $result['score']);
    $this->assertGreaterThanOrEqual(2, $result['fact_anchors']);
  }
}
