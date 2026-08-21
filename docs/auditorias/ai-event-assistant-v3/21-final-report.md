# 21 — Reporte final

## Executive summary

Se auditó el AI Event Assistant V2 (forensic discovery) y se implementó la arquitectura V3 por etapas, manteniendo V2 100% funcional detrás de una feature flag. V3 separa responsabilidades (extracción → estrategia → copy → SEO → auditor independiente → repair), introduce un contrato determinista de hechos canónicos con procedencia y `copy_safe`, activa los modelos `audit`/`escalate` que estaban configurados sin cablear, y suma 3 quality gates deterministas + un dataset de evals con runner live/offline.

## Qué existía antes (confirmado)

- V2: extracción (luna) + PROMPT MAESTRO de 37 secciones (terra) que hacía copy+SEO+FAQ+social+auto-auditoría en UNA llamada. `audit`/`escalate` configurados sin uso. Quality gate heurístico con 2 intentos hardcodeados. Sin medición de genericness. Sin tests del servicio.

## Qué cambió

| Área | Cambio |
|---|---|
| Facts | `CanonicalEventFacts` (value/source/confidence/verified/sensitive/copy_safe) con resolución determinista por prioridad de fuente; builder para evento y para flujo temporal; precios de tickets ahora llegan al contexto (copy_safe=false) |
| Contexto | `PlatformContext` (marca TAYRONA/TukiPass, rol, legal como restricción no boilerplate), `BrandVoice` (voz es-AR, clichés prohibidos), `EventArchetypes` (taxonomía + few-shot contextual) |
| Etapas | 6 clases de prompt + 5 schemas versionados; pipeline `EventAiOrchestrator`; auditor INDEPENDIENTE con rúbrica; repair dirigido; escalación condicional por confianza baja |
| Gates | QualityGate (estructura/terminología), PolicyGate (escasez/notas internas/volátiles/gratis), GenericnessGate (score 0-10 + señales) |
| Evals | 16 fixtures + integridad + `ai:eval-run` (capture live / grade offline) |
| Integración | Rama flag-gated en `analyzeTemporaryCover` y `GenerateEventContentDraftJob`; payload final fusionado al contrato V2 |
| Config | `openai.event_assistant.v3.*`, `features.event_ai_assistant_v3_enabled`, cuota temporal unificada en config (6, documentado) |

## Archivos (ver commits por fase)

- Nuevos: `app/Services/EventAi/{CanonicalEventFacts,CanonicalEventFactsBuilder,PlatformContext,BrandVoice,EventArchetypes}.php`, `app/Services/OpenAI/EventAI/**`, `app/Console/Commands/AiEvalRun.php`, `tests/AI/**`, tests unitarios nuevos.
- Modificados: `config/openai.php`, `config/features.php`, `EventAiAssistantService` (métodos públicos aditivos), `EventAiAssistantController`, `GenerateEventContentDraftJob`.
- Documentación: `docs/auditorias/ai-event-assistant-v3/00-21`.

## Migraciones

Ninguna. V3 reutiliza el modelo de datos V2 (runs/reviews/drafts). Rollback = apagar flag.

## Tests

- Suite completa: **222 tests, 0 fallos** (deprecations preexistentes de PHPUnit: 6).
- Playwright: wizard venue con flag V3 OFF renderiza correctamente (regresión V2 = PASS).

## V2 vs V3

**NOT VERIFIED** — requiere `OPENAI_API_KEY` y captura de baselines (`ai:eval-run --capture`). Ver `20-v2-vs-v3-results.md`. Ninguna afirmación de mejora lingüística es válida sin esos datos.

## Riesgos conocidos

1. Costo: V3 usa ~4-6 llamadas por generación; mitigado con toggles de audit/escalation y límites V2 vigentes.
2. Latencia: pipeline secuencial; medible solo con key.
3. Extracción V3 cambia el schema de análisis (más rico); el flujo temporal lo adapta a canonical facts V2-compatible — validado por tests, no por llamada live.
4. Cola: sin worker en docker-compose local (documentado; no es scope V3).

## Rollback

`AI_EVENT_ASSISTANT_V3_ENABLED=false` — V2 queda intacto, sin migraciones.

## Próxima iteración recomendada

1. Capturar baselines V2/V3 con API key y completar 20-.
2. Ampliar dataset a 50 fixtures.
3. Wizard UX V3 (estados de revisión + acciones dirigidas: más vendedor/más corto/otro ángulo).
4. Consolidar duplicaciones V2 heredadas (`canonicalFactValue`, `temporaryCanonicalFacts`).
5. V4: adaptación creativa de imágenes (fuera de alcance V3, intencionalmente no tocada).
