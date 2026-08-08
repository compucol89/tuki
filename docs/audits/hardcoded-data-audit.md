# Auditoría Zero Hardcoded Data — TukiPass

Fecha: 2026-08-07
Ámbito: identidad corporativa (fiscal), emails, dominios, moneda, URLs, precios, IDs fijos.

## Resumen

- **Archivos escaneados:** 698 (app/, config/, resources/views/, routes/, database/, scripts/)
- **Sistemas de patrones:** 6 greps léxicos + 1 scan semántico (`php artisan audit:hardcoded`)
- **Hardcodes críticos corregidos:** 24 archivos
- **Hardcodes productivos restantes (CONFIRMED):** 0
- **Literales justificados documentados:** migrations históricas (10), whitelist de monedas MercadoPago (3rd-party API), demo seeder (test fixture), config/env defaults

## Matriz: fuente de verdad por tipo de dato

| Dato | Fuente de verdad (DB) | Fallback centralizado |
|---|---|---|
| Identidad fiscal (nombre, CUIT, IVA, dirección) | `billing_settings` (`BillingSetting::current()`) | `config/tukipass.php` (`fiscal.*`) |
| Email de soporte/contacto | `basic_settings.email_address` | `config/tukipass.php` (`fiscal.support_email`, `contact_email`) |
| Dirección del organizador | `basic_settings.address` | `config/tukipass.php` (`fiscal.issuer_address`) |
| Moneda base | `basic_settings.base_currency_text/symbol` | `config/tukipass.php` (`currency.*`) |
| Dominio canónico / www | `.env` (APP_URL, APP_WWW_DOMAIN, APP_BARE_DOMAIN) | `config/tukipass.php` (`redirect_www.*`) |
| Redes sociales | `social_medias` | — (sin links hardcodeados ✅) |
| Texto legal (politica de reembolsos, ARCA) | settings + `EventRefundPolicy` | `config/tukipass.php` |
| CUIT / punto de venta ARCA | `.env` (ARCA_ENVIRONMENT, ARCA_CUIT, ...) | `config/arca.php` |

## Matriz: archivos corregidos

| Archivo | Hallazgo | Fix |
|---|---|---|
| `views/frontend/contact.blade.php` | Dirección mapa literal | `$contactAddresses[0] ?? $websiteInfo->address` + sección condicional |
| `views/frontend/event/event-details.blade.php` | mailto soporte, priceCurrency, TAYRONA/CUIT | `$basicInfo->email_address`, config currency, `BillingSetting::current()` |
| `views/frontend/invoice/status.blade.php` | email hola@ | `$basicInfo->email_address` |
| `app/Support/EventRefundPolicy.php` | texto legal + defaults duplicados | dinámico + config único |
| `views/frontend/layout.blade.php` | preconnect/dns-prefetch literal | `url('/')` |
| `app/Http/Middleware/RedirectToWww.php` | dominios | `config('tukipass.redirect_www.*')` |
| `app/Http/Middleware/BlockSensitivePaths.php` | CSP | `securityHeaders()` + `parse_url(config('app.url'))` |
| `views/frontend/shop/details.blade.php` | priceCurrency | config currency |
| `views/frontend/payment/success.blade.php` | currency + resta `-5` horas | config currency + zona horaria |
| `MercadoPagoDiagnosticoController.php:145` | currency_id | config currency |
| `EventController.php:312` | `?? 'ARS'` | config currency |
| `AppServiceProvider.php:142` | fallback currency object | config currency |
| `ArcaInvoiceIssuingJob.php`, `CommissionInvoiceBuilder.php` | currency | `config('tukipass.currency.text')` |
| `views/frontend/event/invoice.blade.php` | TAYRONA/CUIT/currency fallbacks | config |
| `views/pdf/arca_invoice.blade.php` | fiscal footer | config |
| `views/emails/customer_verification.blade.php` | email | `$basicInfo->email_address` |
| `views/emails/arca_invoice.blade.php` | footer + condición IVA | `BillingSetting::current()` |
| `views/emails/event_confirmation.blade.php` | brand + fallbacks | `config('app.name')` + config |
| `SystemHealthCheck.php` | email literal | config |
| `app/Support/VenueGeocoder.php` | USER_AGENT | email de settings o config |
| `LegalPagesContentSeeder.php` | operator/cuit/address | config |
| `AiIndexController.php` | TAYRONA/CUIT | config |
| `views/frontend/partials/footer/footer.blade.php` | 6 URLs legales | `route('dynamic_page')` |

## Guardrail CI

- **Workflow:** `.github/workflows/hardcode-audit.yml` (on push + pull_request)
- **Script:** `scripts/audit-hardcode.sh` — 8 secciones:
  1. Secretos (P0) · 2. URLs productivas (P1) · 3. Emails (P1) · 4. IDs fijos (P1) · 5. Fallbacks de negocio (P2) · 6. Precios literales (P1) · 7. Direcciones (P1) · 8. Scan semántico artisan `audit:hardcoded --fail`
- **Comando Laravel:** `php artisan audit:hardcoded [--fail]` — `app/Console/Commands/AuditHardcodedData.php`, allowlist en `config/tukipass.php` (HARDCODE-ALLOW comentado), migrations, CORS/ARCA config, demo seeder, auto-referencia.
- **Verificado:** reintroducción de `soporte@tukipass.com` en una vista → FAIL (exit 1); limpio → PASS.

## Literales justificados (no reintroducir sin razón)

| Literal | Ubicación | Clasificación |
|---|---|---|
| TAYRONA / CUIT en email templates | `database/migrations/2026_05_22_*` (10) | SNAPSHOT HISTÓRICO (ya migrado a DB; no re-editar) |
| Lista monedas MP | `MercadoPagoController.php` (×2) | THIRD-PARTY API constraint |
| Honduras 5535 / Colombia 2026 | `SeedColombiaWorldCupEvents.php` | TEST FIXTURE (seed manual) |
| `http://localhost` | `config/app.php`, installer | ENV DEFAULT |
| `https://www.tukipass.com` | `config/cors.php` | CONFIG (env override) |
| `'Consumidor final'` | `pdf/arca_invoice.blade.php`, `emails/arca_invoice.blade.php` | Constante legal ARCA |

## Métricas

| Métrica | Valor |
|---|---|
| Archivos escaneados | 698 |
| Hallazgos totales | ~40 |
| Corregidos (P0/P1/P2) | 24 |
| Justificados documentados | 15 |
| Reintroducciones detectadas por guardrail | 1 (sonda de prueba, eliminada) |
| CONFIRMED PRODUCTION HARDCODES | **0** |
