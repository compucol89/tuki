# Validación SEO, IA y Meta en producción

Fecha: 2026-07-23
Dominio: https://www.tukipass.com
Perfil usado como prueba: https://www.tukipass.com/organizer/details/29/Rumba-Colombiana

## Documentación usada

- `docs/docs-google-search/04-supervisar-depurar/01-search-console-start.md`
- `docs/docs-google-search/02-posicionamiento-aparicion/30-resultados-enriquecidos.md`
- `docs/docs-google-search/03-datos-estructurados/16-evento.md`
- `docs/docs-google-search/03-datos-estructurados/25-perfil.md`
- `docs/ai-llm-seo/chapters/04-llms-txt.md`
- `docs/ai-llm-seo/chapters/06-robots-txt-sitemap.md`
- `docs/bots-seo-ia/robots-txt-reference.md`
- `docs/meta-webmasters/01-open-graph-markup.md`
- `docs/meta-webmasters/02-testing-markup.md`
- `docs/metapixel/test-events-tool.md`
- `docs/metapixel/05-standard-events.md`
- `docs/metapixel/value-currency.md`

## Estado público actual

Comando ejecutado:

```bash
node scripts/validate-production-seo-meta.js
```

Resultado contra producción:

- 24 `PASS`
- 6 `WARN`
- 15 `FAIL`

Producción todavía no tiene el deploy SEO/IA/Meta completo de esta rama.

## Producción: OK

- `/`, `/eventos` y el perfil de Rumba Colombiana responden `200`.
- `/`, `/eventos` y el perfil probado no tienen `noindex`.
- `sitemap.xml` responde `200`, es XML válido e incluye `/eventos` y el perfil probado.
- El perfil tiene canonical, `og:url`, `og:type`, `og:title`, `og:image` y `twitter:card`.
- El user-agent de Facebook recibe `200`, Open Graph y respuesta comprimida con `gzip`.

## Producción: bloqueantes antes de cerrar

- `robots.txt` no declara `sitemap-images.xml`.
- `robots.txt` no tiene la política nueva para `OAI-SearchBot`.
- `robots.txt` no bloquea `GPTBot` para entrenamiento.
- `/sitemap-images.xml` responde `404`.
- `/llms.txt` responde `404`.
- `/llms-full.txt` responde `404`.
- El perfil probado no publica `og:description`.
- El perfil probado no incluye `ProfilePage` ni `ItemList` en JSON-LD.

## Producción: pendiente por datos del organizador

- Meta Pixel se valida con un perfil cuyo organizador haya cargado su propio `meta_pixel_id`.
- Rumba Colombiana no tiene Pixel propio cargado hoy; eso no es un fallo de Tukipass ni debe resolverse con un Pixel global de la plataforma.

## Comparación local

Comando ejecutado:

```bash
node scripts/validate-production-seo-meta.js http://localhost:8801
```

Resultado local sin exigir Pixel propio:

- 37 `PASS`
- 8 `WARN`
- 0 `FAIL`

La rama local ya contiene:

- `/llms.txt`
- `/llms-full.txt`
- `/sitemap-images.xml`
- `robots.txt` con bloques IA
- `ProfilePage`
- `ItemList`
- `BreadcrumbList`
- Open Graph completo en el perfil

Las advertencias locales de Meta Pixel no son fallos de plataforma: Rumba Colombiana no tiene cargado un Pixel propio en su perfil. Tukipass no debe usar un Pixel global para perfiles de organizador; cada organizador debe guardar su propio `meta_pixel_id` para medir su audiencia y sus conversiones.

Modo estricto para un perfil que sí debería tener Pixel propio:

```bash
TUKIPASS_EXPECT_ORGANIZER_PIXEL=1 TUKIPASS_PROFILE_PATH=/organizer/details/ID/slug-del-organizador node scripts/validate-production-seo-meta.js
```

## Corte de herramientas externas

Validado contra las guías adjuntas de Search Console, resultados enriquecidos de Google, Open Graph, Facebook Crawler y Meta Events Manager.

URLs usadas:

- Perfil: `https://www.tukipass.com/organizer/details/29/Rumba-Colombiana`
- Evento real: `https://www.tukipass.com/reggaeton-old-school/123`

Estado actual:

- Search Console: `sitemap.xml` responde `200` e incluye `/eventos`, el evento real y el perfil probado. `sitemap-images.xml` responde `404` en producción, así que no se puede cerrar ni enviar todavía.
- Rich Results Test: el evento real tiene JSON-LD `Event`, `Place`, `PostalAddress`, `Offer` y `BreadcrumbList` en producción. El perfil todavía no se puede cerrar porque producción aún no tiene `ProfilePage` ni `ItemList`.
- Meta Sharing Debugger: el crawler de Facebook recibe `200`, Open Graph y `gzip` en perfil y evento. El evento tiene `og:description` y `og:image` de evento; el perfil aún no tiene `og:description` y usa el logo como `og:image`, así que no conviene hacer "Scrape Again" del perfil antes del deploy.
- Meta Events Manager: no se puede cerrar con Rumba Colombiana porque ese organizador no cargó Pixel propio. La prueba debe hacerse con un perfil cuyo organizador ya tenga `meta_pixel_id`; Tukipass no debe usar un Pixel global para perfiles.
- Acceso externo: Search Console y Meta Events Manager requieren sesión y permisos de las cuentas de Google/Meta. Aunque haya sesión, este corte no se debe cerrar hasta que producción tenga deployado `sitemap-images.xml`, `ProfilePage`, `ItemList` y el perfil elegido tenga Pixel propio.

## Checklist post-deploy

1. Correr validación pública:

```bash
node scripts/validate-production-seo-meta.js
```

2. Para probar otro perfil real cuyo organizador ya cargó su propio Pixel ID:

```bash
TUKIPASS_EXPECT_ORGANIZER_PIXEL=1 TUKIPASS_PROFILE_PATH=/organizer/details/ID/slug-del-organizador node scripts/validate-production-seo-meta.js
```

3. Search Console:

- Confirmar propiedad de dominio `tukipass.com` verificada por DNS.
- Enviar `https://www.tukipass.com/sitemap.xml`.
- Enviar `https://www.tukipass.com/sitemap-images.xml`.
- Inspeccionar y solicitar indexación de:
  - `https://www.tukipass.com/`
  - `https://www.tukipass.com/eventos`
  - `https://www.tukipass.com/organizer/details/29/Rumba-Colombiana`
  - una URL real de evento vigente

4. Rich Results Test:

- Probar el perfil de organizador.
- Probar al menos una página de evento vigente.
- En eventos, validar `Event`.
- En perfiles, validar que Google detecte `ProfilePage`, `Organization`, `BreadcrumbList` e inventario de eventos enlazados.

URL rápida para el perfil:

```text
https://search.google.com/test/rich-results?url=https%3A%2F%2Fwww.tukipass.com%2Forganizer%2Fdetails%2F29%2FRumba-Colombiana
```

5. Meta Sharing Debugger:

- Probar el perfil.
- Revisar preview, `og:title`, `og:description`, `og:image`, canonical y warnings.
- Usar "Scrape Again" después del deploy.

URL rápida:

```text
https://developers.facebook.com/tools/debug/?q=https%3A%2F%2Fwww.tukipass.com%2Forganizer%2Fdetails%2F29%2FRumba-Colombiana
```

6. Meta Events Manager:

- Ir a `https://business.facebook.com/events_manager2/list`.
- Elegir el dataset/pixel propio del organizador que se va a probar.
- Abrir "Probar eventos".
- Ingresar la URL del perfil real cuyo organizador cargó su `meta_pixel_id`.
- Abrir el sitio desde esa herramienta.
- Esperar `PageView` y `ViewContent`.
- Hacer clic en contacto/compartir si aplica.
- Confirmar `Contact`.
- Revisar que los eventos tengan `eventID` para deduplicación futura con Conversion API.
- No cerrar esta prueba usando un Pixel de Tukipass para perfiles de organizador.

## Criterio de cierre

No cerrar la validación hasta que:

- `node scripts/validate-production-seo-meta.js` termine sin `FAIL`.
- Search Console acepte ambos sitemaps.
- Rich Results Test no marque errores críticos en eventos vigentes.
- Meta Sharing Debugger no marque errores críticos de Open Graph.
- Events Manager reciba eventos del navegador para un perfil real con Pixel propio del organizador activo.
