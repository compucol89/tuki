# 02 · Cobertura de Superficie y Rutas

## Clasificación de los 87 tests por tipo

| Tipo | Suites | Qué prueban realmente |
|---|---|---|
| SMOKE/STRUCTURAL | e2e "carga sin errores" (15), aria h1/jerarquía (18) | status 200 + 1 h1 (locator en e2e) + consola + snapshot parcial |
| NAVIGATION | e2e flujo home→eventos, mobile search | navegación básica |
| FUNCTIONAL-A11Y | a11y (16), legal SEO (8), seo (4), theme (16) | Axe + invariantes SEO/legal + contrato computed-style del theming |
| TRUE E2E (funcionalidad transaccional) | **ninguno** | no hay compra/reserva/checkout/login-exitoso-autorizado automatizado |

**Hallazgo P1:** el nombre "e2e" cubre principalmente smoke estructural; **no hay ningún flujo funcional real** (reservar entrada, checkout, crear evento, withdraw, scanner) automatizado.

## Cobertura de rutas vs rutas reales (Laravel)

| Grupo | Rutas reales | Cubiertas por Playwright | Gap |
|---|---|---|---|
| Estáticas públicas | /, /eventos, /blog, /contacto, /sobre-nosotros, /organizadores, /preguntas-frecuentes, /tienda* | ✅ e2e+a11y+aria (+visual parcial) | — |
| Auth pública | /login, /registro, /recuperar, /reset + organizador login/signup/forget/reset | ✅ e2e+a11y+aria | — |
| Evento detalle | /{slug}/{id} | ⚠️ solo @theme (dark, evento 123) y @legal none | ❌ sin e2e funcional del detalle |
| Organizador panel | ~40 rutas | ⚠️ @theme (12 rutas light/dark) + @a11y dashboard (2, con creds) | ❌ resto sin cobertura |
| Blog detalle | /blog/{slug} | ❌ | ❌ |
| Legal | 6 páginas | ✅ @legal (SEO+a11y+MerchantReturnPolicy) | — |
| Errores | 404/410 | ❌ | ❌ |
| Transaccional (checkout/pagos) | varias | ❌ (intencionalmente excluido — fuera de alcance declarado) | documentado |
| Tienda (productos) | /tienda, /shop/* | ❌ (shop inactivo en prod) | documentado |

## Estados interactivos NO escaneados por Axe

Axe corre sobre el DOM inicial (`analyze()` tras `goto` + 300ms): dropdowns, modales, menú móvil, errores de validación, estados disabled/loading **no se escanean** (solo @theme interactúa, y valida color, no a11y). Gap P2 documentado.

## WAIVERS

- `a11y.spec.js`: `exclude('.phpdebugbar')` — solo dev; documentado en el spec con dueño/fecha. Sin expiry/ticket. Aceptable (Debugbar no existe en prod), pero sin expiración → deuda abierta.
- `@a11y organizer-dashboard` (2 tests): `test.skip` si no hay `E2E_ORGANIZER_USERNAME/PASSWORD` → **se saltan en cualquier entorno sin creds**.

## Auth

- Credenciales `E2E_ORGANIZER_USERNAME/PASSWORD` en **env, no versionadas**; CI debe recibirlas por secrets. No aparecen en logs/traces de las corridas observadas. Riesgo bajo si se mantienen fuera de Git.
