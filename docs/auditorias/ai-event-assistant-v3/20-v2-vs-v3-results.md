# 20 — Resultados V2 vs V3

## Estado: NOT VERIFIED (sin medición live)

`OPENAI_API_KEY` no está configurada en el entorno local de desarrollo. No se ejecutaron llamadas live a OpenAI durante esta iteración.

## Lo que SÍ está verificado (offline)

| Ítem | Estado |
|---|---|
| Suite PHPUnit completa | PASS — 225 tests, 0 fallos |
| Tests nuevos V3 (facts, prompts, schemas, gates, orquestador, runner, fixtures) | PASS — 50 tests nuevos |
| Integridad del dataset de evals | PASS — 16 fixtures válidos |
| Runner `ai:eval-run --capture` sin key | PASS — reporta NOT VERIFIED sin error |
| Runner `ai:eval-run --pipeline=v2|v3` | PASS — V2 usa el generador legacy; V3 usa el orchestrator |
| Gate de genericidad runtime | PASS — `EventAiOrchestrator` bloquea `score > 4` |
| SEO post-repair | PASS — SEO se regenera cuando `audit → repair` modifica el copy |
| Regresión del wizard con V3 OFF (V2 activo) | PASS — Playwright: modal, stepper, extracción IA y panel renderizan |
| Compilación de vistas | PASS — `php artisan view:cache && php artisan view:clear` |

## Cómo completar este documento

1. Configurar `OPENAI_API_KEY` en staging.
2. `php artisan ai:eval-run --capture --pipeline=v2 --bundle=2026-08-21-v2 --limit=3` para smoke V2.
3. `php artisan ai:eval-run --capture --pipeline=v3 --bundle=2026-08-21-v3 --limit=3` para smoke V3.
4. Repetir ambos comandos sin `--limit` si el smoke pasa.
5. `php artisan ai:eval-run --grade --baseline=2026-08-21-v2 --candidate=2026-08-21-v3` para comparar ambos bundles + correr los 3 gates deterministas sobre los outputs capturados.
6. Rellenar la tabla de promoción:

| Métrica | V2 | V3 | Delta |
|---|---|---|---|
| Hallucinated sensitive facts | ? | ? | ? |
| Critical contradictions | ? | ? | ? |
| Prompt injection escapes | ? | ? | ? |
| Wrong prices/dates | ? | ? | ? |
| Internal note leaks | ? | ? | ? |
| Genericness score | ? | ? | ? |
| Specificity | ? | ? | ? |
| Costo por evento | ? | ? | ? |
| Latencia p95 | ? | ? | ? |

Ninguna afirmación de mejora es válida hasta completar esta tabla.
