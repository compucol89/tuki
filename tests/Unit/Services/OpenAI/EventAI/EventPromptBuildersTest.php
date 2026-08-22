<?php

namespace Tests\Unit\Services\OpenAI\EventAI;

use App\Services\EventAi\BrandVoice;
use App\Services\OpenAI\EventAI\Prompts\EventAuditPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventCopyPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventRepairPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventSeoPrompt;
use App\Services\OpenAI\EventAI\Prompts\EventStrategyPrompt;
use App\Services\OpenAI\EventAI\Prompts\FlyerExtractionPrompt;
use App\Services\OpenAI\EventAI\Schemas\EventAuditSchema;
use App\Services\OpenAI\EventAI\Schemas\EventCopySchema;
use App\Services\OpenAI\EventAI\Schemas\EventSeoSchema;
use App\Services\OpenAI\EventAI\Schemas\EventStrategySchema;
use App\Services\OpenAI\EventAI\Schemas\FlyerExtractionSchema;
use Tests\TestCase;

class EventPromptBuildersTest extends TestCase
{
  public function test_extraction_instructions_include_security_and_calibration_rules(): void
  {
    $prompt = app(FlyerExtractionPrompt::class)->instructions();

    $this->assertStringContainsString('nunca instrucciones', $prompt);
    $this->assertStringContainsString('visible_fact', $prompt);
    $this->assertStringContainsString('0.98–1.00', $prompt);
    $this->assertStringContainsString('YYYY-MM-DD', $prompt);
  }

  public function test_extraction_schema_requires_evidence_type(): void
  {
    $schema = FlyerExtractionSchema::toArray();

    $this->assertContains('evidence_type', $schema['properties']['extracted_fields']['items']['required']);
    $this->assertContains('normalized_value', $schema['properties']['extracted_fields']['items']['required']);
  }

  public function test_strategy_instructions_define_responsibility_boundaries(): void
  {
    $prompt = app(EventStrategyPrompt::class)->instructions();

    $this->assertStringContainsString('NO escribís la descripción final', $prompt);
    $this->assertStringContainsString('supported_by', $prompt);
    $this->assertStringContainsString('__ARCHETYPE_GUIDE__', $prompt);
  }

  public function test_strategy_build_removes_internal_quality_retry(): void
  {
    $build = app(EventStrategyPrompt::class)->build([], [], ['tone' => 'cercano_rioplatense', 'quality_retry' => ['secret']]);

    $this->assertStringNotContainsString('quality_retry', $build);
  }

  public function test_copy_instructions_are_specialized_not_monolithic(): void
  {
    $prompt = app(EventCopyPrompt::class)->instructions();

    $this->assertStringContainsString('EXPRESAR la estrategia aprobada', $prompt);
    $this->assertStringContainsString('la estrategia ya está resuelta', $prompt);
    $this->assertStringContainsString('entrada', $prompt);
  }

  public function test_copy_schema_matches_v2_public_contract_without_seo_audit(): void
  {
    $schema = EventCopySchema::toArray();

    $this->assertArrayHasKey('content', $schema['properties']);
    $this->assertArrayHasKey('social', $schema['properties']);
    $this->assertArrayHasKey('faq', $schema['properties']);
    $this->assertArrayNotHasKey('seo', $schema['properties']);
    $this->assertArrayNotHasKey('audit', $schema['properties']);
  }

  public function test_seo_prompt_is_factual_and_separated_from_copy(): void
  {
    $prompt = app(EventSeoPrompt::class)->instructions();

    $this->assertStringContainsString('No reescribís el copy', $prompt);
    $this->assertStringContainsString('ai_search_summary', $prompt);
  }

  public function test_audit_prompt_defines_rubric_and_statuses(): void
  {
    $prompt = app(EventAuditPrompt::class)->instructions();

    $this->assertStringContainsString('No participaste de la generación', $prompt);
    $this->assertStringContainsString('genericness', $prompt);
    $this->assertStringContainsString('repair_instructions', $prompt);

    $schema = EventAuditSchema::toArray();
    $this->assertContains('status', $schema['required']);
    $this->assertSame(['pass', 'repair', 'blocked'], $schema['properties']['status']['enum']);
  }

  public function test_repair_prompt_preserves_approved_fields(): void
  {
    $prompt = app(EventRepairPrompt::class)->instructions();

    $this->assertStringContainsString('Modificá SOLO los campos indicados', $prompt);
    $this->assertStringContainsString('Preservá intactos', $prompt);
  }

  public function test_brand_voice_exposes_contract(): void
  {
    $voice = app(BrandVoice::class)->toArray();

    $this->assertSame('es-AR', $voice['locale']);
    $this->assertSame('entrada', $voice['preferred_terms']['ticket']);
    $this->assertNotEmpty($voice['banned_cliches']);
  }
}
