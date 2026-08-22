# 19 — Rollout y rollback

## Activación (gradual)

1. Staging: `AI_EVENT_ASSISTANT_V3_ENABLED=true` + `OPENAI_API_KEY` presente.
2. Capturar baseline V2 real: `php artisan ai:eval-run --capture --pipeline=v2 --bundle=2026-08-21-v2`.
3. Capturar candidato V3: `php artisan ai:eval-run --capture --pipeline=v3 --bundle=2026-08-21-v3`.
4. Comparar: `php artisan ai:eval-run --grade --baseline=2026-08-21-v2 --candidate=2026-08-21-v3`.
5. Producir `20-v2-vs-v3-results.md` y evaluar promotion gates.
6. Producción: activar flag. Monitorear runs (`event_ai_assistant_runs`), costos y errores.

## Rollback inmediato

```
AI_EVENT_ASSISTANT_V3_ENABLED=false
```

- No requiere migraciones de DB ni cambios de código: el pipeline V2 queda intacto en controller y job.
- Los runs V3 ya persistidos quedan legibles (mismo contrato de payload V2).

## Controles de costo

- V3 usa ~4-6 llamadas por generación (strategy+copy+seo+audit ± repair/escalation).
- Toggles: `AI_EVENT_ASSISTANT_V3_AUDIT_ENABLED`, `AI_EVENT_ASSISTANT_V3_ESCALATION_ENABLED`, `AI_EVENT_ASSISTANT_V3_MAX_REPAIR_ATTEMPTS`.
- Límites existentes de V2 siguen vigentes (`EventAiUsageLimiter`: 2 drafts/evento, 10/día).

## Feature flags totales

| Flag | Default | Efecto |
|---|---|---|
| `AI_EVENT_ASSISTANT_ENABLED` | false | Habilita el asistente IA completo (V2 o V3) |
| `AI_EVENT_ASSISTANT_V3_ENABLED` | false | Activa el pipeline V3 dentro del asistente |

## Checklist de producción (antes de activar)

- [ ] API key configurada y cola `ai-content` con worker real.
- [ ] Baselines capturados y `20-v2-vs-v3-results.md` poblado.
- [ ] Promotion gates de 17-eval-methodology en verde (hard gates = 0).
- [ ] Monitoreo de errores/costos activo.
