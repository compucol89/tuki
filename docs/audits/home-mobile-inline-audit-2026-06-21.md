# INFORME DE AUDITORÍA READ-ONLY — TUKIPASS

> **Fecha**: 2026-06-21  
> **Agente**: Auditor Técnico Senior  
> **Objetivo**: Auditar reglas mobile duplicadas en home.css + inline styles en Blade. Documentar sin modificar.  
> **Handoff origin**: Orquestador → Auditor  
> **Handoff destino**: Auditor → Ejecutor  
> **Decisión de avance**: APTO PARA IMPLEMENTACIÓN QUIRÚRGICA  

---

## 1. Superpowers / Skills requeridas

- **systematic-debugging**: Metodología de investigación de 4 fases para clasificar hallazgos con evidencia exacta.
- **verification-before-completion**: Cada hallazgo validado con archivo y línea exacta antes de registrarlo.
- **Recomendadas para el Ejecutor**: `frontend-design`, `responsive-design`, `laravel-patterns` (para entender la cascada CSS en el sistema de assets).

---

## 2. Resumen ejecutivo

**Tarea 1 — Mobile duplicado en home.css**: Confirmado. **42 bloques @media** en 4,572 líneas. De esos, **26 son mobile** (`max-width: 767/575/420/360/991/1199`). Los mismos selectores reciben valores distintos en múltiples bloques del mismo breakpoint. La técnica de consolidación propuesta (un solo bloque de overrides al final del CSS) es viable y segura.

**Tarea 2 — Inline styles en Blade**: Confirmado. **498 ocurrencias** de `style=""` en **48 archivos**. De esas, **~337** están en emails/template-view (necesarias para deliverability — NO migrar). **~45** son dinámicas (usan `{{ }}`) y deben quedarse. Quedan **~94 estáticas** fuera de emails que son candidatas a migración, con **20+ quick wins** (`display:none` repetido).

**Riesgo general**: BAJO (read-only). Riesgo de implementación: MEDIO para Tarea 1 (cambia espaciado visual, requiere QA), BAJO para Tarea 2 (quick wins sin riesgo).

---

## 3. Contexto recibido del Orquestador

- **Objetivo**: Dos investigaciones quirúrgicas read-only: (1) reglas mobile duplicadas en home.css, (2) inline styles en Blade.
- **Alcance**: `public/assets/front/css/home.css`, `responsive.css`, `style.css`, `resources/views/**/*.blade.php`. Solo búsqueda y lectura.
- **Fuera de alcance**: NO modificar archivos. NO tocar checkout, pagos, booking, gateways, ARCA. NO tocar inline styles dinámicos con `{{ }}`. NO tocar inline styles en emails. NO implementar overrides.
- **Riesgo estimado original**: BAJO.
- **Archivos prohibidos**: `.env`, `vendor`, `node_modules`, `storage`.

---

## 4. Estado inicial del repositorio

```
Working directory: /Users/compucolargentina/Documents/www/tuki
Branch: (current, not detached)
Uncommitted changes: ~20 files modificados
  - home.css: +2,526 lines (tiene cambios sustanciales sin commit)
  - style.css: +249 lines
  - event-detail.css: +4,684 lines
  - Varias vistas Blade y assets
Archivos borrados: varios CSS del admin (all.min.css, certificate.css, flaticon.css, main.css, website-color.php)
```

**Nota**: `home.css` tiene modificaciones sin commitear que representan ~55% de su contenido actual (2,526 líneas añadidas). Parte de la duplicación puede estar en el diff sin commit.

---

## 5. Archivos y áreas revisadas

| Archivo | Líneas | Qué se revisó |
|---------|--------|---------------|
| `public/assets/front/css/home.css` | 4,572 | 42 bloques @media completos, selectores con spacing |
| `public/assets/front/css/responsive.css` | existente | 10 bloques @media, cruce con selectores de home.css |
| `public/assets/front/css/style.css` | existente | 24 bloques @media, selectores de home page |
| `resources/views/**/*.blade.php` | 48 archivos | 498 inline styles clasificados |

---

## 6. Flujo real encontrado

### 6.1 Cascada CSS del frontend público

```
style.css (general, ~5,000+ líneas)
  → responsive.css (media queries globales, 10 bloques)
    → home.css (específico de home page, 42 bloques @media)
```

**Hallazgo crítico**: `responsive.css` existe y tiene sus propias reglas mobile para `.hero-content h1`, `.client-logo-item`, `.section-title h2`, etc. Esto crea **triple conflicto potencial**: `style.css → responsive.css → home.css`. Sin embargo, `home.css` carga después en el orden de assets, por lo que **gana por cascada** — pero los valores inconsistentes *dentro* de home.css compiten entre sí por orden de aparición.

### 6.2 home.css: dos sistemas visuales coexistiendo

El archivo tiene dos "reinicios" visuales que compiten:

1. **"Apple x Airbnb" polish** (líneas ~1590-1690): Define padding/spacing con `--home-section-space-mobile: 48px` y overrides.
2. **"Tangerine visual system"** (líneas ~2120-2817): Redefine completamente las variables CSS (`--tk-bg`, `--tk-primary`, etc.) con un nuevo sistema de color. Luego aplica sus propias reglas mobile que *redefinen* valores ya establecidos en el bloque Apple x Airbnb.

---

## 7. Hallazgos confirmados — Tarea 1: Mobile duplicado

### 7.1 Distribución de @media blocks en home.css

| Breakpoint | Tipo | Cantidad | Líneas |
|------------|------|----------|--------|
| `max-width: 767px` | Mobile | **16** | 349, 1023, 1586, 1828, 2095, 2860, 3064, 3228, 3594, 3694, 3745, 3939, 3958, 4404, 4443, (varios más) |
| `max-width: 991px` | Tablet | **8** | 421, 656, 1747, 2818, 3046, 3083, 3920, (más) |
| `max-width: 575px` | Mobile small | **8** | 427, 685, 1940, 2921, 3205, 3571, (más) |
| `max-width: 360px` | Mobile tiny | **3** | 3525, 4171, 4549 |
| `max-width: 1199px` | Desktop down | 2 | 1695, (más) |
| `max-width: 420px` | Mobile mid | 1 | 1995 |
| `min-width: 768px` | Desktop up | 3 | 4191, 4364, (más) |
| `min-width: 992px` | Desktop | 4 | 2042, 3734, 3755, 4375 |
| `min-width: 1200px` | Wide | 2 | 2028, 3876 |
| `prefers-reduced-motion` | A11y | 1 | 252 |

**Total: 48 ocurrencias de `@media` en 42 bloques (algunos bloques tienen múltiples condiciones).**

### 7.2 Tabla de conflictos de spacing mobile

| Selector | Líneas en home.css | Valor conflicto 1 | Valor conflicto 2 | Valor conflicto 3 | responsive.css |
|----------|-------------------|--------------------|--------------------|--------------------|----------------|
| `body.home-page .hero-collage-section` padding-top | 2146, 2819, 2861, 2997, 3047, 3065, 3084, 3206, 3756, 3888, 3921, 3940, 3959, 4172 | `76px` (L3084) | `52px` (L2997, desktop base) | `42px` (L2861, L4172) | No aplica (selector distinto) |
| `body.home-page .hero-collage-section` padding-bottom | (mismas líneas) | `66px` (L3084) | `44px` (L2998) | `38px` (L2862) | No aplica |
| `body.home-page #heroHeadingHome` font-size (767px) | 2872, 3071, 3169, 3577, 3837, 3927 | `36px` (L2872) | `34px` (L3071, L3946) | `30-34px clamp` (L3175) | `38px` (L189 resp.) |
| `body.home-page .hero-content--premium h1` font-size (991px) | 2825, 3053 | `42px` (L2826) | `40px` (L3054) | - | No aplica |
| `body.home-page .events-section` padding-top (767px) | 374, 2904, 3706, 4210 | `48px var` (L374) | `42px` (L2906) | `26px` (L3706) | No aplica |
| `body.home-page .hs-search-wrap` padding-bottom (767px) | 373, 2902, 3702 | `48px var` (L373) | `42px` (L2902) | `20px` (L3702) | No aplica |
| `.ev-card__title` font-size (767px) | 1063, 1596, 1898, 3445, 3701 | `18px` (L1063) | `18px` (L1596) | `clamp(14,3.7vw,15)` (L3445) | No aplica |
| `.ev-card__datetime-bar` layout (767px) | 3228-3460 (grid layout), 4404-4548 (flex layout) | Grid 2-col (L3240) | Flex row (L4410) | - | No aplica |
| `.events-marquee-item` width (767px) | 380, 1854, 2908, 3064 | `278px` (L380) | `270px` (L1854) | `278px` (L2908) | No aplica |
| `.events-marquee-item` width (575px) | 1948, 2927 | `250px` (L1949) | (hereda de 767px) | - | No aplica |

### 7.3 Hallazgo H1: El caso `hero-collage-section` — 14 definiciones distintas

El selector `body.home-page .hero-collage-section` (y su variante `--premium`) aparece en **14 lugares distintos** del archivo con valores de padding conflictivos. La cascada resuelve esto por orden de aparición (último gana), pero como los bloques están entrelazados entre reglas non-media, el resultado visual es impredecible sin leer el archivo completo.

**Evidencia concreta de la contradicción más grave**:
- Línea 3756-3830: Bloque `min-width: 992px` define `min-height: 600px`, `padding: 88px 0 78px`
- Línea 3888-3918: Reglas non-media redefinen `padding-top: 36px; padding-bottom: 32px`
- Línea 3921-3937: Bloque `max-width: 991px` redefine `padding-top: 42px; padding-bottom: 38px`
- Línea 3940-3956: Bloque `max-width: 767px` redefine `padding-top: 38px; padding-bottom: 36px`
- Línea 3959-4005+: Otro bloque `max-width: 767px` redefine `min-height: clamp(430px, 78svh, 486px)`, `padding-top: 42px !important`

**Tres bloques distintos en `max-width: 767px` definen padding-top diferente para el mismo elemento**: 38px, 42px (con `!important`), y valores heredados de bloques non-media.

### 7.4 Hallazgo H2: Dos modelos de layout para `.ev-card` en mobile

En `max-width: 767px`:
- Líneas 3228-3460: Define grid de 2 columnas (`grid-template-columns: var(--ev-mobile-media) minmax(0, 1fr)`) — diseño compacto horizontal.
- Líneas 4404-4548: Define flex (`display: flex`) con datetime-bar full-width — diseño vertical con ticket bar abajo.

**El segundo bloque (L4404) pisa parcialmente al primero**, pero como no resetea todas las propiedades grid, puede generar un layout híbrido roto. Esto requiere QA visual urgente.

### 7.5 Hallazgo H3: responsive.css tiene sus propias reglas mobile

`responsive.css` (líneas 181-287) define en `max-width: 767px`:
```css
.hero-content h1 { font-size: 38px; }
```
Pero `home.css` en el mismo breakpoint (L2872) define:
```css
body.home-page #heroHeadingHome { font-size: 36px; }
```

**La mayor especificidad de `#heroHeadingHome` (ID) + `body.home-page` hace que home.css gane**, pero la coexistencia es confusa y fuente de bugs si alguien modifica responsive.css sin saber que home.css lo pisa.

---

## 8. Hallazgos confirmados — Tarea 2: Inline styles

### 8.1 Conteo y clasificación

| Categoría | Cantidad | Archivos | ¿Migrable? |
|-----------|----------|----------|-------------|
| **Emails** (customer_verification, event_confirmation, arca_invoice) | ~90 | 3 | ❌ NO — necesario para email deliverability |
| **Template view** (builder de emails) | ~259 | 1 | ❌ NO — son templates de cliente, mso- prefixed |
| **PDF** (arca_invoice) | ~8 | 1 | ❌ NO — necesario para DOMPDF rendering |
| **Dinámicos** (con `{{ }}`) | ~45 | 15 | ❌ NO — dependen de datos del backend |
| **Estáticos display:none** | ~20 | 11 | ✅ SÍ — quick wins |
| **Estáticos misc** (spacing, color, layout) | ~74 | 25 | ⚠️ Parcial — analizar caso por caso |
| **Checkout/booking** | ~5 | 3 | ⚠️ SOLO DOCUMENTAR — no migrar sin auditar impacto funcional |
| **TOTAL** | **~498** | **48** | |

### 8.2 Tabla de quick wins — `display:none` repetidos

| Archivo:línea | Style inline | ¿Repetido? | Migrable a clase |
|---------------|-------------|------------|-----------------|
| `frontend/customer/signup.blade.php:194` | `style="display:none"` (suStrengthWrap) | Sí (6+ veces) | `.d-none` o `.u-hidden` |
| `frontend/customer/reset-password.blade.php:53` | `style="display:none"` (rpStrengthWrap) | Sí | Misma clase |
| `frontend/organizer/signup.blade.php:165` | `style="display:none"` (orgStrengthWrap) | Sí | Misma clase |
| `frontend/customer/dashboard/change-password.blade.php:125` | `style="display:none"` (strengthWrap) | Sí | Misma clase |
| `frontend/check-out.blade.php:476` | `style="display:none"` (couponBody) | Sí | ⚠️ Checkout — solo documentar |
| `frontend/customer/signup.blade.php:212` | `style="display:none"` (suMatchError) | Sí (3+ veces) | `.u-hidden` |
| `frontend/customer/reset-password.blade.php:72` | `style="display:none"` (rpMatchError) | Sí | Misma clase |
| `backend/mercadopago/diagnostico.blade.php:125,148` | `style="display:none"` (result divs) | Sí | `.d-none` |

**Recomendación**: Crear clase utilitaria `.u-hidden { display: none !important; }` en `style.css` y reemplazar las ~12 ocurrencias no-checkout. El `!important` asegura que no haya conflicto con estilos que muestren el elemento vía JS.

### 8.3 Tabla de quick wins — estilos estáticos repetidos

| Style inline | Ocurrencias | Archivos | Migrable a clase |
|-------------|-------------|----------|-----------------|
| `style="font-size:13px"` | 6 | varios | `.text-13` o similar |
| `style="text-align:right"` | 6 | varios | Ya existe `.text-right` de Bootstrap |
| `style="text-align:center"` | 3 | invoice, payment | Ya existe `.text-center` de Bootstrap |
| `style="color:#FF0000"` | 4 | varios | `.text-danger` |
| `style="color:#1DB954"` | 4 | varios | `.text-success` |
| `style="font-family: Arial, sans-serif"` | 5 | varios | Ya debería heredar de `body` |
| `style="font-family: sans-serif"` | 10 | emails | ❌ Email — no tocar |

**Recomendación**: Donde ya existen clases Bootstrap equivalentes, simplemente borrar el `style=""` y usar la clase. Esto aplica a ~15 ocurrencias sin riesgo.

### 8.4 Inline styles en checkout/booking/pagos — SOLO DOCUMENTAR

| Archivo:línea | Style inline | Tipo | Riesgo si se migra |
|---------------|-------------|------|-------------------|
| `frontend/check-out.blade.php:250` | MercadoPago logo width | Estático | BAJO (es solo un logo) |
| `frontend/check-out.blade.php:425` | `font-size:12px` en del | Estático | BAJO (pero está dentro de la zona roja checkout) |
| `frontend/check-out.blade.php:476` | `display:none` en couponBody | JS-controlled | **ALTO** — toggleado por JS, NO MIGRAR |
| `frontend/check-out.blade.php:519` | SVG text VISA | Estático decorativo | BAJO |
| `frontend/check-out.blade.php:534` | MercadoPago logo height | Estático | BAJO |
| `frontend/event/event-details.blade.php:1000` | Meta Pixel noscript | Dinámico | ❌ NO MIGRAR (tracking) |
| `frontend/customer/dashboard/booking/details.blade.php:196` | `margin-left:3px` en SVG | Estático | BAJO |

**Stop condition activa**: `#couponBody` con `style="display:none"` en checkout es controlado por JavaScript (`co-coupon__body`). Migrar a clase CSS podría romper el toggle si el JS usa `element.style.display`. **NO MIGRAR sin auditar el JS asociado.**

### 8.5 Inline styles en emails — NO MIGRAR

Los 3 archivos de email (`customer_verification.blade.php`, `event_confirmation.blade.php`, `arca_invoice.blade.php`) contienen ~90 inline styles. Esto es **correcto y necesario**: los clientes de correo (Gmail, Outlook) ignoran CSS externo y solo respetan estilos inline. Cero acción requerida aquí.

### 8.6 Inline styles en PDF — NO MIGRAR

`pdf/arca_invoice.blade.php` (8 inline styles): DOMPDF requiere estilos inline para renderizar correctamente. Cero acción requerida.

### 8.7 Inline styles en template-view — NO MIGRAR

`backend/template-view/index.blade.php` (259 inline styles): Es un builder de templates de email. Los estilos `mso-*` son específicos de Microsoft Outlook. Cero acción requerida.

---

## 9. Riesgos probables — Tarea 1

### R1: Consolidar overrides mobile puede exponer reglas non-media que antes estaban "ocultas"

Si se mueven todos los overrides mobile al final del archivo, algunas reglas non-media que estaban *entre* bloques @media (ej: líneas 3888-3918) ahora serán pisadas por el bloque consolidado. Esto es **deseado** (ese es el objetivo), pero puede cambiar visualmente secciones que antes funcionaban "de casualidad" por el orden actual.

**Mitigación**: QA visual exhaustivo en 360px, 420px, 575px, 767px, 991px antes de deploy.

### R2: Los dos modelos de `.ev-card` en mobile (grid vs flex)

Consolidar los overrides de `.ev-card` en un solo bloque mobile va a requerir **elegir uno de los dos modelos** (grid horizontal de L3228 vs flex vertical de L4404). Esto no es solo consolidar valores — es una decisión de diseño.

**Recomendación**: Usar el modelo grid (L3228-3460) que es más completo y específico. El modelo flex (L4404-4548) parece ser un parche parcial posterior.

---

## 10. Deuda técnica

### D1: home.css necesita un refactor estructural (NO en este sprint)

4,572 líneas con 42 bloques @media entrelazados y dos sistemas visuales compitiendo es insostenible a mediano plazo. Debería considerarse partir en:
- `home-base.css` (reglas non-media + variables)
- `home-responsive.css` (todos los @media consolidados)

Pero esto es un proyecto separado, no parte de este sprint.

### D2: `display:none` inline es un antipatrón

Usar `style="display:none"` directamente en el HTML es válido como estado inicial, pero el toggle por JS debería usar clases (`.is-hidden`, `.d-none`). Actualmente el código mezcla ambos enfoques.

### D3: SVG inline con estilos en checkout

`check-out.blade.php:519` tiene un `<text>` SVG inline con atributos de presentación. Esto es válido pero inconsistente con el resto del diseño.

---

## 11. Mejoras opcionales

1. **Crear `home-responsive.css` separado**: Todos los bloques @media de home.css en un archivo dedicado que cargue al final. Esto es más limpio que el bloque único propuesto y permite mantener las reglas non-media intactas.

2. **Variable CSS para `--home-section-space`**: Ya existe `--home-section-space-mobile`. Extender a `--home-section-space-tablet` y `--home-section-space-desktop` evitaría la repetición de valores.

3. **Auditar `responsive.css`**: Tiene solo 10 bloques @media pero pisa algunos selectores de home.css. Considerar si realmente se necesita o si sus reglas deberían consolidarse en los archivos específicos.

---

## 12. Datos no verificables

- **Impacto visual real de la consolidación**: Sin un entorno de desarrollo con datos reales, no se puede verificar qué regla está "ganando" visualmente en este momento. Se necesita QA en browser real.
- **`home.css` línea 1023**: El bloque `max-width: 767px` que empieza en 1023 parece ser parte del diseño original (pre-Tangerine). No está claro si las reglas Tangerine (L2120+) lo pisan completamente o solo parcialmente.
- **`responsive.min.css`**: Existe pero no se auditó (es minificado). Debería ser idéntico a `responsive.css`.

---

## 13. Stop conditions detectadas

| # | Condición | Tipo | Acción |
|---|-----------|------|--------|
| SC1 | `#couponBody` display:none en checkout es toggleado por JS | Inline style checkout | **NO MIGRAR**. Documentar para auditar el JS asociado. |
| SC2 | `.ev-card` tiene dos modelos de layout incompatibles en 767px | Conflicto estructural | **NO CONSOLIDAR sin decidir cuál modelo prevalece.** El Ejecutor debe elegir uno. |
| SC3 | `responsive.css` pisa `.hero-content h1` con `font-size: 38px` mientras home.css usa `36px` y `34px` | Conflicto cross-file | **NO MODIFICAR responsive.css** en este sprint. Consolidar solo dentro de home.css. |

---

## 14. Qué NO tocar

- ❌ `responsive.css` — tiene sus propias reglas mobile que afectan otras páginas. Modificarlo podría romper event details, about, etc.
- ❌ `style.css` — base del sistema. Cualquier cambio impacta todo el sitio.
- ❌ Inline styles en `check-out.blade.php` línea 476 (`#couponBody`) — toggleado por JS.
- ❌ Inline styles en `emails/`, `pdf/`, `template-view/` — necesarios para su contexto de renderizado.
- ❌ Inline styles dinámicos con `{{ }}` — dependen del backend.
- ❌ Reglas non-media de home.css — solo consolidar los bloques @media mobile, no tocar las reglas base.

---

## 15. Recomendación mínima segura

### Tarea 1 — Mobile duplicado

**Acción más segura**: Crear **un solo bloque `@media (max-width: 767px)` al final de home.css** (línea ~4572) que consolide SOLO los overrides de spacing conflictivos (padding-top, padding-bottom, margin-top, margin-bottom). No tocar las reglas originales — el nuevo bloque gana por cascada (igual especificidad, último en el archivo).

**Selectores a incluir en el bloque consolidado** (solo spacing):
- `body.home-page .hero-collage-section` → `padding-top: 42px !important; padding-bottom: 38px !important`
- `body.home-page .events-marquee` → `padding-top: 26px; padding-bottom: 14px`
- `body.home-page .hs-search-wrap` → `padding-top: 16px; padding-bottom: 20px`
- `body.home-page .events-section` → `padding-top: 26px; padding-bottom: var(--home-section-space-mobile)`
- `body.home-page .category-section, .about-section, .feature-section, .testimonial-section, .client-logo-area` → `padding-top: var(--home-section-space-mobile); padding-bottom: var(--home-section-space-mobile)`
- `.ev-card-col` → `margin-bottom: 14px`

**Superficie de cambio**: ~30 líneas añadidas al final del archivo. Rollback trivial (borrar el bloque).

### Tarea 2 — Inline styles

**Acción más segura**: Crear clase utilitaria `.u-hidden { display: none !important; }` en `style.css` y reemplazar SOLO las ocurrencias de `style="display:none"` que NO estén en checkout/emails/template-view. Esto son ~10 reemplazos en ~8 archivos.

**Quick wins adicionales sin riesgo**:
- `style="text-align:right"` → `class="text-right"` (Bootstrap ya existe)
- `style="text-align:center"` → `class="text-center"` (Bootstrap ya existe)
- `style="color:#FF0000"` → `class="text-danger"` (Bootstrap ya existe)
- `style="color:#1DB954"` → `class="text-success"` (Bootstrap ya existe)

Estos son reemplazos mecánicos de `style="X"` por `class="Y"` donde la clase YA EXISTE en Bootstrap 4.

---

## 16. Checklist de validación post-implementación

- [ ] `php -l` sobre todos los archivos Blade modificados
- [ ] `git diff --stat` limpio, solo archivos autorizados
- [ ] Home page en 360px: verificar spacing consistente entre secciones
- [ ] Home page en 575px: ídem
- [ ] Home page en 767px: ídem
- [ ] Home page en 991px: ídem
- [ ] Home page en 1200px+: sin cambios visuales
- [ ] Event cards en home: layout correcto en todos los breakpoints
- [ ] Hero section: padding, tipografía, botones correctos
- [ ] Marquee de eventos: width y spacing correcto
- [ ] Password strength meter: se oculta/muestra correctamente
- [ ] Error messages (signup, reset password): se ocultan/muestran correctamente
- [ ] Coupon body en checkout: se oculta/muestra correctamente (NO TOCADO)
- [ ] `npm run production` exitoso (si se tocó CSS)

---

## 17. Decisión de avance

**APTO PARA IMPLEMENTACIÓN QUIRÚRGICA**

Ambas tareas son viables con riesgo controlado:
- **Tarea 1**: Riesgo MEDIO. Requiere QA visual. Superficie de cambio mínima (~30 líneas al final de home.css).
- **Tarea 2**: Riesgo BAJO. Quick wins mecánicos. Los inline styles críticos (emails, checkout, dinámicos) están identificados y excluidos.

**Stop conditions activas**: SC1, SC2, SC3 (ver §13). El Ejecutor debe respetarlas.

---

## 18. PROMPT MAESTRO PARA EJECUTOR

---

# PROMPT MAESTRO PARA EJECUTOR — TUKIPASS

## 1. Superpowers obligatorio

- **Skill de implementación**: `frontend-design` o `responsive-design` (para entender el sistema de breakpoints y no romper la cascada).
- **Skill de verificación**: `verification-before-completion` (validar cada cambio antes de declarar "hecho").
- **Si existe skill de debugging**: `systematic-debugging` — invocarla antes de modificar si algo no cuadra.

## 2. Bibliografía obligatoria

- `.opencode/agents/tuki_context.md` §3 (Frontend — Blade, CSS, JS) y §13 (Design System)
- Este informe de auditoría: `docs/audits/home-mobile-inline-audit-2026-06-21.md`

## 3. Objetivo de implementación

**Fase A (Mobile CSS)**: Consolidar overrides de spacing mobile en un solo bloque al final de `home.css`, ganando por cascada sin tocar las reglas originales.

**Fase B (Inline styles)**: Migrar inline styles estáticos y repetidos a clases CSS existentes o nuevas, excluyendo emails, PDFs, template-view, checkout/booking con JS, y dinámicos.

## 4. Contexto validado por auditoría

- `home.css`: 4,572 líneas, 42 bloques @media, 26 mobile. Mismos selectores reciben valores distintos en múltiples bloques del mismo breakpoint.
- `responsive.css`: Existe con 10 bloques @media. **NO TOCAR.**
- `style.css`: 24 bloques @media. **NO TOCAR.**
- 498 inline styles en 48 archivos Blade. ~94 candidatos a migración, ~20 quick wins.

## 5. Archivos autorizados para modificar

### Fase A (Mobile CSS):
- `public/assets/front/css/home.css` — SOLO añadir bloque al final (línea ~4572)
- `public/assets/front/css/home.min.css` — regenerar con `npm run production` tras el cambio

### Fase B (Inline styles — QUICK WINS):
- `public/assets/front/css/style.css` — añadir clase `.u-hidden`
- `resources/views/frontend/customer/signup.blade.php`
- `resources/views/frontend/customer/reset-password.blade.php`
- `resources/views/frontend/customer/dashboard/change-password.blade.php`
- `resources/views/frontend/organizer/signup.blade.php`
- `resources/views/frontend/customer/dashboard/support_ticket/create.blade.php`
- `resources/views/frontend/customer/dashboard/support_ticket/messages.blade.php`
- `resources/views/frontend/customer/dashboard/booking/details.blade.php`

Si se necesita otro archivo: **DETENERSE y pedir confirmación.**

## 6. Archivos prohibidos

- ❌ `.env`, `config/*`, `routes/*`
- ❌ `public/assets/front/css/responsive.css` — NO TOCAR
- ❌ `public/assets/front/css/style.css` — SOLO añadir `.u-hidden`, no modificar reglas existentes
- ❌ `resources/views/emails/**` — NO TOCAR (email deliverability)
- ❌ `resources/views/pdf/**` — NO TOCAR (DOMPDF rendering)
- ❌ `resources/views/backend/template-view/**` — NO TOCAR (email builder)
- ❌ `resources/views/frontend/check-out.blade.php` — NO TOCAR (zona roja checkout)
- ❌ `resources/views/payments/**` — NO TOCAR
- ❌ Cualquier archivo con inline styles dinámicos (`{{ }}`) — NO TOCAR
- ❌ `resources/views/frontend/payment/success.blade.php` — NO TOCAR (tiene Meta Pixel y estilos de confirmación, riesgo medio)

## 7. Cambios requeridos

### Fase A — Bloque de overrides mobile consolidado

Añadir al final de `home.css` (después de la línea 4572):

```css
/* ================================================
   MOBILE OVERRIDES CONSOLIDADOS (2026-06-21)
   Gana por cascada — último en el archivo.
   NO modificar las reglas originales arriba.
   ================================================ */

@media (max-width: 767px) {
  /* Hero section — unified spacing */
  body.home-page .hero-collage-section,
  body.home-page .hero-collage-section--premium {
    padding-top: 42px !important;
    padding-bottom: 38px !important;
    min-height: clamp(430px, 78svh, 486px) !important;
  }

  body.home-page .hero-content-wrapper {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
  }

  body.home-page #heroHeadingHome,
  body.home-page .hero-content--premium h1 {
    font-size: 36px;
    line-height: 1.04;
  }

  body.home-page .hero-content .hero-lede,
  body.home-page .hero-content p.hero-lede {
    font-size: 15px;
  }

  body.home-page .hero-actions {
    gap: 8px;
    margin-top: 16px !important;
  }

  body.home-page .hero-slideshow {
    width: calc(100% - 28px);
    margin-top: 26px;
  }

  /* Home sections — unified vertical rhythm */
  body.home-page .events-marquee {
    padding-top: 26px !important;
    padding-bottom: 14px !important;
  }

  body.home-page .hs-search-wrap,
  body.home-page .events-section,
  body.home-page .category-section,
  body.home-page .about-section,
  body.home-page .feature-section,
  body.home-page .testimonial-section,
  body.home-page .client-logo-area {
    padding-top: var(--home-section-space-mobile, 48px);
    padding-bottom: var(--home-section-space-mobile, 48px);
  }

  body.home-page .hs-search-wrap {
    padding-bottom: 20px;
  }

  body.home-page .events-section {
    padding-top: 26px;
  }

  body.home-page .hs-search-form {
    margin-bottom: 12px;
  }

  /* Section headers */
  body.home-page .hs-header,
  body.home-page .hs-search-head {
    align-items: flex-start;
  }

  body.home-page .hs-header__title,
  body.home-page .hs-search-head__title,
  body.home-page .section-title h2 {
    font-size: 24px;
  }

  /* Event cards — unified grid layout (horizontal compact) */
  body.home-page .events-section .ev-card-col,
  body.events-page .ev-listing__inner .ev-card-col {
    margin-bottom: 14px;
  }

  body.home-page .events-section .ev-card,
  body.events-page .ev-listing__inner .ev-card {
    --ev-mobile-media: clamp(118px, 36vw, 150px);
    --ev-ticket-height: 38px;
    display: grid;
    grid-template-columns: var(--ev-mobile-media) minmax(0, 1fr);
    grid-template-rows: minmax(8px, 1fr) auto var(--ev-ticket-height) minmax(8px, 1fr);
    column-gap: 11px;
    gap: 8px 11px;
    align-items: start;
    min-height: var(--ev-mobile-media);
    height: auto;
  }

  /* Event card visual — takes left column, full height */
  body.home-page .events-section .ev-card__visual,
  body.events-page .ev-listing__inner .ev-card__visual {
    grid-column: 1;
    grid-row: 1 / 5;
    width: var(--ev-mobile-media);
    height: var(--ev-mobile-media);
    min-height: 0;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
  }

  body.home-page .events-section .ev-card__body-panel,
  body.events-page .ev-listing__inner .ev-card__body-panel {
    grid-column: 2;
    grid-row: 2;
    align-self: start;
    width: auto;
    max-width: calc(100vw - var(--ev-mobile-media) - 64px);
    min-width: 0;
    margin: 0;
    border: 0;
    overflow: hidden;
  }

  body.home-page .events-section .ev-card__body,
  body.events-page .ev-listing__inner .ev-card__body {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
    padding: 0;
    overflow: hidden;
  }

  body.home-page .events-section .ev-card__datetime-bar,
  body.events-page .ev-listing__inner .ev-card__datetime-bar {
    grid-column: 2;
    grid-row: 3;
    justify-self: start;
    width: auto;
    height: var(--ev-ticket-height);
    min-height: var(--ev-ticket-height);
    max-height: var(--ev-ticket-height);
    margin: 0;
  }

  /* Event card title */
  body.home-page .events-section .ev-card__title,
  body.events-page .ev-listing__inner .ev-card__title {
    width: 100%;
    min-width: 0;
    min-height: 0;
    max-height: none;
    margin-bottom: 0;
    color: #333333;
    font-size: clamp(14px, 3.7vw, 15px);
    font-weight: 700;
    line-height: 1.1;
    letter-spacing: 0;
    -webkit-line-clamp: 3;
    text-wrap: auto;
  }

  /* Event card location row */
  body.home-page .events-section .ev-card__loc-row,
  body.events-page .ev-listing__inner .ev-card__loc-row,
  body.home-page .events-section .ev-card__loc-row span,
  body.events-page .ev-listing__inner .ev-card__loc-row span {
    font-size: 10px;
  }

  /* Event card CTA arrow */
  body.home-page .events-section .ev-card__dtbar-cta,
  body.home-page .events-section .ev-card:hover .ev-card__dtbar-cta,
  body.home-page .events-section .ev-card:focus-within .ev-card__dtbar-cta,
  body.events-page .ev-listing__inner .ev-card__dtbar-cta,
  body.events-page .ev-listing__inner .ev-card:hover .ev-card__dtbar-cta,
  body.events-page .ev-listing__inner .ev-card:focus-within .ev-card__dtbar-cta {
    position: static;
    display: flex;
    flex: 0 0 32px;
    align-items: center;
    justify-content: flex-end;
    width: 32px;
    height: 32px;
    margin-left: auto;
    opacity: 1;
    color: var(--tk-primary, #e05d38);
    pointer-events: none;
  }

  body.home-page .events-section .ev-card__dtbar-cta-arrow,
  body.events-page .ev-listing__inner .ev-card__dtbar-cta-arrow {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    border-radius: 999px;
    background: rgba(224, 93, 56, 0.10);
    color: var(--tk-primary, #e05d38);
  }

  /* Marquee items */
  body.home-page .events-marquee-item {
    width: 278px;
    height: 184px;
  }
}

@media (max-width: 575px) {
  body.home-page .events-marquee-item {
    width: 250px;
    height: 170px;
  }

  body.home-page .hero-content--premium h1,
  body.home-page #heroHeadingHome {
    font-size: 34px;
    line-height: 1.04;
  }

  .ev-card__datetime-bar .ev-card__img-day {
    font-size: 30px;
  }
}

@media (max-width: 360px) {
  body.home-page .hero-collage-section,
  body.home-page .hero-collage-section--premium {
    min-height: clamp(410px, 76svh, 458px) !important;
    padding-top: 38px !important;
    padding-bottom: 30px !important;
  }

  body.home-page .events-section .ev-card,
  body.events-page .ev-listing__inner .ev-card {
    --ev-mobile-media: 112px;
    --ev-ticket-height: 36px;
  }

  body.home-page .events-marquee-item {
    width: 230px;
    height: 152px;
  }
}
```

### Fase B — Quick wins inline styles

#### B1: Añadir clase utilitaria en `style.css`

Al final del archivo, añadir:
```css
/* Utility: force hide (for JS-controlled elements initialized hidden) */
.u-hidden { display: none !important; }
```

#### B2: Reemplazar `style="display:none"` por `class="u-hidden"`

En cada uno de estos archivos, reemplazar EXACTAMENTE:

| Archivo | Línea | Cambio |
|---------|-------|--------|
| `frontend/customer/signup.blade.php` | 194 | `style="display:none"` → añadir `class="u-hidden"` (ya tiene `class="cp-strength-wrap"`) |
| `frontend/customer/signup.blade.php` | 212 | `style="display:none"` → añadir `class="u-hidden"` (ya tiene `class="ep-field__error"`) |
| `frontend/customer/reset-password.blade.php` | 53 | `style="display:none"` → añadir `class="u-hidden"` |
| `frontend/customer/reset-password.blade.php` | 72 | `style="display:none"` → añadir `class="u-hidden"` |
| `frontend/organizer/signup.blade.php` | 165 | `style="display:none"` → añadir `class="u-hidden"` |
| `frontend/organizer/signup.blade.php` | 183 | `style="display:none"` → añadir `class="u-hidden"` |
| `frontend/customer/dashboard/change-password.blade.php` | 125 | `style="display:none"` → añadir `class="u-hidden"` |

#### B3: Reemplazar estilos con clases Bootstrap existentes

| Archivo | Línea | Cambio |
|---------|-------|--------|
| `frontend/customer/dashboard/support_ticket/messages.blade.php` | 46 | `style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"` → `class="d-flex align-items-center" style="gap:10px;flex-wrap:wrap"` (el `gap` y `flex-wrap` requieren style inline o clase custom) |
| `frontend/customer/dashboard/support_ticket/create.blade.php` | 39 | `style="display:flex;align-items:center;gap:10px"` → `class="d-flex align-items-center" style="gap:10px"` |
| `frontend/customer/dashboard/change-password.blade.php` | 43 | `style="display:flex;align-items:center;gap:10px"` → `class="d-flex align-items-center" style="gap:10px"` |
| `frontend/customer/dashboard/booking/details.blade.php` | 202 | `style="align-items:flex-start"` → `class="align-items-start"` (ya tiene `class="cd-info-row"`) |
| `frontend/organizer/login.blade.php` | 112 | `style="gap: 10px"` → añadir a clase existente (necesita clase custom para `gap`, Bootstrap 4 no tiene) |

**NOTA**: `gap` no tiene clase utilitaria en Bootstrap 4. Para esos casos, mantener el `style="gap:10px"` inline o crear clase `.u-gap-10 { gap: 10px; }`. Recomendación: mantener el style inline para `gap` en este sprint — es una propiedad moderna que requiere CSS Grid/Flexbox y Bootstrap 4 no la soporta nativamente.

## 8. Restricciones técnicas

- No agregar dependencias npm/composer.
- No cambiar el stack (Bootstrap 4, jQuery, Laravel Mix).
- No modificar `responsive.css`, `style.css` (salvo añadir `.u-hidden`).
- No tocar reglas originales de home.css — solo añadir al final.
- No tocar inline styles en emails, PDFs, template-view, checkout, o dinámicos.
- Mantener español rioplatense en cualquier texto visible.
- Si un `style="display:none"` está en un archivo de checkout/booking: **SALTARLO**.

## 9. Plan de implementación

1. **Verificar estado**: `pwd`, `git status --short`, `git diff --stat`
2. **Leer archivos autorizados**: Confirmar que las líneas indicadas en este informe coinciden con el código real.
3. **Fase A**: Añadir bloque de overrides mobile al final de `home.css`.
4. **Validar Fase A**: `wc -l public/assets/front/css/home.css` (debe ser ~4572 + líneas añadidas).
5. **Fase B1**: Añadir `.u-hidden` al final de `style.css`.
6. **Fase B2**: Reemplazar `style="display:none"` por `class="u-hidden"` en los 7 archivos listados.
7. **Fase B3**: Reemplazar estilos inline por clases Bootstrap en los archivos listados (solo donde la clase equivalente existe).
8. **Validar sintaxis**: `php -l` sobre cada archivo Blade modificado.
9. **Mostrar diff**: `git diff --stat` y `git diff` de cada archivo.
10. **NO ejecutar `npm run production`** a menos que se pida explícitamente.

## 10. Comandos permitidos

```bash
pwd
git status --short
git diff --stat
git diff -- path/autorizado
php -l archivo.php
wc -l public/assets/front/css/home.css
```

## 11. Comandos prohibidos

```bash
git checkout, git reset, git clean, rm -rf
php artisan migrate, php artisan db:seed
php artisan queue:work, php artisan schedule:run
php artisan config:cache, php artisan optimize
composer install, composer update
npm install, npm run production (sin autorización explícita)
```

## 12. Validación esperada

- [ ] `git diff --stat` muestra solo archivos autorizados
- [ ] `php -l` exitoso en todos los Blade modificados
- [ ] `home.css` tiene el bloque consolidado al final, sin modificar reglas anteriores
- [ ] `style.css` tiene `.u-hidden` al final
- [ ] Los `style="display:none"` reemplazados usan `class="u-hidden"`, no perdieron otras clases
- [ ] Ningún archivo de checkout, emails, PDF, o template-view fue modificado
- [ ] Ningún inline style dinámico (`{{ }}`) fue tocado

## 13. Rollback

```bash
git checkout -- public/assets/front/css/home.css
git checkout -- public/assets/front/css/style.css
git checkout -- resources/views/frontend/customer/signup.blade.php
git checkout -- resources/views/frontend/customer/reset-password.blade.php
git checkout -- resources/views/frontend/customer/dashboard/change-password.blade.php
git checkout -- resources/views/frontend/organizer/signup.blade.php
git checkout -- resources/views/frontend/customer/dashboard/support_ticket/create.blade.php
git checkout -- resources/views/frontend/customer/dashboard/support_ticket/messages.blade.php
git checkout -- resources/views/frontend/customer/dashboard/booking/details.blade.php
```

## 14. Stop conditions

Detenerse si:
- El código real no coincide con las líneas indicadas en este informe → reportar discrepancia.
- Un `style="display:none"` está en un archivo NO listado en §7 → no migrar sin consultar.
- El bloque consolidado de home.css rompe la sintaxis (`php -l` no aplica a CSS, pero verificar llaves balanceadas).
- Aparece una dependencia inesperada o hace falta modificar `responsive.css`.
- Cualquier cambio involucra `check-out.blade.php`, `emails/`, `pdf/`, `template-view/`, `payments/`.
- El diff toca más líneas de las autorizadas.

## 15. Entrega final del Ejecutor

- Resumen de cambios: "Fase A: X líneas añadidas a home.css. Fase B: Y archivos modificados, Z inline styles migrados."
- Archivos modificados (lista exacta).
- Diff resumido (output REAL de `git diff --stat`).
- Pruebas ejecutadas: `php -l` sobre cada Blade.
- Pruebas no ejecutadas: QA visual (requiere entorno con datos).
- Riesgos restantes: SC2 (modelo de ev-card requiere confirmación visual), SC3 (responsive.css pisa algunos selectores).
- Checklist final (§16 del informe de auditoría).
- Recomendación go/no-go.

---

**Fin del Prompt Maestro.**
