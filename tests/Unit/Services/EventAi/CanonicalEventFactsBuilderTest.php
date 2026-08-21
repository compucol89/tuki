<?php

namespace Tests\Unit\Services\EventAi;

use App\Services\EventAi\CanonicalEventFacts;
use App\Services\EventAi\CanonicalEventFactsBuilder;
use App\Services\EventAi\EventArchetypes;
use Tests\TestCase;

class CanonicalEventFactsBuilderTest extends TestCase
{
  private function formFacts(): array
  {
    return [
      'event_type' => 'venue',
      'date_type' => 'single',
      'start_date' => '2026-09-15',
      'start_time' => '21:00',
      'end_date' => '2026-09-16',
      'end_time' => '04:00',
      'duration' => null,
      'category' => 'Fiestas',
      'title' => 'Fiesta de prueba',
      'description' => 'Una descripción de prueba',
      'address' => 'Av. Corrientes 1234',
      'city' => 'CABA',
      'state' => 'Buenos Aires',
      'country' => 'Argentina',
      'zip_code' => 'C1043',
      'has_thumbnail' => true,
    ];
  }

  public function test_build_from_temporary_maps_form_facts_with_provenance(): void
  {
    $facts = app(CanonicalEventFactsBuilder::class)->buildFromTemporary($this->formFacts(), null, []);

    $title = $facts->resolve('title');
    $this->assertSame('Fiesta de prueba', $title['value']);
    $this->assertSame(CanonicalEventFacts::SOURCE_FORM, $title['source']);
    $this->assertTrue($title['sensitive']);

    $address = $facts->resolve('address');
    $this->assertSame('Av. Corrientes 1234', $address['value']);
  }

  public function test_build_from_temporary_marks_price_volatile(): void
  {
    $facts = app(CanonicalEventFactsBuilder::class)->buildFromTemporary(
      $this->formFacts(),
      null,
      ['price' => 8000, 'pricing_type' => null],
    );

    $price = $facts->resolve('price');
    $this->assertSame(8000.0, $price['value']);
    $this->assertSame(CanonicalEventFacts::SOURCE_TICKET_DB, $price['source']);
    $this->assertFalse($price['copy_safe']);
    $this->assertTrue($price['sensitive']);
  }

  public function test_build_from_temporary_free_ticket(): void
  {
    $facts = app(CanonicalEventFactsBuilder::class)->buildFromTemporary(
      $this->formFacts(),
      null,
      ['pricing_type' => 'free'],
    );

    $this->assertSame('free', $facts->resolve('pricing_type')['value']);
  }

  public function test_flyer_evidence_is_added_with_low_authority(): void
  {
    $facts = app(CanonicalEventFactsBuilder::class)->buildFromTemporary(
      $this->formFacts(),
      [
        'image_analysis' => [
          'extracted_fields' => [
            ['key' => 'fecha', 'label' => 'Fecha', 'value' => '2026-09-20', 'raw_text' => '20/09', 'confidence' => 0.85, 'sensitive' => true, 'needs_review' => false],
          ],
        ],
      ],
      [],
    );

    $this->assertSame('2026-09-15', $facts->resolve('start_date')['value']);
    $this->assertSame('2026-09-20', $facts->resolve('flyer:fecha')['value']);
    $this->assertSame(CanonicalEventFacts::SOURCE_FLYER, $facts->resolve('flyer:fecha')['source']);
  }

  public function test_flyer_evidence_below_confidence_threshold_is_ignored(): void
  {
    $facts = app(CanonicalEventFactsBuilder::class)->buildFromTemporary(
      $this->formFacts(),
      [
        'image_analysis' => [
          'extracted_fields' => [
            ['key' => 'artista', 'label' => 'Artista', 'value' => 'DJ Dudoso', 'raw_text' => 'DJ?', 'confidence' => 0.3, 'sensitive' => true, 'needs_review' => true],
          ],
        ],
      ],
      [],
    );

    $this->assertNull($facts->resolve('flyer:artista'));
  }

  public function test_archetype_guess_from_category_and_title(): void
  {
    $archetypes = app(EventArchetypes::class);

    $this->assertSame('nightlife', $archetypes->guess('Fiestas', 'Fiesta de cumbia', 'venue'));
    $this->assertSame('concert', $archetypes->guess('Conciertos y Música', 'Banda en vivo', 'venue'));
    $this->assertSame('online', $archetypes->guess('Conferencias', 'Webinar', 'online'));
    $this->assertSame('sports', $archetypes->guess('Deportes', 'Partido de fútbol', 'venue'));
    $this->assertSame('theatre', $archetypes->guess('Teatro y Shows', 'Comedia', 'venue'));
  }

  public function test_archetype_has_few_shot_examples(): void
  {
    $archetypes = app(EventArchetypes::class);

    foreach (array_keys(EventArchetypes::FEW_SHOT) as $archetype) {
      $this->assertNotEmpty($archetypes->example($archetype), "Falta few-shot para {$archetype}");
    }
  }
}
