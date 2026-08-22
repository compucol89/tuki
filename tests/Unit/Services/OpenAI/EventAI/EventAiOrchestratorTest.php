<?php

namespace Tests\Unit\Services\OpenAI\EventAI;

use App\Services\EventAi\CanonicalEventFacts;
use App\Services\EventAi\EventAiDraftPostProcessor;
use App\Services\OpenAI\EventAI\EventAiOrchestrator;
use App\Services\OpenAI\EventAI\Schemas\EventAuditSchema;
use App\Services\OpenAI\EventAI\Schemas\EventCopySchema;
use App\Services\OpenAI\EventAI\Schemas\EventSeoSchema;
use App\Services\OpenAI\EventAI\Schemas\EventStrategySchema;
use App\Services\OpenAI\EventAiAssistantService;
use Mockery;
use Tests\TestCase;

class EventAiOrchestratorTest extends TestCase
{
  private function facts(): CanonicalEventFacts
  {
    $facts = new CanonicalEventFacts();
    $facts->add('title', 'Fiesta de cumbia y reggaetón en Palermo', CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true]);
    $facts->add('city', 'Palermo', CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true]);
    $facts->add('start_time', '23:00', CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true]);
    $facts->add('event_type', 'venue', CanonicalEventFacts::SOURCE_DATABASE);

    return $facts;
  }

  private function strategy(): array
  {
    return [
      'event_archetype' => 'nightlife',
      'core_proposition' => 'Una noche de cumbia y reggaetón para bailar con tu grupo en Palermo.',
      'primary_motivation' => 'Salir a bailar con amigos',
      'secondary_motivations' => ['Pertenencia', 'Energía'],
      'audience_hypotheses' => [['segment' => 'Jóvenes de Palermo', 'supported_by' => ['Categoría fiestas']]],
      'proof_points' => ['DJ en vivo', 'Pista desde temprano'],
      'objections' => [['objection' => 'Lejos', 'answerable' => false, 'evidence' => null]],
      'creative_angles' => [['angle' => 'Nostalgia cumbiera', 'strength' => 0.94], ['angle' => 'Plan de grupo', 'strength' => 0.88]],
      'recommended_angle' => 'Nostalgia cumbiera',
      'tone_profile' => ['tone' => 'cercano_rioplatense', 'intensity' => 'equilibrado', 'language_style' => 'es_ar_voseo'],
      'cta_strategy' => 'Reservá tu entrada anticipada',
      'avoid' => ['clichés de fiesta genéricos'],
    ];
  }

  private function copy(): array
  {
    return [
      'content' => [
        'public_title' => 'Fiesta de cumbia y reggaetón en Palermo',
        'title_options' => ['Fiesta de cumbia y reggaetón en Palermo', 'Sábado de cumbia en Palermo', 'La noche de cumbia que esperabas', 'Cumbia y reggaetón en el corazón de Palermo'],
        'subtitle' => 'Una noche para bailar sin parar',
        'short_description' => 'Cumbia, reggaetón y los clásicos que no pueden faltar, en Palermo.',
        'main_description' => str_repeat('Este sábado, Palermo se prende fuego con la mezcla que más bailás: cumbia, reggaetón y los clásicos que no pueden faltar. ', 5),
        'what_you_will_experience' => ['Pista llena', 'DJ en vivo', 'El mejor ambiente'],
        'important_information' => ['Apertura 23:00', 'Mayores de 18', 'Entrada anticipada'],
        'cta' => 'Reservá tu entrada',
        'alternative_version' => null,
      ],
      'social' => [
        'open_graph_title' => 'Fiesta de cumbia y reggaetón en Palermo',
        'open_graph_description' => 'Este sábado en Palermo: cumbia, reggaetón y los clásicos que más bailás, con entrada anticipada disponible.',
        'meta_ad_safe_copy' => 'Fiesta de cumbia en Palermo',
        'instagram_caption' => 'Este sábado nos vemos en la pista',
        'whatsapp_share_text' => 'Fiesta de cumbia este sábado en Palermo',
      ],
      'faq' => [['question' => '¿A qué hora abre?', 'answer' => 'La apertura de puertas es a las 23:00.']],
      'review_checklist' => array_fill(0, 6, ['label' => 'Revisar', 'status' => 'revisar', 'note' => '']),
      'missing_information' => [],
    ];
  }

  private function seo(?string $title = null): array
  {
    return [
      'seo_title' => $title ?? 'Fiesta de cumbia y reggaetón en Palermo | Entradas',
      'google_short_description' => str_repeat('Fiesta de cumbia y reggaetón en Palermo con entrada anticipada. ', 3),
      'meta_description' => 'Fiesta de cumbia y reggaetón en Palermo. Entradas anticipadas disponibles.',
      'primary_keyword' => 'fiesta cumbia palermo',
      'secondary_keywords' => ['reggaetón palermo'],
      'local_search_variants' => ['fiesta cumbia buenos aires'],
      'tags' => ['fiesta', 'cumbia', 'reggaetón', 'palermo', 'sábado', 'música', 'entradas', 'buenos aires'],
      'suggested_slug' => 'fiesta-cumbia-reggaeton-palermo',
      'image_alt_text' => 'Fiesta de cumbia y reggaetón en Palermo',
      'schema_event_description' => 'Fiesta de cumbia y reggaetón en Palermo.',
      'ai_search_summary' => str_repeat('Fiesta de cumbia y reggaetón en Palermo el sábado, con entrada anticipada disponible. ', 3),
    ];
  }

  private function passAudit(): array
  {
    return [
      'factuality' => 9.9, 'specificity' => 9.2, 'persuasion' => 9.1, 'genericness' => 1.5,
      'brand_voice' => 9.4, 'locale_consistency' => 10, 'seo_quality' => 9,
      'unsupported_claims' => [], 'contradictions' => [], 'stale_fact_risks' => [],
      'blocking_failures' => [], 'repair_instructions' => [], 'status' => 'pass',
    ];
  }

  public function test_generate_runs_pipeline_and_merges_v2_payload(): void
  {
    $assistant = Mockery::mock(EventAiAssistantService::class);

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventStrategySchema::NAME)
      ->andReturn($this->strategy());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventCopySchema::NAME)
      ->andReturn($this->copy());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventSeoSchema::NAME)
      ->andReturn($this->seo());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventAuditSchema::NAME)
      ->andReturn($this->passAudit());

    $orchestrator = new EventAiOrchestrator($assistant, app(EventAiDraftPostProcessor::class));

    $result = $orchestrator->generate($this->facts(), [], 'Fiestas', 'venue');

    $this->assertSame('Fiesta de cumbia y reggaetón en Palermo', $result['content']['public_title']);
    $this->assertArrayHasKey('seo', $result);
    $this->assertSame('passed', $result['audit']['status']);
    $this->assertArrayHasKey('ai_search_summary', $result['seo']);
  }

  public function test_repair_regenerates_seo_before_final_payload(): void
  {
    $assistant = Mockery::mock(EventAiAssistantService::class);
    $fixedCopy = $this->copy();
    $fixedCopy['content']['main_description'] = str_repeat('Copy reparado con datos concretos de Palermo, apertura 23:00, DJ en vivo y pista de cumbia y reggaetón. ', 5);

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventStrategySchema::NAME)
      ->andReturn($this->strategy());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventCopySchema::NAME)
      ->times(2)
      ->andReturn($this->copy(), $fixedCopy);
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventSeoSchema::NAME)
      ->times(2)
      ->andReturn($this->seo('SEO inicial que no debe quedar'), $this->seo('SEO reparado con copy final'));

    $repairAudit = $this->passAudit();
    $repairAudit['status'] = 'repair';
    $repairAudit['blocking_failures'] = ['Descripción genérica'];
    $repairAudit['repair_instructions'] = ['Reescribir main_description con datos específicos del evento'];

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventAuditSchema::NAME)
      ->times(2)
      ->andReturn($repairAudit, $this->passAudit());

    $orchestrator = new EventAiOrchestrator($assistant, app(EventAiDraftPostProcessor::class));

    $result = $orchestrator->generate($this->facts(), [], 'Fiestas', 'venue');

    $this->assertSame('SEO reparado con copy final', $result['seo']['seo_title']);
  }

  public function test_repair_runs_when_audit_returns_repair(): void
  {
    $assistant = Mockery::mock(EventAiAssistantService::class);
    $fixedCopy = $this->copy();

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventStrategySchema::NAME)
      ->andReturn($this->strategy());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventCopySchema::NAME)
      ->times(2)
      ->andReturn($this->copy(), $fixedCopy);
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventSeoSchema::NAME)
      ->twice()
      ->andReturn($this->seo(), $this->seo('Fiesta de cumbia y reggaetón reparada | Entradas'));

    $repairAudit = $this->passAudit();
    $repairAudit['status'] = 'repair';
    $repairAudit['blocking_failures'] = ['Descripción genérica'];
    $repairAudit['repair_instructions'] = ['Reescribir main_description con datos específicos del evento'];

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventAuditSchema::NAME)
      ->times(2)
      ->andReturn($repairAudit, $this->passAudit());

    $orchestrator = new EventAiOrchestrator($assistant, app(EventAiDraftPostProcessor::class));

    $result = $orchestrator->generate($this->facts(), [], 'Fiestas', 'venue');

    $this->assertSame('passed', $result['audit']['status']);
  }

  public function test_generate_throws_when_gates_fail(): void
  {
    $assistant = Mockery::mock(EventAiAssistantService::class);

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventStrategySchema::NAME)
      ->andReturn($this->strategy());

    $badCopy = $this->copy();
    $badCopy['content']['cta'] = 'Comprá tu ticket ahora';

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventCopySchema::NAME)
      ->andReturn($badCopy);
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventSeoSchema::NAME)
      ->andReturn($this->seo());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventAuditSchema::NAME)
      ->andReturn($this->passAudit());

    $orchestrator = new EventAiOrchestrator($assistant, app(EventAiDraftPostProcessor::class));

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessageMatches('/ticket/');

    $orchestrator->generate($this->facts(), [], 'Fiestas', 'venue');
  }

  public function test_generate_throws_when_genericness_gate_fails(): void
  {
    $assistant = Mockery::mock(EventAiAssistantService::class);
    $genericCopy = $this->copy();
    $genericCopy['content']['main_description'] = str_repeat('Una experiencia única e increíble, especial, mágica y sorprendente para vivir una noche única con música, energía y diversión. ', 6);

    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventStrategySchema::NAME)
      ->andReturn($this->strategy());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventCopySchema::NAME)
      ->andReturn($genericCopy);
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventSeoSchema::NAME)
      ->andReturn($this->seo());
    $assistant->shouldReceive('createStructured')
      ->withArgs(fn ($model, $instructions, $input, $name, $schema) => $name === EventAuditSchema::NAME)
      ->andReturn($this->passAudit());

    $orchestrator = new EventAiOrchestrator($assistant, app(EventAiDraftPostProcessor::class));

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessageMatches('/genéric|generic/i');

    $orchestrator->generate($this->facts(), [], 'Fiestas', 'venue');
  }

  protected function tearDown(): void
  {
    Mockery::close();
    parent::tearDown();
  }
}
