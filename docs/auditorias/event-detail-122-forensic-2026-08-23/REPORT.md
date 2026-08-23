# TukiPass Event Detail 122 - Forensic UI/UX Audit & Remediation

Fecha de auditoria: 2026-08-23
Pagina objetivo: `https://www.tukipass.com/colombia-vs-suiza-por-gol-caracol/122`
Implementacion local verificada: `http://127.0.0.1:8801/colombia-vs-suiza-por-gol-caracol/122`

## Alcance

Se trabajo sobre la pagina real Laravel:

- Ruta: `/{slug}/{id}` -> `FrontEnd\EventController@details`
- Vista: `resources/views/frontend/event/event-details.blade.php`
- CSS fuente: `public/assets/front/css/event-detail.css`
- CSS produccion: `public/assets/front/css/event-detail.min.css`

No se modificaron checkout, gateways, calculo de precios, stock, campos hidden, `data-*` criticos ni `recalcTotal()`.

## Evidencia

Capturas guardadas:

- `screenshots/production/*.png`: baseline de produccion actual.
- `screenshots/local/*.png`: local corregido, con imagenes faltantes interceptadas desde produccion.

Mediciones:

- `measurements.json`
- `detect/layout-files.json`

El scanner URL de `detect.mjs` no se ejecuto porque el repo no tiene `puppeteer` instalado. No se instalo para evitar cambios de dependencias. La verificacion renderizada se hizo con Playwright.

## Hallazgos Corregidos

| Area | Antes en produccion | Despues local | Veredicto |
| --- | --- | --- | --- |
| Ciclo de vida | `Finalizado` convivia con `Venta online activa` | Disponibilidad muestra `Evento finalizado` / `Venta finalizada` | PASS |
| Conversion | Habia urgencia activa: `Reservá con anticipacion`, `Se esta moviendo`, movimientos recientes | Se suprime en eventos finalizados | PASS |
| Actividad reciente | `609/610 movimientos recientes` sinteticos | Solo actividad real 24h, oculta si terminado o bajo umbral; se retiro la funcion privada sin uso que generaba señales sinteticas | PASS |
| Barra mobile | En `320x568`: `Entradas desde FREE PASS` + `Evento finalizado` | `Estado del evento` + `Venta finalizada` | PASS |
| Entradas gratis agotadas | `AGOTADAS` estaba embebido en el titulo | Titulo limpio + badge `Agotadas`; `FREE PASS` queda como precio | PASS |
| Subtotal | Mostraba subtotal de compra aunque CTA estuviera deshabilitada | Muestra estado de venta; subtotal visual oculto en finalizado | PASS |
| SEO | Meta custom podia conservar `Reserva en Tukipass` en evento terminado | Meta/OG/Twitter fuerzan `evento finalizado` y `Venta online cerrada` cuando el ciclo cerro; canonical/JSON-LD siguen validos | PASS |

## Viewports

Todos los viewports medidos quedaron sin overflow horizontal.

| Viewport | Produccion forbidden copy | Local forbidden copy | Local lifecycle visible |
| --- | --- | --- | --- |
| 1440x900 | true | false | true |
| 1024x768 | true | false | true |
| 768x1024 | true | false | true |
| 390x844 | true | false | true |
| 360x800 | true | false | true |
| 320x568 | true | false | true |

## Veredictos

- Geometry: PASS. No overflow horizontal en 1440, 1024, 768, 390, 360 ni 320.
- Density: PASS. La tarjeta de compra conserva densidad de lectura; el estado finalizado no agrega bloques pesados.
- Rhythm: PASS. Se mantiene el ritmo del bloque de compra; el total cerrado no empuja CTA ni lista de entradas.
- Public parity: PASS con salvedad. Local usa imagenes interceptadas desde produccion porque faltan assets locales.
- Conversion: PASS en superficies de plataforma. Evento finalizado ya no comunica venta activa ni urgencia en hero, disponibilidad, compra, total ni barra mobile.
- Mobile: PASS. Barra mobile refleja estado finalizado cuando es visible.
- Accessibility: PASS en la spec de la ficha, incluyendo Axe. El CTA principal es `disabled`; la barra mobile usa `aria-disabled`.
- Data integrity: PASS LOCAL / PENDIENTE PRODUCCION. La descripcion local fue corregida a `Martes 7 de julio`; el evento canonico empieza el martes 2026-07-07 a las 16:00 ART. Queda pendiente limpieza editorial del WYSIWYG en produccion.
- Global readiness: PASS condicionado. La ficha esta lista; `npm run test:a11y` sigue fallando en paginas ajenas por contraste global de botones naranja.

## Correccion de Contenido Aplicada Localmente

Se aplico solo en la DB local Docker. No se modifico produccion ni datos desde codigo.

```sql
UPDATE event_contents
SET description = REPLACE(description, 'Lunes 7 de julio', 'Martes 7 de julio')
WHERE event_id = 122
  AND description LIKE '%Lunes 7 de julio%';
```

Dato canonico verificado:

- `events.id = 122`
- `start_date = 2026-07-07`
- `start_time = 16:00`
- `end_date = 2026-07-08`
- `end_time = 01:00`
- Zona: America/Argentina/Buenos_Aires

Verificacion local:

- SQL posterior: `has_lunes = false`, `has_martes = true`.
- Playwright: `.ed-info-card--description .summernote-content` contiene `Martes 7 de julio` y no contiene `Lunes 7 de julio`.

## Pendientes Editoriales de Produccion

No se reescribio contenido productivo desde codigo. La descripcion WYSIWYG de `event_contents.description` todavia contiene la frase historica `Reserva tu entrada ahora en Tukipass.`. Recomendacion admin/DB para produccion: quitar esa linea o reemplazarla por una frase informativa, por ejemplo `La venta online ya finalizo. Informacion del evento disponible para consulta.`

La DB tambien tiene `event_contents.meta_description` con `Reserva en Tukipass.`. El render publico ya lo sobrescribe para eventos finalizados, pero conviene actualizar el dato fuente en produccion para coherencia editorial.

## Verificacion Ejecutada

- `node scripts/build-front-assets.js`: PASS
- `npx playwright test tests/playwright/event-detail-forensic.spec.js`: PASS, 6/6
- `npm run test:theme`: PASS, 5 passed / 28 skipped
- `npm run test:seo`: PASS, 4/4
- HTML real ficha 122: PASS, `meta[name=description]`, `og:description` y `twitter:description` dicen `evento finalizado` + `Venta online cerrada`
- `npm run test:a11y`: FAIL ajeno al cambio, 5 paginas publicas con contraste global `#e05d38` sobre blanco
- `node .agents/skills/impeccable/scripts/detect.mjs --json --scope layout resources/views/frontend/event/event-details.blade.php public/assets/front/css/event-detail.css`: PASS, `[]`

## Nota de Build

`node scripts/build-front-assets.js` compila todo el paquete frontal. Por eso tambien aparecen cambios en `public/assets/front/css/style.min.css` y `public/assets/front/css/responsive.min.css`, aunque sus fuentes no se modificaron en esta tarea. El cambio funcional de esta ficha esta en `event-detail.css` y `event-detail.min.css`.
