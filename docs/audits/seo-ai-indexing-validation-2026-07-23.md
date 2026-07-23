# Validacion SEO/IA - Tukipass

Fecha: 2026-07-23
Dominio validado: `tukipass.com` / `www.tukipass.com`

## Resultado ejecutivo

- Search Console muestra propiedad verificada por DNS.
- `https://www.tukipass.com/sitemap.xml` esta procesado correctamente y Google descubrio 17 paginas.
- HTTPS esta correcto, sin problemas criticos visibles.
- El host no presenta problemas de rastreo en Search Console.
- Home, `/eventos` y el evento probado `/reggaeton-old-school/123` responden 200, tienen `index,follow`, canonical y JSON-LD.
- Produccion todavia no esta sirviendo la version nueva de `robots.txt`, `/llms.txt` ni `/sitemap-images.xml`.

## Search Console

Datos del informe de rastreo exportado el 2026-07-22:

- Total de solicitudes de rastreo: 1.14 mil.
- Descarga total: 14.6 MB.
- Tiempo medio de respuesta: 446 ms.
- Host `www.tukipass.com`: 995 solicitudes, sin problemas.
- Host `tukipass.com`: 146 solicitudes, sin problemas.
- Respuestas: 72.04% 200, 16.04% 301, 11.39% 404, 0.44% 304, 0.09% 302.
- Finalidad: 93.08% actualizacion, 6.92% deteccion.
- Googlebot: recursos 31.81%, desktop 29.45%, smartphone 21.47%, otros 11.48%, imagen 3.77%, AdsBot 2.02%.

## Produccion validada

- `https://www.tukipass.com/robots.txt`: 200, pero version vieja de 197 bytes con `cache-control: public, max-age=31536000, immutable`.
- `https://www.tukipass.com/sitemap.xml`: 200, XML correcto.
- `https://www.tukipass.com/sitemap-images.xml`: 404.
- `https://www.tukipass.com/llms.txt`: 404.
- `https://www.tukipass.com/`: 200.
- `https://tukipass.com/`: 301 hacia `https://www.tukipass.com/`.
- `https://www.tukipass.com/eventos`: 200.
- `https://www.tukipass.com/reggaeton-old-school/123`: 200.

## Ajustes dejados en repo

- `public/robots.txt` preparado para permitir crawlers de busqueda/IA y bloquear rutas privadas/transaccionales.
- `/llms.txt` y `/llms-full.txt` agregados como mapas para agentes IA.
- `/sitemap-images.xml` agregado para imagenes publicas de eventos, organizadores, blog y tienda.
- `robots.txt`, sitemaps y llms tienen cache corto en `.htaccess`/headers para evitar cache anual.
- `layout.blade.php` expone enlaces alternativos a `llms.txt` y mejora JSON-LD base.

## Pendientes post-deploy

1. Deployar los cambios SEO/IA.
2. Purgar cache de `https://www.tukipass.com/robots.txt`.
3. Validar que `robots.txt` ya no tenga cache anual y que incluya `OAI-SearchBot`.
4. Enviar `https://www.tukipass.com/sitemap-images.xml` en Search Console.
5. Probar `/llms.txt` y `/llms-full.txt` en produccion.
6. Revisar en Search Console o logs el listado de URLs que generan el 11.39% de 404.
7. Pedir nuevo rastreo de home, `/eventos` y eventos principales.

## Verificacion local

- `php -l routes/web.php`: OK.
- `php -l app/Http/Controllers/FrontEnd/SitemapController.php`: OK.
- `php -l app/Http/Controllers/FrontEnd/ImageSitemapController.php`: OK.
- `php -l app/Http/Controllers/FrontEnd/AiIndexController.php`: OK.
- `git diff --check` en archivos SEO: OK.
- `php artisan test tests/Feature/AiIndexFilesTest.php`: pendiente, falta `vendor/autoload.php` en este workspace.
