# 01 — Mapa de arquitectura (V2 real)

## Flujo de CREACIÓN (wizard)

```
/organizer/add-event?type=venue|online
  wizard modal (6 pasos: Portada · Copy IA · Entradas · Ubicación · Publicar · Avanzado)
  ├─ Paso 1 "Extraer datos con IA"
  │    POST organizer.events.ai-assistant.temporary_cover_analysis  (throttle 4,1)
  │    → EventAiAssistantController@analyzeTemporaryCover (L29)
  │      ├─ config features.event_ai_assistant_enabled
  │      ├─ validación imagen (mimes + tamaño)
  │      ├─ consumeTemporaryAnalysisQuota (Cache, default 2 en controller / 6 en config)
  │      ├─ moderateImageAndText  (omni-moderation-latest)
  │      ├─ analyzeFlyer  (gpt-5.6-luna, Responses API, json_schema análisis)
  │      ├─ temporaryCanonicalFacts  (duplica EventFactsBuilder)
  │      └─ si generate_content=1 → generateContentWithQualityGate (gpt-5.6-terra, máx 2 intentos)
  └─ Paso 2 "Armar evento con IA" (panel create-cover-ai-panel)
       → mismo endpoint con generate_content=1
       → apply client-side (event-wizard.js / bindCoverAiCreateFlow)
```

## Flujo de EDICIÓN (por evento, async con jobs)

```
POST organizer.events.ai-assistant.analysis
  → EventAiAssistantController@startAnalysis (L170)
    ├─ EventAiUsageLimiter@check (2/evento, 10/día)
    ├─ crea EventAiAssistantRun (type=analysis, model=gpt-5.6-luna)
    └─ dispatch AnalyzeEventFlyerJob
        ├─ moderateImageAndText
        ├─ analyzeFlyer (gpt-5.6-luna)
        ├─ EventFactsBuilder::canonicalFromAnalysis
        └─ crea EventAiAssistantReview (status=pending)

POST organizer.events.ai-assistant.draft  (reviewed)
  → EventAiAssistantController@generateDraft
    ├─ EventAiUsageLimiter@checkContent (2/evento, 10/día)
    ├─ crea EventAiAssistantRun (type=content, model=gpt-5.6-terra)
    ├─ crea EventAiContentDraft (status=pending)
    └─ dispatch GenerateEventContentDraftJob
        ├─ EventAiDraftPreferences::fromReview
        ├─ generateContent (gpt-5.6-terra, PROMPT MAESTRO 37 secciones, json_schema)
        ├─ moderateText (texto generado)
        ├─ EventAiDraftPostProcessor::sanitize
        └─ guarda draft (generated_payload + audit_payload + audit_status)

POST organizer.events.ai-assistant.drafts/{draft}/apply
  → aplica campos al EventContent (selectivo por checkboxes)
```

## Punto débil estructural (confirmado)

El generador resuelve TODO en una sola llamada:

```
gpt-5.6-terra  →  copy + SEO + OG + FAQ + social + checklist + audit (auto-calificación)
```

- No existe capa de ESTRATEGIA comercial previa (motivación, ángulo, arquetipo).
- La "auditoría" es auto-evaluación dentro del mismo schema; no hay segunda opinión independiente.
- `models.audit` y `models.escalate` están configurados pero sin cablear.
- No hay métrica de genericness/similitud entre eventos.

## Dependencias a preservar en V3

- Schemas V2 públicos (ver 02-prompt-inventory) — consumidos por UI (JS `event-ai-assistant-script`, `bindCoverAiCreateFlow`, wizard `event-wizard.js`).
- Rutas y throttles existentes.
- Tablas y modelos actuales (runs/reviews/drafts).
- `EventAiDraftPostProcessor::sanitize` (filtros de seguridad en producción).
- Moderación pre/post.
