# Validacion Meta / Open Graph - Tukipass

Fecha: 2026-07-23

## Documentacion revisada

- `docs/meta-webmasters/`
- `docs/ogp/`
- Texto adjunto: `A Guide to Sharing for Webmasters`
- Texto adjunto: `Meta Web Crawlers`

## Requisitos clave

- Tags OGP servidos en HTML inicial, sin depender de JavaScript.
- `og:url` canonica, absoluta y sin parametros de tracking.
- `og:title`, `og:type`, `og:image` y `og:url` presentes.
- `og:description`, `og:site_name` y `og:locale` recomendados.
- Imagen absoluta por HTTPS.
- Imagen recomendada: 1200x630 px, ratio 1.91:1, JPG/PNG preferente.
- Especificar `og:image:width`, `og:image:height`, `og:image:type` y `og:image:alt`.
- Permitir `facebookexternalhit` y `Facebot` en robots/firewall.
- Facebook cachea previews e imagenes por URL; si cambia la imagen, debe cambiar la URL.
- `fb:app_id` es necesario para Facebook Insights.
- La verificacion de dominio de Meta se activa desde Business Manager por DNS, archivo HTML o meta tag.

## Estado encontrado

- Home, `/eventos` y un evento individual ya emiten tags OGP y Twitter Card en HTML server-side.
- Ya existen `og:image:secure_url`, dimensiones, MIME type, alt, `og:locale`, `og:site_name`, canonical y Twitter Card.
- Faltaba `prefix="og: https://ogp.me/ns#"` en `<html>`.
- Faltaba permiso explicito para `facebookexternalhit` y `Facebot` en `robots.txt`.
- El evento probado en produccion usaba imagen social vertical `1092x1440`, no el formato recomendado `1200x630`.
- Home usa actualmente una imagen `.webp` de campana como preview; funciona como preview moderno, pero para maxima compatibilidad Meta conviene usar tarjetas JPG/PNG 1200x630.

## Cambios aplicados

- `resources/views/frontend/layout.blade.php`
  - Agregado `prefix="og: https://ogp.me/ns#"`.
  - Agregado `og:image:url` junto con `og:image` y `og:image:secure_url`.
  - Agregado fallback global de `meta description` / `og:description` para evitar previews sin descripcion.
  - Agregados tags opcionales `fb:app_id` y `facebook-domain-verification` cuando existan en config/env.
  - Agregado fallback global a `assets/front/img/og/tukipass-og.jpg` con `?v=filemtime` para cache de Meta.
- `config/services.php`
  - Agregado `services.facebook.domain_verification`.
- `.env.example`
  - Agregado `FACEBOOK_DOMAIN_VERIFICATION=`.
- `public/robots.txt`
  - Agregado grupo explicito para `facebookexternalhit` y `Facebot`.
  - Agregados `meta-webindexer`, `meta-externalads` y `meta-externalfetcher` para lectura/citas de contenido publico.
  - Bloqueado `meta-externalagent` para entrenamiento de modelos.
- `resources/views/frontend/event/event-details.blade.php`
  - Cambiado `og:type` de evento a `website` para mayor compatibilidad OGP.
  - Fallback de `og:image` cambiado a la imagen institucional 1200x630 cuando no hay imagen de evento valida.
  - El JSON-LD de evento ahora mantiene imagen fallback si no hay imagen social especifica.
- `app/Support/EventSocialImage.php`
  - Las imagenes sociales de eventos ahora se generan como JPEG `1200x630` con recorte centrado.
  - La URL sigue versionada con `?v=` para evitar cache viejo de Meta.
- `public/assets/front/img/og/tukipass-og.jpg`
  - Nueva imagen institucional JPG 1200x630 para home, listados, blog, paginas legales/estaticas, auth, organizadores, tienda y fallback general.
- Vistas actualizadas para usar la imagen institucional:
  - Home, eventos, sobre nosotros, contacto, preguntas frecuentes, blog listado, blog detalle, organizador detalle, login/registro de cliente, login/registro de organizador y detalle de producto.

## Validacion sitewide con Meta crawler

Se probo produccion contra las 17 URLs publicadas en `https://www.tukipass.com/sitemap.xml` usando:

```bash
facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)
Range: bytes=0-524288
Accept-Encoding: gzip/deflate
```

Resultado de produccion antes de deploy:

- Las 17 URLs respondieron y pudieron ser leidas.
- Las 17 respuestas llegaron comprimidas (`gzip`/compatible por `curl --compressed`).
- Todas las URLs todavia muestran incidencias porque produccion aun sirve el codigo anterior.
- Home sirve imagen real `767x440` pero declara `1200x630`.
- Eventos/listados/blog/FAQ/contacto/sobre nosotros sirven breadcrumb `1600x1067` pero declaran `1200x630`.
- Organizadores, paginas legales y algunas estaticas sirven logo `250x49` pero declaran `1200x630`.
- Evento `reggaeton-old-school/123` sirve imagen vertical; localmente el generador ya produce version social `1200x630`.

## Verificacion

- `php -l resources/views/frontend/layout.blade.php`: OK.
- `php -l resources/views/frontend/event/event-details.blade.php`: OK.
- `php -l config/services.php`: OK.
- `php -l app/Support/EventSocialImage.php`: OK.
- `git diff --check` en archivos tocados: OK.
- Prueba aislada de `EventSocialImage`: genero JPEG 1200x630, `image/jpeg`, 76 KB.
- `php -l` en 15 vistas frontend tocadas: OK.
- `identify public/assets/front/img/og/tukipass-og.jpg`: JPEG 1200x630, 48 KB.
- Prueba aislada de `EventSocialImage` con thumbnail vertical `1080x1920`: genero JPEG 1200x630, `image/jpeg`, 204 KB.
- Auditoria local de implementacion:
  - Layout con tags OG/Twitter completos.
  - Imagen institucional `1200x630`, JPEG, 45.459 bytes, menor a 8 MB.
  - Cero referencias `og:image` a logo/breadcrumb/`assets/admin/img`.
  - 39 vistas frontend que extienden `frontend.layout` cubiertas por fallback global.
  - Politica de crawlers Meta presente en `robots.txt`.
  - `EventSocialImage` genera JPEG `1200x630` menor a 8 MB.

## Pendientes post-deploy

1. Purgar cache de `robots.txt`.
2. Probar home, `/eventos`, `/blog`, paginas legales, organizadores y eventos principales en Facebook Sharing Debugger.
3. Hacer "Scrape Again" para eventos con imagen anterior cacheada.
4. Agregar `FACEBOOK_DOMAIN_VERIFICATION` cuando Meta Business Manager entregue el token.
5. Revisar si se define `FACEBOOK_CLIENT_ID` para activar `fb:app_id` en paginas publicas.
