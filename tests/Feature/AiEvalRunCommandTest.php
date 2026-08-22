<?php

namespace Tests\Feature;

use App\Services\OpenAI\EventAI\EventAiOrchestrator;
use App\Services\OpenAI\EventAiAssistantService;
use Illuminate\Support\Facades\File;
use Mockery;
use Tests\TestCase;

class AiEvalRunCommandTest extends TestCase
{
  private string $bundle = 'test-v2-command';

  protected function setUp(): void
  {
    parent::setUp();

    File::deleteDirectory(base_path("tests/AI/baseline/{$this->bundle}"));
    config(['openai.api_key' => 'test-key']);
  }

  public function test_capture_pipeline_v2_uses_legacy_generator_and_records_metadata(): void
  {
    $assistant = Mockery::mock(EventAiAssistantService::class);
    $assistant->shouldReceive('generateContent')
      ->once()
      ->withArgs(function (array $canonicalFacts, array $preferences): bool {
        return isset($canonicalFacts['form_facts'])
          && array_key_exists('image_analysis', $canonicalFacts)
          && array_key_exists('ticket_facts', $canonicalFacts)
          && is_array($preferences);
      })
      ->andReturn($this->validPayload('Título legacy desde V2'));

    $orchestrator = Mockery::mock(EventAiOrchestrator::class);
    $orchestrator->shouldReceive('generate')->never();

    $this->app->instance(EventAiAssistantService::class, $assistant);
    $this->app->instance(EventAiOrchestrator::class, $orchestrator);

    $this->artisan('ai:eval-run', [
      '--capture' => true,
      '--pipeline' => 'v2',
      '--bundle' => $this->bundle,
      '--limit' => 1,
    ])->assertExitCode(0);

    $captures = glob(base_path("tests/AI/baseline/{$this->bundle}/*.json")) ?: [];
    $this->assertCount(1, $captures);

    $capture = json_decode((string) File::get($captures[0]), true);
    $this->assertSame('v2', $capture['meta']['pipeline']);
    $this->assertSame($this->bundle, $capture['meta']['bundle']);
    $this->assertSame('Título legacy desde V2', $capture['output']['content']['public_title']);
  }

  private function validPayload(string $title): array
  {
    return [
      'content' => [
        'public_title' => $title,
        'title_options' => [$title, "{$title} opción 2", "{$title} opción 3", "{$title} opción 4"],
        'subtitle' => 'Una propuesta clara para reservar entradas',
        'short_description' => 'Descripción breve y concreta del evento para publicar.',
        'main_description' => str_repeat('Descripción concreta del evento con datos de fecha, lugar, acceso y propuesta para reservar entradas. ', 5),
        'what_you_will_experience' => ['Música en vivo', 'Acceso organizado', 'Ambiente local'],
        'important_information' => ['Apertura 23:00', 'Entrada anticipada', 'Revisar datos antes de publicar'],
        'cta' => 'Reservá tu entrada',
      ],
      'seo' => [
        'seo_title' => "{$title} | Entradas",
        'google_short_description' => str_repeat('Evento en TukiPass con entradas disponibles para reservar online. ', 3),
        'meta_description' => 'Evento en TukiPass con entradas disponibles para reservar online.',
        'tags' => ['evento', 'entradas', 'tukipass', 'argentina', 'reservas', 'música', 'agenda', 'salida'],
        'ai_search_summary' => str_repeat('Evento publicado en TukiPass con datos revisables para reservar entradas online. ', 3),
      ],
      'social' => [
        'open_graph_title' => $title,
        'open_graph_description' => 'Evento en TukiPass con información clara para compartir y reservar entradas online.',
      ],
      'faq' => [
        ['question' => '¿Cómo reservo?', 'answer' => 'Podés reservar tu entrada desde TukiPass.'],
      ],
      'review_checklist' => array_fill(0, 6, ['label' => 'Revisar', 'status' => 'revisar', 'note' => '']),
    ];
  }

  protected function tearDown(): void
  {
    File::deleteDirectory(base_path("tests/AI/baseline/{$this->bundle}"));
    Mockery::close();

    parent::tearDown();
  }
}
