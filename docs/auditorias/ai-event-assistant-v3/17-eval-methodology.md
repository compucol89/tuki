# 17 — Metodología de evaluación

## Componentes

1. **Fixtures** (`tests/AI/fixtures/event_fixtures.php`): 16 casos, objetivo 50. Cada fixture: `input` (form_facts, ticket_facts, flyer_evidence, preferences) + `constraints` + `forbidden_claims` + `expected_critical_fields`.
2. **Integridad offline** (`tests/AI/FixtureIntegrityTest.php`): IDs únicos, estructura, cobertura de arquetipos, sin placeholders. Corre en CI sin API.
3. **Captura live** (`php artisan ai:eval-run --capture`): requiere `OPENAI_API_KEY`; guarda `tests/AI/baseline/{bundle}/{id}.json` con latencia y timestamp. Sin key → NOT VERIFIED sin error.
4. **Grading offline** (`php artisan ai:eval-run --grade`): compara outputs capturados contra forbidden_claims (token overlap). Offline y determinista.
5. **Gates deterministas** (3 clases) + grader de genericness: corren sobre cualquier payload, con y sin API.

## Reglas

- CI nunca llama APIs pagas (el runner live es explícito).
- Mocks solo para tests estructurales (orquestador con servicio mockeado); NUNCA para afirmar calidad lingüística.
- Una variable por experimento: para comparar V2 vs V3, mismo fixture set, mismo modelo base, captura de ambos bundles.

## Métricas por correr con key

Hard: hallucinated_sensitive_fact_rate, critical_contradiction_rate, wrong_price/date/time_rate, false_scarcity_rate, internal_note_leak_rate, faq_unsupported_rate, schema_validity, locale_consistency.
Soft: specificity, genericness, brand_voice, persuasion, seo_quality.
Costo: llamadas por evento, tokens, latencia p50/p95, repair rate, escalation rate.
