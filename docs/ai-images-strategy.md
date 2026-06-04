# AI Images Strategy

## Decision

Event image variants must never change factual flyer content. Dates, address, venue,
logos, team names, prices, and visible text are treated as protected content.

## Current Architecture

The default path is deterministic and local:

1. The event thumbnail is loaded from `public/assets/admin/img/event/thumbnail`.
2. `BlurExtendService` creates the target canvas with a blurred background derived
   from the original flyer.
3. The original flyer is composited centered on top.
4. The result is saved as a preview in `public/assets/admin/img/event-ai/{eventId}`.

This path does not call OpenAI and has `cost_estimate = 0.0`.

## Hybrid Mode

`AI_IMAGES_USE_HYBRID_MODE=false` by default.

When enabled, OpenAI is used only to extend the masked background. The original flyer
is composited on top again after the API response. The job validates the protected
center area against the source flyer using `AI_IMAGES_SSIM_THRESHOLD`.

If OpenAI fails or validation fails, the job falls back to `BlurExtendService`.

The hybrid path requires an additive migration that stores generation metadata on
`event_images` and the validation score on `event_ai_generations`.

Before enabling `AI_IMAGES_USE_HYBRID_MODE=true` outside staging, validate one real
flyer against OpenAI and confirm that the mask is interpreted correctly: only the
background outside the protected flyer area should change. If the output is
identical to the source, the mask was likely ignored and the flag must remain off.

## Safety Rules

- Generated variants are previews only.
- Applying a variant remains a manual admin/organizer action.
- The event thumbnail is never overwritten by generation.
- OpenAI must not be used unless `AI_IMAGES_USE_HYBRID_MODE=true`.
- Fallback to blur extend must remain available at all times.
