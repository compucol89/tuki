<?php

namespace App\Services\OpenAI;

use App\Models\Event;
use InvalidArgumentException;
use LogicException;

class EventImagePromptBuilder
{
    private const ALLOWED_FORMATS = ['square', 'gallery', 'og'];

    public function build(string $format, Event $event): string
    {
        if (!in_array($format, self::ALLOWED_FORMATS, true)) {
            throw new InvalidArgumentException("Invalid format: {$format}");
        }

        if (!$event->relationLoaded('information')) {
            throw new LogicException(
                'Event::information must be eager-loaded before calling build(). '
                . 'Use $event->load("information") or ->with("information") in the caller.'
            );
        }

        $basePrompt = config("openai.prompts.{$format}");
        $context = $this->buildContext($event);

        return trim($basePrompt) . "\n\nContexto del evento:\n" . $context;
    }

    private function buildContext(Event $event): string
    {
        $content = $event->information;
        $lines = [];

        if ($content) {
            if (!empty($content->title)) {
                $lines[] = "- Nombre: {$content->title}";
            }
            if (!empty($content->city)) {
                $lines[] = "- Ciudad: {$content->city}";
            }
            if (!empty($content->event_category_id)) {
                $lines[] = "- Categoría ID: {$content->event_category_id}";
            }
        }

        if (!empty($event->start_date)) {
            $lines[] = "- Fecha: {$event->start_date}";
        }

        return $lines ? implode("\n", $lines) : "- (sin contexto adicional)";
    }
}
