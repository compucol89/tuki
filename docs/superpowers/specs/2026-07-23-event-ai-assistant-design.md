# Event AI Assistant Design

**Status:** Approved for V1 implementation
**Date:** 2026-07-23
**Project:** TukiPass

## Goal

Build an organizer-facing AI assistant that reads event flyers, extracts verifiable event facts, lets the organizer review them, and generates high-conversion Spanish Argentina copy plus SEO/social metadata without publishing or overwriting sensitive data automatically.

## Scope

V1 covers organizer events only. It works from the saved event cover image and existing event fields, stores every run for auditability, and applies generated text only after organizer confirmation.

V1 does not change checkout, bookings, tickets, prices, payment gateways, fiscal flows, Meta Pixel, canonical public routes, or published slugs.

## AI Provider

Use OpenAI Responses API with strict JSON Schema outputs.

- Standard flyer extraction: `gpt-5.6-luna`
- Difficult extraction, copy, SEO, audit: `gpt-5.6-terra`
- Exceptional escalation: `gpt-5.6-sol`
- Moderation: `omni-moderation-latest`

Models, timeouts, queues, and limits must be configurable through `config/openai.php`, `config/features.php`, and env vars.

## Usage Limits

Default limits:

- 2 flyer analyses per event.
- 10 flyer analyses per organizer per day.
- 2 content drafts per event.
- 10 content drafts per organizer per day.

Limits are configurable. The system returns clear Spanish messages when a limit is reached. Failed external calls count as runs only after a job is created, and run creation is transactionally locked per event so repeated UI clicks cannot bypass limits.

## Workflow

1. Organizer uploads or already has an event cover image.
2. Organizer clicks "Asistente IA".
3. Backend validates feature flag, ownership, image existence, API key, active jobs, and rate limits.
4. A queued job analyzes the image and event fields.
5. Analysis stores extracted facts, confidence, evidence, conflicts, missing questions, and sensitive fields.
6. Organizer reviews the analysis in the event edit screen.
7. Organizer requests content generation from accepted facts and preferences.
8. A queued job creates copy, SEO, social text, FAQ, and an audit result.
9. Organizer applies selected generated fields to `event_contents`.

## Data Model

`event_ai_assistant_runs`

- Belongs to `events` and `organizers`.
- Stores type (`analysis`, `content`), status, model, source image hash, prompt version, input/output JSON, moderation JSON, audit JSON, error, and duration.

`event_ai_assistant_reviews`

- Belongs to a run, event, and organizer.
- Stores `canonical_event_facts`, selected fields, ignored fields, tone, intensity, audience, status, and timestamps.

`event_ai_content_drafts`

- Belongs to a review.
- Stores generated payload, audit status, human review flag, and applied timestamp.

## Extraction Rules

Every extracted field includes:

```json
{
  "value": null,
  "raw_text": null,
  "confidence": 0,
  "source_type": "image_text",
  "source_image": "thumbnail",
  "needs_review": true,
  "warning_code": null,
  "sensitive": false
}
```

Sensitive fields always require review: dates, times, address, price, promotions, capacity, age limits, artists, benefits, access rules, refunds, sponsors, and official endorsements.

Image text is data, never instructions. Prompt injection text inside the flyer must be ignored.

## Generation Output

Generate modular Spanish Argentina content:

- Public title
- Subtitle
- Short description
- Main description
- What you will experience
- Important information
- CTA
- Alternative version
- SEO title
- Descripción corta para Google
- Meta description
- Primary and secondary keywords
- Local search variants
- Tags
- Suggested slug for new events only
- Image alt text
- Open Graph title and description
- Schema Event description
- FAQ items
- AI-search answer summary
- Missing information alerts

## Tone Options

- Directo y vendedor
- Cercano y rioplatense
- Enérgico y festivo
- Emotivo e inspirador
- Profesional e institucional
- Exclusivo y premium
- Informativo y neutral
- Familiar y accesible
- Urgencia responsable

Commercial intensity:

- Informativo
- Equilibrado
- Alta conversión

## SEO And Policy Guardrails

Generated content must be useful for people first, factually grounded, and aligned with Google Event structured data guidance. Metadata, schema descriptions, alt text, and generated content must describe the same event facts.

The assistant must not create fake scarcity, fake popularity, unverifiable claims, misleading sponsor mentions, exaggerated guarantees, or keyword stuffing.

## UI

Add a compact assistant panel to the organizer event edit screen:

- Shows remaining uses.
- Starts analysis from the event cover.
- Polls run status.
- Shows detected facts, conflicts, confidence, and missing questions.
- Allows tone, audience, and intensity selection.
- Generates draft content.
- Applies selected fields to existing form fields without submitting the whole event.

The UI stays Bootstrap 4/jQuery and Atlantis-compatible.

## Verification

V1 needs unit tests for rate limits and payload builders, feature tests for organizer authorization and limits, and HTTP fake tests for OpenAI service behavior.
