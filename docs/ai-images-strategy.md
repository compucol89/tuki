# AI Images Strategy

## Decision

Event image variants must never change factual flyer content. Dates, address, venue,
logos, team names, prices, and visible text are treated as protected content.

## Current Architecture

The default path is deterministic and local. This is the smart-crop mode
(`AI_IMAGES_SMART_CROP_MODE=true` by default):

1. The event thumbnail is loaded from `public/assets/admin/img/event/thumbnail`.
2. `BlurExtendService` creates the target canvas with a blurred background derived
   from the original flyer.
3. `SmartFlyerPlacementService` places the original flyer on top. Square outputs
   use a full contain placement. Landscape outputs (`gallery` and `og`) use a
   conservative crop scale so the flyer occupies more of the canvas and leaves
   less invented margin.
4. The result is saved as a preview in `public/assets/admin/img/event-ai/{eventId}`.

This path does not call OpenAI and has `cost_estimate = 0.0`.

## Hybrid Mode

`AI_IMAGES_USE_HYBRID_MODE=false` by default.

When `AI_IMAGES_SMART_CROP_MODE=false` and `AI_IMAGES_USE_HYBRID_MODE=true`, OpenAI
is used only to extend the masked background. The original flyer is composited on
top again after the API response. The job validates the protected center area
against the source flyer using `AI_IMAGES_SSIM_THRESHOLD`.

If OpenAI fails or validation fails, the job falls back to `BlurExtendService`.

The hybrid path requires an additive migration that stores generation metadata on
`event_images` and the validation score on `event_ai_generations`.

After OpenAI returns the extended background, PHP/GD composites the original flyer
again and applies a local blend layer around the flyer boundary. This blend uses
edge colors from the flyer, a subtle shadow, and a soft outer transition so the
center stays unchanged while the visible seam is reduced.

Before enabling `AI_IMAGES_USE_HYBRID_MODE=true` outside staging, validate one real
flyer against OpenAI and confirm that the mask is interpreted correctly: only the
background outside the protected flyer area should change. If the output is
identical to the source, the mask was likely ignored and the flag must remain off.

## Fase 3A: Mask Alpha Channel

The first hybrid production test preserved the flyer content but produced poor
background extensions with duplicated players near the sides. The likely cause was
the legacy opaque mask: white outside the flyer and black over the flyer, without
an alpha channel.

Fase 3A changes the hybrid mask to an alpha PNG:

- Outside the flyer: alpha `127`, editable background area.
- Over the flyer: alpha `0`, protected original area.
- PNG masks are saved with `imagesavealpha($mask, true)`.
- `input_fidelity` is omitted from background-extension requests because the
  configured image model handles input fidelity automatically.

Rollback is immediate with `AI_IMAGES_USE_ALPHA_MASK=false`, which restores the
previous opaque mask without changing the deterministic blur fallback.

Manual staging check:

1. Use a flyer with visible people, date, venue, logo, and address.
2. Set `AI_IMAGES_USE_HYBRID_MODE=true` and `AI_IMAGES_USE_ALPHA_MASK=true`.
3. Generate one variant.
4. Confirm the flyer center is unchanged and the extended background does not add
   duplicated players or factual text.
5. If the output is worse, set `AI_IMAGES_USE_ALPHA_MASK=false` and restart config
   cache/workers.

## Safety Rules

- Generated variants are previews only.
- Applying a variant remains a manual admin/organizer action.
- The event thumbnail is never overwritten by generation.
- OpenAI must not be used unless `AI_IMAGES_SMART_CROP_MODE=false` and
  `AI_IMAGES_USE_HYBRID_MODE=true`.
- Fallback to blur extend must remain available at all times.
