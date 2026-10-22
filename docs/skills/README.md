# Skills de TukiPass

## Skills propias (versionadas)

Están en `docs/skills/` y se instalan en el store local con:

```bash
./scripts/install-project-skills.sh
```

| Skill | Uso |
|---|---|
| `tukipass-payment-integrity` | Pagos, webhooks, idempotencia, reembolsos |
| `tukipass-ticket-stock` | Stock, concurrencia, liberación |
| `tukipass-qr-security` | QR, escaneo, auditoría |
| `tukipass-release-guard` | Deploy, backup, rollback |
| `tukipass-organizer-isolation` | Aislamiento organizador/cliente |
| `tukipass-performance` | Medición y optimización de capacidad, queries y assets |

> **Fuente de verdad:** código + tests + mediciones. Estas skills son mapas operativos. Si el código cambia, actualizar la skill en el mismo PR. Antes de asumir que un fix está en producción, comparar `git rev-parse HEAD` con el commit desplegado en EasyPanel.

## Skills externas instaladas

```bash
npx skills add jeffallan/claude-skills --skill database-optimizer -y
npx skills add coreyhaines31/marketingskills --skill seo-audit -y
npx skills add coreyhaines31/marketingskills --skill schema -y
npx skills add coreyhaines31/marketingskills --skill analytics -y
npx skills add coreyhaines31/marketingskills --skill conversion-tracking -y
```

Nombres reales del repo de Corey Haines: `schema`, `analytics`, `signup`. Los nombres `schema-markup`, `analytics-tracking` y `signup-flow-cro` que muestra skills.sh no coinciden con la ruta `skills/<name>/SKILL.md`.

## Skills que no se instalan

- `playwright-expert`: duplica `playwright-best-practices` (26 KB, ya instalada).
- `signup-flow-cro` / `signup`: riesgo/beneficio bajo para el checkout actual; evaluar si se prioriza CRO.
- `ui-design-system`, `tailwind-css-patterns`, `vercel-react-*`: no aplican a Blade + Bootstrap 4 + jQuery.

## Verificación

```bash
STRICT=1 ./scripts/audit-skills.sh
```

El lock (`skills-lock.json`) tiene 37 entradas. En un checkout sin el store local, el script informa las ausentes y no falla; usar `STRICT=1` en la máquina del operador.
