<?php

namespace Tests\Unit\Services\OpenAI;

use App\Models\Event;
use App\Models\Event\EventContent;
use App\Services\OpenAI\EventImagePromptBuilder;
use Tests\TestCase;

class EventImagePromptBuilderTest extends TestCase
{
    private EventImagePromptBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new EventImagePromptBuilder();
    }

    public function test_build_returns_base_prompt_for_square(): void
    {
        $event = $this->makeEvent();
        $event->setRelation('information', null);
        $prompt = $this->builder->build('square', $event);
        $this->assertStringContainsString('cuadrado 1:1', $prompt);
        $this->assertStringContainsString('1024x1024', $prompt);
    }

    public function test_build_returns_base_prompt_for_gallery(): void
    {
        $event = $this->makeEvent();
        $event->setRelation('information', null);
        $prompt = $this->builder->build('gallery', $event);
        $this->assertStringContainsString('landscape 3:2', $prompt);
        $this->assertStringContainsString('1536x1024', $prompt);
    }

    public function test_build_returns_base_prompt_for_og(): void
    {
        $event = $this->makeEvent();
        $event->setRelation('information', null);
        $prompt = $this->builder->build('og', $event);
        $this->assertStringContainsString('Open Graph', $prompt);
        $this->assertStringContainsString('1536x1024', $prompt);
    }

    public function test_build_includes_event_context_from_information_relation(): void
    {
        $event = $this->makeEvent();
        $event->setRelation('information', new EventContent([
            'title' => 'Festival de Jazz',
            'city' => 'Buenos Aires',
            'event_category_id' => 5,
        ]));

        $prompt = $this->builder->build('square', $event);

        $this->assertStringContainsString('Festival de Jazz', $prompt);
        $this->assertStringContainsString('Buenos Aires', $prompt);
        $this->assertStringContainsString('Categoría', $prompt);
    }

    public function test_build_handles_null_information_relation_gracefully(): void
    {
        $event = $this->makeEvent();
        $event->setRelation('information', null);

        $prompt = $this->builder->build('square', $event);

        $this->assertIsString($prompt);
        $this->assertNotEmpty($prompt);
        $this->assertStringContainsString('sin contexto adicional', $prompt);
    }

    public function test_build_does_not_read_nonexistent_event_title_attribute(): void
    {
        $event = $this->makeEvent();
        $event->setRelation('information', null);
        $event->title = 'This should NOT appear in prompt';

        $prompt = $this->builder->build('square', $event);

        $this->assertStringNotContainsString('This should NOT appear', $prompt);
    }

    public function test_build_throws_for_invalid_format(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->builder->build('invalid_format', $this->makeEvent());
    }

    public function test_build_throws_logic_exception_when_information_not_loaded(): void
    {
        $event = $this->makeEvent();
        $event->id = 1;
        $event->organizer_id = 1;

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Event::information must be eager-loaded');

        $this->builder->build('square', $event);
    }

    private function makeEvent(): Event
    {
        $event = new Event();
        $event->id = 1;
        $event->organizer_id = 1;
        return $event;
    }
}
