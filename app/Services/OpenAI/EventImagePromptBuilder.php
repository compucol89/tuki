<?php

namespace App\Services\OpenAI;

use App\Models\Event;
use App\Models\Event\EventContent;
use App\Models\Language;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class EventImagePromptBuilder
{
    private const ALLOWED_FORMATS = ['square', 'gallery', 'og'];

    public function build(string $format, Event $event): string
    {
        if (!in_array($format, self::ALLOWED_FORMATS, true)) {
            throw new InvalidArgumentException("Invalid format: {$format}");
        }

        $basePrompt = config("openai.prompts.{$format}");
        $context = $this->buildContext($event);

        return trim($basePrompt) . "\n\nContexto del evento:\n" . $context;
    }

    private function buildContext(Event $event): string
    {
        $lines = [];
        $content = null;

        if ($event->relationLoaded('information') && $event->information) {
            $content = $event->information;
        } elseif ($event->getKey() && Schema::hasTable('event_contents')) {
            $defaultLanguageId = Schema::hasTable('languages') && Schema::hasColumn('languages', 'is_default')
                ? Language::query()->where('is_default', 1)->value('id')
                : null;

            $contentQuery = EventContent::query()->where('event_id', $event->getKey());
            if ($defaultLanguageId) {
                $contentQuery->where('language_id', $defaultLanguageId);
            }
            $content = $contentQuery->orderBy('language_id')->first();
        }

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
