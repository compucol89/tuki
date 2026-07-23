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

**Recomendación original** (corregida en revisión): Crear clase utilitaria `.u-hidden { display: none; }` **sin `!important`** en `style.css` y reemplazar las ~7 ocurrencias no-checkout. El `!important` rompería el JS que muestra los elementos con `element.style.display = 'flex'/'block'`. Sin `!important`, el style inline del JS gana sobre la clase, igual que antes.

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

## 15. Recomendación mínima segura (corregida post-revisión)

### Tanda 1 — Solo spacing mobile de home

Crear un bloque `@media (max-width: 767px)` + `575px` + `360px` al final de `home.css` que consolide SOLO overrides de spacing (padding-top, padding-bottom, margin). Sin tocar:
- `body.events-page` (impactaría `/eventos` mobile)
- `.ev-card` estructural (grid, datetime-bar, CTA — requiere decisión visual)
- `responsive.css` ni `style.css`

**Superficie de cambio**: ~55 líneas añadidas al final de `home.css`. Rollback: `git checkout -- home.css home.min.css`.

### Tanda 2 — Inline style quick wins

Crear `.u-hidden { display: none; }` **SIN `!important`** (el JS usa `element.style.display` y debe poder pisar la clase). Reemplazar ~7 `style="display:none"` estáticos en signup/reset-password/change-password. Mejoras cosméticas con clases Bootstrap existentes en ~5 archivos más.

**Corrección clave vs versión original**: `!important` en `.u-hidden` rompería el JS que muestra los elementos con `element.style.display = 'flex'`. Sin `!important`, el style inline del JS gana sobre la clase, igual que antes.

---

## 16. Checklist de validación post-implementación

### Tanda 1 — Spacing mobile home

- [ ] `git diff --stat` muestra solo `home.css` y `home.min.css`
- [ ] El diff de `home.css` solo tiene adiciones al final, sin modificar reglas anteriores
- [ ] `grep 'body.events-page' public/assets/front/css/home.css` no devuelve resultados en el bloque nuevo
- [ ] `npm run production` exitoso
- [ ] Home page en 360px: spacing consistente entre secciones
- [ ] Home page en 575px: ídem
- [ ] Home page en 767px: ídem
- [ ] Home page en 991px: ídem
- [ ] Home page en 1200px+: sin cambios visuales
- [ ] Hero section: padding, tipografía, botones correctos
- [ ] Marquee de eventos: width y spacing correcto
- [ ] Event cards en home: layout intacto (solo cambió `margin-bottom` en `.ev-card-col`)

### Tanda 2 — Inline quick wins

- [ ] `php -l` exitoso en todos los Blade modificados
- [ ] `git diff` no muestra `!important` en `.u-hidden`
- [ ] `.u-hidden` en `style.css` es exactamente `.u-hidden { display: none; }`
- [ ] Password strength meter: se oculta al cargar y se muestra al escribir (signup, reset, change-password)
- [ ] Error messages: se ocultan al cargar y se muestran en error (signup, org signup, reset password)
- [ ] Organizer login: layout del alert de "Estás ingresando al panel" intacto
- [ ] Support ticket messages/create: layout intacto
- [ ] Booking details: layout intacto
- [ ] Coupon body en checkout: NO TOCADO, funciona igual

---

## 17. Decisión de avance

**APTO PARA IMPLEMENTACIÓN QUIRÚRGICA — EN DOS TANDAS INDEPENDIENTES**

La auditoría confirma los problemas, pero el primer Prompt Maestro era demasiado agresivo. Correcciones aplicadas:

| Problema detectado | Corrección |
|--------------------|------------|
| `.u-hidden { !important }` rompe JS que usa `element.style.display` | Quitar `!important`. En Tanda 2 se audita si el JS debe cambiar a clases. |
| Bloque consolidado incluía `body.events-page` → impactaba `/eventos` | Quitar. Tanda 1 solo toca `body.home-page`. |
| `.ev-card` estructural (grid, datetime-bar, CTA) no es mecánico | Quitar. Es una decisión visual que requiere screenshot QA. Solo queda spacing. |
| Prompt decía "NO ejecutar npm run production" | Corregir: `npm run production` es **OBLIGATORIO** al final de Tanda 1. Sin minificar, producción no ve el cambio. |

**Tandas**:
- **Tanda 1** (riesgo MEDIO-BAJO): Solo spacing mobile de home. Sin cards. Sin `/eventos`. Sin `responsive.css`. Build obligatorio. ~40 líneas netas al final de `home.css`.
- **Tanda 2** (riesgo BAJO): Inline quick wins con `.u-hidden` sin `!important`. Sin tocar checkout. QA de signup/reset/change-password.

**Stop conditions activas**: SC1, SC2, SC3 (ver §13). El Ejecutor debe respetarlas.

---

## 18. PROMPT MAESTRO PARA EJECUTOR — TUKIPASS (v2 corregida)

---

# TANDA 1 — SPACING MOBILE DE HOME (QUIRÚRGICO)

## 1. Superpowers obligatorio

- `verification-before-completion`: validar cada cambio con `git diff` ANTES de declarar "hecho".
- `frontend-design` o `responsive-design`: entender el sistema de breakpoints y no romper la cascada.

## 2. Bibliografía obligatoria

- `.opencode/agents/tuki_context.md` §3 y §13
- Este informe: `docs/audits/home-mobile-inline-audit-2026-06-21.md` §7 (tabla de conflictos)

## 3. Objetivo

Consolidar SOLO los overrides de **spacing mobile** (padding-top, padding-bottom, margin) en un único bloque al final de `home.css`. El bloque gana por cascada (misma especificidad, último en el archivo) sin tocar las reglas originales.

**Qué NO se toca**:
- ❌ `body.events-page` — no impactar `/eventos` mobile
- ❌ `.ev-card` estructural (grid, datetime-bar, título, CTA) — requiere decisión visual con screenshots
- ❌ `responsive.css`, `style.css`
- ❌ Checkout, emails, PDFs, template-view

## 4. Archivos autorizados

- `public/assets/front/css/home.css` — SOLO añadir al final (línea ~4572)
- `public/assets/front/css/home.min.css` — **OBLIGATORIO regenerar** con `npm run production`

Si se necesita otro archivo: **DETENERSE**.

## 5. Archivos prohibidos

- ❌ `responsive.css`, `style.css`
- ❌ Cualquier Blade, PHP, JS
- ❌ `.env`, `config/*`

## 6. Bloque a añadir al final de `home.css`

```css
/* ================================================
   MOBILE OVERRIDES CONSOLIDADOS — SOLO SPACING (2026-06-21)
   Gana por cascada — último en el archivo.
   NO modificar las reglas originales arriba.
   Solo aplica a body.home-page. No toca /eventos.
   ================================================ */

@media (max-width: 767px) {
  /* ── Hero section: unified padding ── */
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

  /* ── Home sections: unified vertical rhythm ── */
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

  /* ── Section headers ── */
  body.home-page .hs-header,
  body.home-page .hs-search-head {
    align-items: flex-start;
  }

  body.home-page .hs-header__title,
  body.home-page .hs-search-head__title,
  body.home-page .section-title h2 {
    font-size: 24px;
  }

  /* ── Event cards: solo spacing entre columnas ── */
  body.home-page .events-section .ev-card-col {
    margin-bottom: 14px;
  }

  /* ── Marquee items ── */
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
}

@media (max-width: 360px) {
  body.home-page .hero-collage-section,
  body.home-page .hero-collage-section--premium {
    min-height: clamp(410px, 76svh, 458px) !important;
    padding-top: 38px !important;
    padding-bottom: 30px !important;
  }

  body.home-page .events-marquee-item {
    width: 230px;
    height: 152px;
  }
}
```

## 7. Plan de implementación

1. `pwd`, `git status --short`, `git diff --stat`
2. Leer últimas 10 líneas de `home.css` para confirmar punto de inserción
3. Añadir el bloque CSS arriba al final de `home.css`
4. Validar: `wc -l public/assets/front/css/home.css`
5. **`npm run production`** — OBLIGATORIO. Sin esto, `home.min.css` no se actualiza y producción no ve el cambio.
6. `git diff --stat` — debe mostrar solo `home.css` y `home.min.css`
7. `git diff public/assets/front/css/home.css` — verificar que solo se añadió el bloque al final

## 8. Comandos

```bash
# Permitidos
pwd
git status --short
git diff --stat
git diff -- public/assets/front/css/home.css
wc -l public/assets/front/css/home.css
npm run production

# Prohibidos
git checkout, git reset, git clean, rm -rf
php artisan migrate, php artisan db:seed
php artisan queue:work, php artisan schedule:run
php artisan config:cache, php artisan optimize
composer install, composer update
npm install
```

## 9. Validación esperada

- [ ] Solo `home.css` y `home.min.css` modificados
- [ ] Bloque añadido al final, reglas anteriores intactas
- [ ] Sin `body.events-page` en el diff
- [ ] Sin `.ev-card__visual`, `.ev-card__body-panel`, `.ev-card__datetime-bar`, `.ev-card__dtbar-cta` en el diff
- [ ] `npm run production` exitoso
- [ ] `home.min.css` actualizado (timestamp nuevo)

## 10. Rollback

```bash
git checkout -- public/assets/front/css/home.css
git checkout -- public/assets/front/css/home.min.css
```

## 11. Stop conditions

Detenerse si:
- El diff toca algo que no sea `home.css` / `home.min.css`
- El diff muestra `body.events-page`
- El diff muestra reglas `.ev-card` estructurales
- `npm run production` falla
- Las reglas originales de `home.css` fueron modificadas (no solo añadidas al final)

## 12. Entrega Tanda 1

- `git diff --stat`
- `git diff public/assets/front/css/home.css` (solo el bloque añadido)
- Confirmación: `npm run production` exitoso
- Riesgo restante: requiere QA visual en 360/575/767/991 px

---

# TANDA 2 — INLINE STYLES QUICK WINS (INDEPENDIENTE DE TANDA 1)

## 1. Superpowers obligatorio

- `verification-before-completion`
- `laravel-patterns` (para entender el ciclo de vistas Blade)

## 2. Objetivo

Migrar inline styles estáticos repetidos a clases CSS, excluyendo emails, PDFs, template-view, checkout, y dinámicos.

## 3. Corrección crítica vs primera versión del Prompt Maestro

**`.u-hidden` NO lleva `!important`.** El JS de password strength y error messages muestra los elementos con `element.style.display = 'flex'` / `'block'`. Si la clase CSS tiene `!important`, gana sobre el style inline del JS y los elementos quedan permanentemente ocultos.

```css
/* CORRECTO — sin !important */
.u-hidden { display: none; }
```

Si en el futuro se quiere migrar el JS a class toggling (`.classList.add('u-hidden')` / `.classList.remove('u-hidden')`), eso es otra tarea. Por ahora, con `display: none` sin `!important`, el `element.style.display = 'flex'` del JS pisa la clase correctamente.

## 4. Archivos autorizados

- `public/assets/front/css/style.css` — añadir `.u-hidden` al final
- `resources/views/frontend/customer/signup.blade.php` — 2 reemplazos
- `resources/views/frontend/customer/reset-password.blade.php` — 2 reemplazos
- `resources/views/frontend/organizer/signup.blade.php` — 2 reemplazos
- `resources/views/frontend/customer/dashboard/change-password.blade.php` — 1 reemplazo (línea 125) + 1 mejora (línea 43)
- `resources/views/frontend/customer/dashboard/support_ticket/messages.blade.php` — 1 mejora
- `resources/views/frontend/customer/dashboard/support_ticket/create.blade.php` — 1 mejora
- `resources/views/frontend/customer/dashboard/booking/details.blade.php` — 1 mejora

## 5. Archivos prohibidos

- ❌ `check-out.blade.php` — NO TOCAR (especialmente `#couponBody style="display:none"`)
- ❌ `emails/**`, `pdf/**`, `template-view/**`
- ❌ `payments/**`, `payment/success.blade.php`
- ❌ Cualquier archivo con `{{ }}` en el style

## 6. Cambios requeridos

### B1: Añadir en `style.css` (al final)

```css
/* Utility: initial hidden state for JS-controlled elements.
   Sin !important para que element.style.display del JS pueda pisarlo. */
.u-hidden { display: none; }
```

### B2: Reemplazos `style="display:none"` → `class="u-hidden"`

Cada elemento YA tiene una clase existente. Solo se **quita** el `style="display:none"` y se **añade** `u-hidden` a la clase existente.

| Archivo | Línea aprox | Clase existente | Cambio |
|---------|------------|-----------------|--------|
| `frontend/customer/signup.blade.php` | 194 | `cp-strength-wrap` | Quitar `style="display:none"`, añadir `u-hidden` al `class` |
| `frontend/customer/signup.blade.php` | 212 | `ep-field__error` | Quitar `style="display:none"`, añadir `u-hidden` al `class` |
| `frontend/customer/reset-password.blade.php` | 53 | `cp-strength-wrap` | Quitar `style="display:none"`, añadir `u-hidden` al `class` |
| `frontend/customer/reset-password.blade.php` | 72 | `ep-field__error` | Quitar `style="display:none"`, añadir `u-hidden` al `class` |
| `frontend/organizer/signup.blade.php` | 165 | `cp-strength-wrap` | Quitar `style="display:none"`, añadir `u-hidden` al `class` |
| `frontend/organizer/signup.blade.php` | 183 | `ep-field__error` | Quitar `style="display:none"`, añadir `u-hidden` al `class` |
| `frontend/customer/dashboard/change-password.blade.php` | 125 | `cp-strength-wrap` | Quitar `style="display:none"`, añadir `u-hidden` al `class` |

**Ejemplo concreto del cambio**:
```blade
<!-- ANTES -->
<div class="cp-strength-wrap" id="suStrengthWrap" style="display:none;">

<!-- DESPUÉS -->
<div class="cp-strength-wrap u-hidden" id="suStrengthWrap">
```

### B3: Mejoras con clases Bootstrap existentes

| Archivo | Cambio |
|---------|--------|
| `support_ticket/messages.blade.php` L46 | `style="display:flex;align-items:center;gap:10px;flex-wrap:wrap"` → `class="d-flex align-items-center flex-wrap" style="gap:10px"` |
| `support_ticket/create.blade.php` L39 | `style="display:flex;align-items:center;gap:10px"` → `class="d-flex align-items-center" style="gap:10px"` |
| `dashboard/change-password.blade.php` L43 | `style="display:flex;align-items:center;gap:10px"` → `class="d-flex align-items-center" style="gap:10px"` |
| `dashboard/booking/details.blade.php` L202 | `style="align-items:flex-start"` → quitar style, añadir `align-items-start` a la clase `cd-info-row` |
| `organizer/login.blade.php` L112 | `style="gap: 10px"` → mantener inline (Bootstrap 4 no tiene clase para `gap`) |

**Nota sobre `gap`**: Bootstrap 4 no tiene utilitarias `gap-*`. Se mantiene `style="gap:10px"` inline. No vale la pena crear una clase custom para 3 ocurrencias en este sprint.

## 7. Plan de implementación

1. `pwd`, `git status --short`
2. Añadir `.u-hidden` al final de `style.css`
3. Aplicar los 7 reemplazos B2 (display:none → u-hidden)
4. Aplicar los 5 reemplazos B3 (clases Bootstrap)
5. `php -l` sobre cada Blade modificado
6. `git diff --stat` — verificar archivos exactos
7. `git diff` — revisar cada cambio

## 8. Validación esperada

- [ ] `php -l` exitoso en TODOS los Blade modificados
- [ ] `git diff` no muestra `!important`
- [ ] Ningún `check-out.blade.php` modificado
- [ ] Ningún `emails/` o `pdf/` modificado
- [ ] Los `id="..."` de los elementos con `u-hidden` no cambiaron (el JS los referencia por ID)
- [ ] `.u-hidden` en `style.css` no tiene `!important`

## 9. Rollback

```bash
git checkout -- public/assets/front/css/style.css
git checkout -- resources/views/frontend/customer/signup.blade.php
git checkout -- resources/views/frontend/customer/reset-password.blade.php
git checkout -- resources/views/frontend/customer/dashboard/change-password.blade.php
git checkout -- resources/views/frontend/organizer/signup.blade.php
git checkout -- resources/views/frontend/customer/dashboard/support_ticket/create.blade.php
git checkout -- resources/views/frontend/customer/dashboard/support_ticket/messages.blade.php
git checkout -- resources/views/frontend/customer/dashboard/booking/details.blade.php
```

## 10. Stop conditions

Detenerse si:
- El código real no coincide con las líneas indicadas
- Un `style="display:none"` está en checkout o archivo no listado
- `php -l` falla en algún Blade
- El diff muestra `!important` en `.u-hidden`

## 11. Entrega Tanda 2

- `git diff --stat`
- `git diff` completo
- `php -l` output de cada Blade
- Riesgo: testear signup, reset password, change password — los strength meters y error messages deben mostrarse/ocultarse igual que antes

---

**Fin del Prompt Maestro v2.**
