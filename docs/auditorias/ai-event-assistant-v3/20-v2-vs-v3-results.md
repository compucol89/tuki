# 20 — Resultados V2 vs V3

## Estado: NOT VERIFIED (sin medición live)

`OPENAI_API_KEY` no está configurada en el entorno local de desarrollo. No se ejecutaron llamadas live a OpenAI durante esta iteración.

## Lo que SÍ está verificado (offline)

| Ítem | Estado |
|---|---|
| Suite PHPUnit completa | PASS — 222 tests, 0 fallos |
| Tests nuevos V3 (facts, prompts, schemas, gates, orquestador, fixtures) | PASS — 47 tests nuevos |
| Integridad del dataset de evals | PASS — 16 fixtures válidos |
| Runner `ai:eval-run --capture` sin key | PASS — reporta NOT VERIFIED sin error |
| Regresión del wizard con V3 OFF (V2 activo) | PASS — Playwright: modal, stepper, extracción IA y panel renderizan |
| Compilación de vistas | PASS |

## Cómo completar este documento

1. Configurar `OPENAI_API_KEY` en staging.
2. `php artisan ai:eval-run --capture` con `AI_EVENT_ASSISTANT_V3_ENABLED=false` (bundle v2).
3. `php artisan ai:eval-run --capture` con la flag en true (bundle v3).
4. `php artisan ai:eval-run --grade` para ambos bundles + correr los 3 gates deterministas sobre los outputs capturados.
5. Rellenar la tabla de promoción:

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
