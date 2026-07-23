<?php

namespace Tests\Unit\Support;

use App\Support\EventAiDraftPreferences;
use Tests\TestCase;

class EventAiDraftPreferencesTest extends TestCase
{
    public function test_builds_event_brief_from_audience_payload(): void
    {
        $review = (object) [
            'tone' => 'directo_vendedor',
            'intensity' => 'alta_conversion',
            'audience_payload' => [
                'event_brief' => 'Noche de reggaeton viejo en Palermo con promo de acceso y ambiente latino.',
                'locations' => ['argentina', 'caba'],
                'communities' => ['publico_argentino', 'colombianos_en_argentina'],
            ],
        ];

        $preferences = EventAiDraftPreferences::fromReview($review);

        $this->assertSame('directo_vendedor', $preferences['tone']);
        $this->assertSame('alta_conversion', $preferences['intensity']);
        $this->assertSame('Noche de reggaeton viejo en Palermo con promo de acceso y ambiente latino.', $preferences['event_brief']);
        $this->assertSame(['argentina', 'caba'], $preferences['audience']['locations']);
    }

    public function test_uses_description_as_legacy_event_brief_fallback(): void
    {
        $review = (object) [
            'tone' => 'cercano_rioplatense',
            'intensity' => 'equilibrado',
            'audience_payload' => [
                'description' => 'Descripción previa guardada antes de separar event_brief.',
            ],
        ];

        $preferences = EventAiDraftPreferences::fromReview($review);

        $this->assertSame('Descripción previa guardada antes de separar event_brief.', $preferences['event_brief']);
    }
}
