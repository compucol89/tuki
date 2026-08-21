# 10 — Pipeline V3 implementado (etapas, prompts, routing)

Consolida los documentos 10-16 del plan (canonical facts, extraction, strategy, copy, seo, audit, repair).

## Etapas

```
extraction (FlyerExtractionPrompt, gpt-5.6-luna, escalación condicional a gpt-5.6-sol)
  → strategy (EventStrategyPrompt, gpt-5.6-terra)
  → copy (EventCopyPrompt, gpt-5.6-terra + few-shot por arquetipo)
  → seo (EventSeoPrompt, gpt-5.6-terra)
  → audit (EventAuditPrompt, gpt-5.6-terra via models.audit) [toggle]
  → repair (EventRepairPrompt, máx N intentos desde config) [si audit=repair]
  → gates deterministas (QualityGate + PolicyGate + GenericnessGate)
```

## Archivos

| Capa | Archivo |
|---|---|
| Facts | `app/Services/EventAi/CanonicalEventFacts.php`, `CanonicalEventFactsBuilder.php` (+ `buildFromTemporary`) |
| Contexto | `PlatformContext.php` (marca/legal/rol), `BrandVoice.php` (voz es-AR), `EventArchetypes.php` (taxonomía + few-shot) |
| Prompts | `app/Services/OpenAI/EventAI/Prompts/*` (6 clases) |
| Schemas | `app/Services/OpenAI/EventAI/Schemas/*` (5 versionados) |
| Gates | `app/Services/OpenAI/EventAI/Quality/*` (3 deterministas) |
| Orquestador | `app/Services/OpenAI/EventAI/EventAiOrchestrator.php` |
| Evals | `app/Console/Commands/AiEvalRun.php` + `tests/AI/` |

## Routing de modelos

- extract: `models.extract` (luna) · escalación → `models.escalate` (sol) SOLO si campo sensible con confidence < 0.75 o críticos/conflictos (toggle `v3.escalation_enabled`).
- generate/strategy/copy/seo/repair: `models.generate` (terra).
- audit: `models.audit` (terra) — ahora SÍ cableado como llamada independiente (toggle `v3.audit_enabled`).
- repair: máximo `v3.max_repair_attempts` (default 1) desde config (antes hardcodeado en 2).

## Compatibilidad V2

- El payload final se fusiona al contrato V2 (content/social/seo/faq/review_checklist/missing_information/audit) → UI, `EventAiDraftPostProcessor::sanitize`, DB y apply siguen funcionando sin cambios.
- `audit.status` normalizado a valores V2 (`passed`/`repaired`/`needs_human_review`).
- Feature flag `AI_EVENT_ASSISTANT_V3_ENABLED=false` por defecto → comportamiento V2 intacto.

## Deuda documentada (próximas iteraciones)

- Wizard UX V3 (estados de revisión + acciones dirigidas) — pendiente, fase 11.
- Baselines live V2/V3 — pendientes de API key (NOT VERIFIED).
- Ampliar dataset de 16 → 50 fixtures.
