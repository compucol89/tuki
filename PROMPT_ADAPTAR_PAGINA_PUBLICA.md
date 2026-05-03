# Prompt template — adaptar una sola página pública (Codex / agente)

**Uso:** reemplazar los placeholders entre corchetes antes de pegar en el agente. **Una página por tarea.**

**Documento de referencia:** `HOME_DESIGN_SYSTEM.md` (CTA primarios: §7 — patrón `.ed-buy-btn` / `.theme-btn`, sin sombras de color).

## Orden recomendado (bajo → mayor riesgo)

1. Página institucional tipo **“Sobre nosotros”** (`/sobre-nosotros` en este proyecto).
2. **Contacto**, si el formulario es simple.
3. **FAQs**.
4. **Listado de eventos**.
5. **Detalle de evento** (evitar como primera tanda si hay mucha lógica / CTAs de compra alrededor).

## Ejemplo acotado — Sobre nosotros (TukiPass)

Canónica en rutas: **`/sobre-nosotros`** (`route('about')`). Redirect permanente desde **`/about-us`**.

Antes de pegar el bloque `text` en el agente, fijar estos tres datos:

```txt
Página objetivo: /sobre-nosotros (institucional; equivalente a “/about”)
Patrón rg: "sobre-nosotros|about\.blade|HomeController@about|route\('about'\)"
Blade objetivo: resources/views/frontend/about.blade.php
```

Luego sustituir en el prompt: el párrafo “Página objetivo”, el patrón del primer `rg`, y la ruta en el tercer `rg`.

---

```text
Actuá como desarrollador frontend senior Laravel/Blade en el repo TukiPass.

Objetivo:
Adaptar UNA sola página pública de TukiPass al sistema visual documentado en `HOME_DESIGN_SYSTEM.md`, con cambios mínimos, seguros y consistentes.

Página objetivo:
[REEMPLAZAR: una sola URL o nombre, ej. /about, /contact, FAQs, listado de eventos, detalle de evento — ser específico]

IMPORTANTE:
No adaptar varias páginas.
No rediseñar todo el sitio.
No limpiar CSS legacy en esta tarea.
No tocar checkout, pagos, gateways, webhooks, auth, rutas, migraciones, `.env` ni base de datos.

Antes de ejecutar, evaluá si aplica alguna skill disponible del entorno. Si aplica, usala y seguí su workflow. Para UI/frontend considerá `frontend-design`, `responsive-design` o `accessibility`; para Laravel/PHP considerá `laravel-patterns`, `laravel-specialist` o `php-pro`; para bugs considerá `systematic-debugging`; antes de declarar terminado usá `verification-before-completion` si está disponible.

Usá solo las skills relevantes.

Contexto del proyecto:
TukiPass es una plataforma SaaS de gestión de eventos y venta de entradas.

Stack:
- Laravel 12
- PHP 8.2+
- MySQL
- Eloquent
- Blade
- Bootstrap 4
- jQuery
- Laravel Mix 6

Frontend:
- Fuente: Inter únicamente.
- Color principal: `#F97316`.
- Gris oscuro: `#1e2532`.
- Todo texto visible al cliente debe estar en español rioplatense/neutro argentino.
- No introducir Tailwind, React, Vue ni dependencias nuevas.

Documento base obligatorio:
- Leer primero `HOME_DESIGN_SYSTEM.md`.

Notas críticas confirmadas:
- El home usa el ADN visual `TukiPass Warm SaaS Orange`.
- En el código real puede haber diferencias con nombres ideales de la auditoría.
- Respetar las notas de fidelidad al código dentro de `HOME_DESIGN_SYSTEM.md`.
- No asumir que `.btn-theme` o `.btn-outline-theme` existen si el documento indica que son convención pero no están en `style.css`.
- Para cards de eventos, verificar si el patrón real usa `.ev-card` o partial existente antes de crear clases nuevas.
- No crear clases nuevas si se puede reutilizar una clase real existente.

Reglas obligatorias:
1. Usar `rg` antes de leer archivos grandes.
2. Leer `HOME_DESIGN_SYSTEM.md` antes de editar.
3. Identificar la ruta, controlador y Blade exactos de la página objetivo.
4. Leer solo los archivos necesarios.
5. Hacer cambios quirúrgicos.
6. No tocar más de una página pública.
7. No tocar checkout, pagos, gateways, webhooks, auth, guards, migraciones, DB, `.env` ni producción.
8. No agregar dependencias nuevas.
9. No introducir Tailwind, React ni Vue.
10. No hacer refactors globales.
11. No reescribir `style.css` entero.
12. No modificar layout global salvo que sea estrictamente necesario y esté justificado.
13. Respetar cambios existentes del usuario y no revertir trabajo ajeno.

Archivos permitidos para lectura:
- `HOME_DESIGN_SYSTEM.md`
- archivo Blade de la página objetivo
- controlador de la página objetivo, solo lectura
- ruta correspondiente, solo lectura
- `resources/views/frontend/layout.blade.php`, solo lectura
- `resources/views/frontend/partials/styles.blade.php`, solo lectura
- `public/assets/front/css/style.css`, solo lectura o edición mínima si no hay alternativa
- `public/assets/front/css/responsive.css`, solo lectura o edición mínima si no hay alternativa

Archivos permitidos para edición:
- Preferentemente solo la Blade de la página objetivo.
- CSS mínimo en `public/assets/front/css/style.css` solo si es imprescindible y no existe clase reutilizable.
- CSS responsive mínimo en `public/assets/front/css/responsive.css` solo si es imprescindible.

Archivos prohibidos:
- Checkout
- Pagos
- Gateways
- Webhooks
- Auth
- Guards
- Migraciones
- Seeds
- Base de datos
- `.env`
- Producción
- Rutas, salvo lectura
- Controladores, salvo lectura
- Admin dashboard
- Organizer dashboard
- `dashboard.css`
- `organizer.css`
- cualquier archivo no relacionado con la página objetivo

Comandos iniciales obligatorios:

1. Ubicación de la página objetivo (reemplazar el patrón):

rg -n "[PATRÓN_DE_RUTA_O_NOMBRE_DE_PÁGINA]" routes resources app

2. Estilos/componentes reales disponibles:

rg -n "hero-btn|hero-btn--primary|hero-btn--secondary|ev-card|event-card|hs-header|section-title|events-marquee|btn-theme|btn-outline-theme|--primary-color|--heading-color|--tuki-font-sans|--home-section-space" HOME_DESIGN_SYSTEM.md public/assets/front/css/style.css resources/views/frontend

3. Blade objetivo (reemplazar la ruta del archivo):

rg -n "btn|button|card|section|title|container|row|col-|style=|#F97316|#1e2532|font-family|ps-btn|btn-primary" [RUTA_AL_BLADE_OBJETIVO]

Tareas:

A. Diagnóstico inicial
- Identificar qué elementos visuales de la página objetivo están desalineados con el home: botones, títulos, espaciado, cards, colores, tipografía, metadatos, responsive, textos visibles.
- No editar hasta tener claro el alcance.

B. Adaptación visual mínima
- Inter; CTAs con `#F97316` o variable existente; títulos `#1e2532`; metadatos `#6b7280`; radios 8px/12px según corresponda; sombras suaves en cards; grillas Bootstrap 4; espaciado generoso; español rioplatense/neutro argentino.

C. Botones
- Reutilizar clases existentes reales; no inventar `.btn-theme` si no existe; evaluar `.ps-btn` sin romper otras páginas; CSS nuevo mínimo y específico de la página.

D. Cards / bloques
- Listados de eventos: partial / `.ev-card`; no duplicar markup; `object-fit: cover` si aplica; radios y sombras consistentes.

E. Responsive
- Mobile OK; sin CSS incompatible con Bootstrap 4; no romper layout.

F. Contenido
- Texto visible en español; no cambiar contenido funcional sin necesidad; no tocar traducciones globales salvo imprescindible.

Stop conditions (detenerse y reportar sin editar si):
1. No se identifica con certeza la Blade de la página objetivo.
2. La página es checkout, pago, gateway, auth o dashboard sensible.
3. Hace falta tocar rutas, controladores, checkout/pagos/gateways/webhooks, migraciones, DB o `.env`.
4. El cambio requiere reescribir CSS global amplio o hay conflicto fuerte que pide auditoría aparte.
5. Clases compartidas con zonas sensibles y el cambio podría afectarlas.

Verificación obligatoria después de editar:
1. git diff -- [ARCHIVOS_MODIFICADOS]
2. git diff --name-only
3. Confirmar archivos dentro del alcance permitido.
4. Confirmar que no se tocaron: checkout, pagos, gateways, webhooks, auth, guards, rutas, migraciones, DB, `.env`, admin u organizer dashboard.
5. Textos visibles en español; sin inglés innecesario.
6. Si hubo CSS: mínimo, sin redefinir globalmente botones/cards, no romper el home.
7. Indicar cómo validar desktop / tablet / mobile.
8. Usar `verification-before-completion` si está disponible.

Formato del reporte final:
1. Resumen del cambio
2. Página objetivo
3. Archivos modificados
4. Componentes alineados al sistema visual
5. Clases/tokens reutilizados
6. CSS agregado, si hubo
7. Confirmación de exclusiones (checkout, pagos, etc.)
8. Verificaciones ejecutadas y resultado
9. Riesgos remanentes
10. Próximo paso recomendado

Próximo paso recomendado:
Adaptar otra única página pública en una tarea separada, o validar visualmente esta página antes de avanzar.

Recordatorio final:
Una sola página. Cambios mínimos. Leer `HOME_DESIGN_SYSTEM.md`. Respetar fidelidad al código real. Sin dependencias nuevas. Sin Tailwind/React/Vue. Sin refactors globales. Sin zonas sensibles. No revertir trabajo ajeno.
```

---

## Checklist antes de ejecutar

- [ ] Sustituir `[REEMPLAZAR: una sola URL o nombre…]` por la página concreta.
- [ ] Sustituir `[PATRÓN_DE_RUTA_O_NOMBRE_DE_PÁGINA]` en el comando `rg` (ej. `about`, `contact`, `faqs`).
- [ ] Sustituir `[RUTA_AL_BLADE_OBJETIVO]` por la ruta real del `.blade.php`.
