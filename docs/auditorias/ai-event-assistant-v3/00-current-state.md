# 00 — Estado actual del AI Event Assistant (V2)

Fecha: 2026-08-21 · Fuente: inspección directa del repo (solo lectura).

## Leyenda
- ✅ CONFIRMADO — verificado contra código
- 🟡 INFERIDO — deducido, no verificado en runtime
- ❌ NO ENCONTRADO — buscado y ausente
- ⚠️ DISCREPANCIA — inconsistencia entre piezas

## Servicios centrales

| Pieza | Archivo | Estado |
|---|---|---|
| Fábrica de prompts | `app/Services/OpenAI/EventAiPromptFactory.php` (452 líneas) | ✅ 5 métodos: `extractionInstructions`, `extractionPrompt`, `generationInstructions`, `generationPrompt` (PROMPT MAESTRO, 37 secciones), más moderación delegada al servicio |
| Cliente OpenAI | `app/Services/OpenAI/EventAiAssistantService.php` (321 líneas) | ✅ Responses API `/responses`, `json_schema` estricto. Métodos: `analyzeFlyer`, `generateContent`, `moderateImageAndText`, `moderateText` |
| Facts builder | `app/Services/EventAi/EventFactsBuilder.php` (82 líneas) | ✅ `fromEvent()` (formFacts) y `canonicalFromAnalysis()` (source_priority, form_facts, image_analysis, confirmed_fields/ignored_fields VACÍOS hardcodeados, locale es-AR fijo) |
| Preferencias | `app/Support/EventAiDraftPreferences.php` (21 líneas) | ✅ tone, intensity, event_brief, audience, locale es-AR fijo, timezone |
| Post-procesador | `app/Services/EventAi/EventAiDraftPostProcessor.php` (123 líneas) | ✅ `sanitize()` + `filterFaq()` + `BANNED_PATTERN` (regex `/iu` de frases "no informado/consultar con el organizador/…") + fallbacks deterministas |
| Limiter | `app/Services/EventAi/EventAiUsageLimiter.php` (99 líneas) | ✅ 2 análisis/evento, 10/día; 2 drafts/evento, 10/día; excluye runs de admin |
| Controlador | `app/Http/Controllers/Organizer/EventAiAssistantController.php` (1209 líneas) | ✅ flujo temporal (creación) + flujo por evento (edición), quality gate 2 intentos hardcodeados (L783), `strengthenGeneratedDraft` (L748), `healStuckAiRuns` |
| Jobs | `app/Jobs/AnalyzeEventFlyerJob.php`, `GenerateEventContentDraftJob.php` | ✅ cola `ai-content`, tries 2, backoff 90, timeout 300 |

## Modelos y configuración

| Clave config | Default | Uso real |
|---|---|---|
| `openai.event_assistant.models.extract` | `gpt-5.6-luna` | ✅ `analyzeFlyer` + registro del run de análisis |
| `openai.event_assistant.models.generate` | `gpt-5.6-terra` | ✅ `generateContent` + registro del run de draft |
| `openai.event_assistant.models.moderation` | `omni-moderation-latest` | ✅ pre (imagen+texto) y post (texto generado) |
| `openai.event_assistant.models.audit` | `gpt-5.6-terra` | ❌ NO USADO como llamada separada. El `audit` sale del MISMO schema de generación (auto-auditoría) |
| `openai.event_assistant.models.escalate` | `gpt-5.6-sol` | ❌ NO USADO en ningún lado |
| `openai.event_assistant.limits.max_repair_attempts` | 1 | ❌ NO USADO (el gate hardcodea 2 intentos) |
| `openai.event_assistant.prompt_version` | `2026-07-23-v2` | ✅ guardado en cada run |
| `openai.base_url` | `https://api.openai.com/v1` | ✅ hardcodeado (sin env) |

⚠️ DISCREPANCIA cuota temporal: `config/openai.php` default 6 · controlador L613 default 2 · `.env.example` = 2.

## Base de datos (3 tablas)

- `event_ai_assistant_runs`: type, status, model, prompt_version, source_image_path/hash, input/output/moderation/audit_payload (JSON), duration_ms, error_message.
- `event_ai_assistant_reviews`: run_id, canonical_event_facts JSON, accepted/ignored_fields JSON, audience_payload, tone, intensity, status.
- `event_ai_content_drafts`: review_id, run_id, generated_payload, audit_payload, audit_status (string 191, migración `2026_08_07_000001`), needs_human_review, applied_at.

## Tests existentes (PHPUnit 11, no Pest)

- ✅ `EventAiUsageLimiterTest` (4), `EventAiDraftPostProcessorTest` (8), `EventAiDraftPreferencesTest` (2), `AnalyzeEventFlyerJobTest` (2), `EventAiContentDraftTest` (3)
- ❌ NO EXISTEN: tests del controller, del servicio assistant (HTTP fakes — pedidos por el spec V2 L155 y no implementados), de `EventFactsBuilder`, de `EventAiPromptFactory`, de `GenerateEventContentDraftJob`
- `phpunit.xml`: SQLite in-memory, queue sync, `OPENAI_API_KEY=sk-test-fake`

## Duplicaciones detectadas

1. `canonicalFactValue()` duplicada: controller L858–876 ↔ PostProcessor L89–109 (idéntica).
2. `temporaryCanonicalFacts()` (controller L682) duplica `EventFactsBuilder::canonicalFromAnalysis()`.
3. `temporaryFormFacts()` (controller L646) duplica `EventFactsBuilder::fromEvent()`.
4. `confirmed_fields`/`ignored_fields` hardcodeados vacíos en ambos builders.

## Colas e infra

- Cola `ai-content` definida solo en `config/openai.php`; `QUEUE_CONNECTION=sync` local.
- Docker compose NO tiene worker (`php artisan queue:work`).
- No existen comandos artisan relacionados con IA.

## Espec V2

`docs/superpowers/specs/2026-07-23-event-ai-assistant-design.md` — aprobada. Lo implementado coincide con el spec salvo: escalate ❌, auditor independiente ❌ (auto-auditoría), HTTP fake tests ❌, repair attempts configurables ❌.

## Feature flags actuales

`config/features.php`: `ai_images_enabled` (false) y `event_ai_assistant_enabled` (false). No hay flag V2/V3.
