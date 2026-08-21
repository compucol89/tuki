<?php

namespace Tests\Unit\Services\EventAi;

use App\Services\EventAi\CanonicalEventFacts;
use PHPUnit\Framework\TestCase;

class CanonicalEventFactsTest extends TestCase
{
  public function test_ticket_database_beats_flyer_for_price(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('price', 15000, CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true, 'copy_safe' => false]);
    $facts->add('price', 8000, CanonicalEventFacts::SOURCE_FLYER, ['sensitive' => true, 'confidence' => 0.7]);

    $this->assertSame(15000, $facts->resolve('price')['value']);
    $this->assertTrue($facts->hasConflict('price'));
  }

  public function test_flyer_only_fills_keys_absent_from_form(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('start_date', '2026-09-15', CanonicalEventFacts::SOURCE_FORM, ['sensitive' => true]);
    $facts->add('start_date', '2026-09-16', CanonicalEventFacts::SOURCE_FLYER, ['sensitive' => true, 'confidence' => 0.9]);

    $this->assertSame('2026-09-15', $facts->resolve('start_date')['value']);
  }

  public function test_low_confidence_inference_is_not_verified(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('artists', 'DJ X', CanonicalEventFacts::SOURCE_AI_INFERENCE, ['confidence' => 0.4]);

    $fact = $facts->resolve('artists');

    $this->assertFalse($fact['verified']);
    $this->assertFalse($fact['copy_safe']);
    $this->assertSame(0.4, $fact['confidence']);
  }

  public function test_empty_values_are_ignored(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('title', '', CanonicalEventFacts::SOURCE_FORM);
    $facts->add('city', null, CanonicalEventFacts::SOURCE_FORM);
    $facts->add('tags', [], CanonicalEventFacts::SOURCE_FLYER);

    $this->assertSame([], $facts->all());
  }

  public function test_same_source_conflict_keeps_first_and_records_note(): void
  {
    $facts = new CanonicalEventFacts();
    $facts->add('venue', 'Club A', CanonicalEventFacts::SOURCE_FLYER, ['confidence' => 0.8]);
    $facts->add('venue', 'Club B', CanonicalEventFacts::SOURCE_FLYER, ['confidence' => 0.8]);

    $this->assertSame('Club A', $facts->resolve('venue')['value']);
    $this->assertSame('same_source_conflict', $facts->conflicts()['venue']['note']);
  }

  public function test_to_array_exposes_fact_contract_fields(): void
  {
    $facts = new CanonicalEventFacts('es-AR', 'America/Argentina/Buenos_Aires');
    $facts->add('price', 5000, CanonicalEventFacts::SOURCE_TICKET_DB, ['sensitive' => true, 'copy_safe' => false]);

    $array = $facts->toArray();

    $this->assertSame('es-AR', $array['locale']);
    $this->assertArrayHasKey('value', $array['facts']['price']);
    $this->assertArrayHasKey('source', $array['facts']['price']);
    $this->assertArrayHasKey('copy_safe', $array['facts']['price']);
    $this->assertFalse($array['facts']['price']['copy_safe']);
  }
}
