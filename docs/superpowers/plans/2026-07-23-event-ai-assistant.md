# Event AI Assistant Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans for inline implementation. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the first production-safe version of the organizer AI assistant for flyer extraction, reviewed canonical facts, copy generation, SEO metadata, and usage limits.

**Architecture:** Add focused tables, Eloquent models, one organizer controller, queued jobs, and OpenAI Responses API services using strict JSON payloads. The UI is a compact Bootstrap 4/jQuery panel in event edit that starts jobs, polls status, and applies generated fields only after organizer action.

**Tech Stack:** Laravel 12, PHP 8.2+, MySQL, Eloquent, database queues, Blade, Bootstrap 4, jQuery, OpenAI Responses API through Laravel HTTP client.

---

## File Map

- Create `database/migrations/2026_07_23_000010_create_event_ai_assistant_runs_table.php`
- Create `database/migrations/2026_07_23_000011_create_event_ai_assistant_reviews_table.php`
- Create `database/migrations/2026_07_23_000012_create_event_ai_content_drafts_table.php`
- Create `app/Models/Event/EventAiAssistantRun.php`
- Create `app/Models/Event/EventAiAssistantReview.php`
- Create `app/Models/Event/EventAiContentDraft.php`
- Create `app/Services/OpenAI/EventAiAssistantService.php`
- Create `app/Services/OpenAI/EventAiPromptFactory.php`
- Create `app/Services/EventAi/EventAiUsageLimiter.php`
- Create `app/Services/EventAi/EventFactsBuilder.php`
- Create `app/Jobs/AnalyzeEventFlyerJob.php`
- Create `app/Jobs/GenerateEventContentDraftJob.php`
- Create `app/Http/Controllers/Organizer/EventAiAssistantController.php`
- Create `resources/views/organizer/event/partials/ai-assistant-panel.blade.php`
- Modify `config/openai.php`
- Modify `config/features.php`
- Modify `.env.example`
- Modify `routes/organizer_events.php`
- Modify `resources/views/organizer/event/edit.blade.php`
- Create focused unit and feature tests under `tests/Unit` and `tests/Feature`

## Tasks

- [x] Add focused tests for the usage limiter and per-event/per-organizer caps.
- [x] Add config/env defaults for assistant models, queues, prompt version, and limits.
- [x] Add migrations and Eloquent models.
- [x] Implement the usage limiter.
- [x] Implement prompt factory and OpenAI Responses service using Laravel HTTP fake-compatible calls.
- [x] Implement analysis and content jobs.
- [x] Implement organizer controller routes: start analysis, status, create review, generate draft, apply draft.
- [x] Add the organizer edit panel and jQuery polling/apply behavior.
- [x] Run PHP syntax checks and diff checks. PHPUnit is blocked until Composer dependencies exist locally.

## V1 Defaults

- `AI_EVENT_ASSISTANT_ENABLED=false`
- `AI_EVENT_ASSISTANT_MAX_RUNS_PER_EVENT=2`
- `AI_EVENT_ASSISTANT_MAX_RUNS_PER_ORGANIZER_DAY=10`
- `AI_EVENT_ASSISTANT_MAX_CONTENT_DRAFTS_PER_EVENT=2`
- `AI_EVENT_ASSISTANT_MAX_CONTENT_DRAFTS_PER_ORGANIZER_DAY=10`
- `AI_EVENT_ASSISTANT_MODEL_EXTRACT=gpt-5.6-luna`
- `AI_EVENT_ASSISTANT_MODEL_GENERATE=gpt-5.6-terra`
- `AI_EVENT_ASSISTANT_MODEL_AUDIT=gpt-5.6-terra`
- `AI_EVENT_ASSISTANT_MODEL_ESCALATE=gpt-5.6-sol`
- `AI_EVENT_ASSISTANT_MODERATION_MODEL=omni-moderation-latest`
