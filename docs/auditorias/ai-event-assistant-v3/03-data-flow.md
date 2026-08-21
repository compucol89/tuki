# 03 — Flujo de datos (V2 real)

## Cadena de datos (creación — flujo temporal)

```
user input (wizard form)
  → StoreRequest (validación de negocio, NO la de IA)
  → analyzeTemporaryCover:
      formFacts = temporaryFormFacts(request)        [duplica EventFactsBuilder::fromEvent]
      moderación (imagen + title/description/address)
      analysis = analyzeFlyer(imagen, formFacts)     [gpt-5.6-luna]
      canonicalFacts = temporaryCanonicalFacts()      [source_priority + form_facts + image_analysis]
      si generate_content:
        generated = generateContentWithQualityGate(canonicalFacts, temporaryPreferences())
        moderación (texto generado)
        draft → JSON al cliente
  → cliente (wizard JS) aplica campos al formulario
  → store() persiste evento/entradas/contenido
```

## Cadena de datos (edición — async)

```
startAnalysis → run(analysis) → AnalyzeEventFlyerJob
  → EventFactsBuilder::fromEvent(event)              [title, desc, address, city, country, category, fechas]
  → moderación
  → analyzeFlyer
  → EventFactsBuilder::canonicalFromAnalysis         [source_priority, form_facts, image_analysis]
  → EventAiAssistantReview (canonical_event_facts JSON)

generateDraft → run(content) + draft(pending) → GenerateEventContentDraftJob
  → EventAiDraftPreferences::fromReview
  → generateContent(canonicalFacts, preferences)
  → moderateText
  → EventAiDraftPostProcessor::sanitize
  → EventAiContentDraft.generated_payload + audit_payload + audit_status

apply → campos seleccionados → event_contents (título, descripción, meta, keywords)
```

## Qué es canónico vs sugerencia (V2 real)

| Dato | Fuente canónica hoy | Problema |
|---|---|---|
| Precio | DB tickets (si hay) | ⚠️ NO entra al contexto de la IA (formFacts no incluye tickets ni precios) |
| Fecha/hora | form | entra como texto sin normalizar |
| Venue/dirección | form | entra |
| Título | form | entra |
| Artist/sponsor | flyer | entra como extracted_field con confidence; el prompt decide |
| Promos | flyer o form | sin distinción de "copy_safe" |

**Hallazgos estructurales V2:**
- No existe el concepto de procedencia por hecho (`source`, `verified`, `copy_safe`, `updated_at`).
- `confirmed_fields` / `ignored_fields` están hardcodeados vacíos.
- La resolución de conflictos flyer↔form se delega 100% al prompt (P1), sin resolución determinista previa.
- El precio no puede ser citado por el copy porque ni siquiera llega al contexto.
