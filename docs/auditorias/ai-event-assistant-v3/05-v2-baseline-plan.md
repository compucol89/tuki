# 05 — Plan de baseline V2

## Objetivo

Poder afirmar "V3 > V2" con evidencia medida, no por sensación.

## Estado de capacidad local

- `OPENAI_API_KEY` NO está configurada en el entorno local actual → baseline live = **NOT VERIFIED** hasta disponer de key.
- La suite de fixtures y graders se construye offline (sin llamadas pagas); el runner live queda listo para ejecutarse con key.

## Fixtures (objetivo: 50; arranque mínimo: 16)

Cada fixture define: `name`, `input` (facts del evento + flyer evidence simulada + preferences), `constraints` (afirmaciones permitidas), `forbidden_claims` (afirmaciones prohibidas), `expected_critical_fields`.

Arquetipos mínimos:
1. venue_nightlife_simple · 2. venue_concert_artist · 3. online_webinar · 4. free_family_event · 5. venue_vip_earlybird · 6. sports_match · 7. theatre_play · 8. conference_professional · 9. multi_date_festival · 10. flyer_vertical_9x16 · 11. flyer_no_date · 12. form_flyer_conflict · 13. flyer_with_sponsors · 14. flyer_with_ticketing_platform · 15. prompt_injection_in_flyer · 16. price_in_tickets_db

## Métricas V2 (a medir)

- Hard: hallucinated_sensitive_fact_rate, critical_contradiction_rate, wrong_price_rate, wrong_date_rate, wrong_time_rate, false_scarcity_rate, internal_note_leak_rate, faq_unsupported_rate, schema_validity, locale_consistency.
- Soft: specificity, genericness, brand_voice, persuasion, seo_quality.
- Costo: llamadas, tokens in/out, latencia p50/p95, repair/retry rate.

## Genericness (métrica especial)

Señales literales: "Prepárate para…", "Sumérgete en…", "una experiencia única", "una noche inolvidable", "no te lo pierdas", "música, energía y diversión". Más similitud semántica entre outputs de eventos distintos (n-gramas compartidos + adjetivo genérico por densidad de hechos específicos).

## Reglas del harness

- CI nunca llama APIs pagas.
- Live runner explícito (comando dedicado) solo con key presente.
- Replay de outputs guardados para comparación posterior.
- Mocks solo para tests estructurales; NUNCA para afirmar calidad lingüística.

## Captura de baseline

1. Completar fixtures (Fase 1 de implementación).
2. Con key disponible: correr P4 V2 sobre fixtures, guardar outputs + metadatos en `tests/AI/baseline/v2/`.
3. Sin key: registrar **NOT VERIFIED** y continuar con lo offline.
