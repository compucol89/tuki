# 19 — Rollout y rollback

## Activación (gradual)

1. Staging: `AI_EVENT_ASSISTANT_V3_ENABLED=true` + `OPENAI_API_KEY` presente.
2. Capturar baseline V2: `php artisan ai:eval-run --capture` con flag off (bundle v2), luego flag on (bundle v3).
3. Comparar: `--grade` + métricas de 17-eval-methodology.
4. Producir `20-v2-vs-v3-results.md` y evaluar promotion gates.
5. Producción: activar flag. Monitorear runs (`event_ai_assistant_runs`), costos y errores.

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
