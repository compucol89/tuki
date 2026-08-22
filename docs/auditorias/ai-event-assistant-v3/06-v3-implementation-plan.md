# 06 — Plan de implementación V3 (archivo por archivo)

Principio rector: **V2 intacto y activo por defecto. V3 detrás de `AI_EVENT_ASSISTANT_V3_ENABLED=false`. Rollback = apagar la flag.**

## Orden de ejecución

1. Baseline/evals (Fase 1) → 2. Facts V3 (Fase 2) → 3. Extraction V3 (Fase 3) → 4. Strategist (Fase 4) → 5. Copy V3 (Fase 5) → 6. SEO V1 (Fase 6) → 7. Auditor + Repair + Escalation (Fases 7-9) → 8. Gates (Fase 10) → 9. Orquestador + flag + integración (Fase 12/18) → 10. Docs finales.

## Archivos nuevos

| Archivo | Responsabilidad |
|---|---|
| `app/Services/EventAi/CanonicalEventFacts.php` | Contrato V3 de hechos: value/source/confidence/verified/sensitive/copy_safe/updated_at + resolutor determinista por prioridad de fuente |
| `app/Services/EventAi/CanonicalEventFactsBuilder.php` | Construye desde Event (tickets, venue, form) + análisis de flyer |
| `app/Services/EventAi/PlatformContext.php` | Marca/mercado/moneda/rol/legal (reglas, no boilerplate) |
| `app/Services/EventAi/BrandVoice.php` | Voz canónica es-AR: terminología, gramática, clichés prohibidos |
| `app/Services/EventAi/EventArchetypes.php` | Taxonomía editorial + few-shot por arquetipo |
| `app/Services/OpenAI/EventAI/Prompts/FlyerExtractionPrompt.php` | Extracción V3: campos por evidencia, normalización, confianza calibrada |
| `app/Services/OpenAI/EventAI/Prompts/EventStrategyPrompt.php` | Estratega: arquetipo, propuesta, motivaciones, ángulos, objeciones, CTA |
| `app/Services/OpenAI/EventAI/Prompts/EventCopyPrompt.php` | Copywriter V3: solo EXPRESAR la estrategia (sin SEO ni auto-auditoría) |
| `app/Services/OpenAI/EventAI/Prompts/EventSeoPrompt.php` | SEO/AEO: título, meta, OG, tags, ai_search_summary |
| `app/Services/OpenAI/EventAI/Prompts/EventAuditPrompt.php` | Auditor independiente con rúbrica |
| `app/Services/OpenAI/EventAI/Prompts/EventRepairPrompt.php` | Repair dirigido: corregir SOLO lo marcado |
| `app/Services/OpenAI/EventAI/Schemas/*.php` | Schemas versionados (extraction/strategy/copy/seo/audit) |
| `app/Services/OpenAI/EventAI/Quality/EventContentQualityGate.php` | Binario: estructura, longitudes, URLs, locale, terminología |
| `app/Services/OpenAI/EventAI/Quality/EventContentPolicyGate.php` | Binario: escasez falsa, notas internas, copy_safe, gratis≠free |
| `app/Services/OpenAI/EventAI/Quality/EventContentGenericnessGate.php` | Clichés + densidad de adjetivos genéricos |
| `app/Services/OpenAI/EventAI/EventAiOrchestrator.php` | Pipeline V3: extraction→strategy→copy→seo→audit→repair con routing |
| `app/Console/Commands/AiEvalRun.php` | Runner de evals (live con key; replay sin key) |
| `tests/AI/fixtures/*.php` | 16 fixtures mínimos (path a 50 documentado) |
| `tests/AI/AiEvalHarness.php` | Base de evals con constraints/forbidden_claims |
| Tests unitarios | Uno por builder/gate/prompt (en `tests/Unit/...`) |

## Archivos modificados

| Archivo | Cambio | Riesgo |
|---|---|---|
| `config/openai.php` | Bloque `event_assistant.v3` (bundle_version, audit/escalation toggles, max_repair_attempts desde config) + unificar default de cuota temporal en config (6, documentado) | Bajo — solo agrega claves |
| `config/features.php` | `event_ai_assistant_v3_enabled` (default false) | Bajo |
| `app/Http/Controllers/Organizer/EventAiAssistantController.php` | Rama flag-gated: si V3 → orchestrator (mismos endpoints, mismas respuestas de contrato). V2 queda como está | Medio — rama nueva, no toca V2 |
| `app/Jobs/GenerateEventContentDraftJob.php` | Idem para flujo de edición | Medio |
| `app/Jobs/AnalyzeEventFlyerJob.php` | Opcional: guardar facts V3 en review cuando flag activa | Bajo |
| `resources/views/organizer/event/partials/create-wizard-modal.blade.php` + `event-wizard.js` | UX V3 (Fase 11, posterior): estados de revisión + acciones dirigidas | Medio — después de backend estable |
| `docs/auditorias/ai-event-assistant-v3/*` | 10-21 finales | Nulo |

## No tocar (fuera de alcance)

Imágenes IA (`ImageGenerationService`, `GenerateAiImageJob`, prompts square/gallery/og), checkout, pagos, SEO global, edición de evento (salvo compatibilidad), DB existente (sin migraciones nuevas salvo necesidad demostrada — no se prevén).

## Definition of Done por fase

Cada fase: tests propios en verde + `php -l`/PHPUnit completos + commit pequeño + doc actualizada.
V3 completa = flag funciona, V2 funciona con flag apagada, gates/evals corriendo, y todo lo medible reportado como PASS/FAIL/NOT VERIFIED.
