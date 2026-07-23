# Deploy seguro: perfil organizador + SEO/IA/Meta

Fecha: 2026-07-23
Objetivo: subir sólo la tanda de perfil público de organizador, builder/admin del perfil, `robots.txt`, `/llms.txt`, `/llms-full.txt`, `sitemap-images.xml`, Open Graph base, JSON-LD y validadores.

## Pathspec seguro

Usar este archivo para preparar staging sin arrastrar cambios ajenos:

```bash
git add --pathspec-from-file=docs/deploy/organizer-seo-meta-core-pathspec-2026-07-23.txt
```

Antes de commitear, revisar lo staged:

```bash
git diff --cached --name-status
git diff --cached --check
```

## Entra en esta tanda

- Migración de organizadores: `cover_photo`, `website`, `instagram`, `tiktok`, `meta_pixel_id`.
- Modelo `Organizer` con fillables nuevos.
- Perfil público del organizador rediseñado.
- Cards de próximos eventos usando el partial del home con fallback de imagen.
- Historial de eventos como lista liviana.
- `ProfilePage`, `Organization`, `ItemList` y `BreadcrumbList` en el perfil.
- Soporte para Pixel propio del organizador en perfil: `PageView`, `ViewContent`, `Contact` con `eventID` cuando el organizador carga su `meta_pixel_id`.
- No se carga ni se recomienda un Pixel de Tukipass para perfiles de organizador.
- Builder del perfil en `/organizer/edit-profile`.
- Empujes directos en dashboard del organizador.
- Diagnóstico compacto del perfil en admin listado/detalle.
- `/llms.txt`, `/llms-full.txt`, `/sitemap-images.xml`.
- `robots.txt` con política IA y sitemaps.
- Open Graph base y default OG image.
- Corrección ya pedida para menú móvil global en frontend.
- Corrección ya pedida para `/eventos` cuando no hay resultados/publicaciones.
- Validador de producción SEO/Meta.

## Revisar aparte

Archivo:

```text
docs/deploy/organizer-seo-meta-review-separately-pathspec-2026-07-23.txt
```

Motivos:

- `PaymentGateway/MercadoPagoController.php` y Shop MercadoPago: no son parte del perfil/SEO. No entran en esta tanda.
- ARCA, PDFs, emails y facturación: otra tanda operativa.
- `payment/success.blade.php`: relacionado con Pixel de compra, pero toca post-compra. Requiere validación propia.
- `event-details.blade.php`, Store/Update request y `EventSocialImage`: evento/Pixel/OG de evento, no perfil. Puede entrar luego como batch Meta Eventos.
- `home/index-v1.blade.php`, blog y shop details: cambian OG de contenido específico a imagen default. No subir sin revisar previews, porque puede bajar calidad de shares.
- `script.js`, `style.css`, partial styles y `/eventos` sí quedan en core porque corrigen problemas ya reportados en esta misma tanda y no tocan checkout ni pagos.

## No entra

- Carpetas de documentación adjunta completas (`docs/ai-llm-seo`, `docs/docs-google-search`, `docs/metapixel`, etc.). Son referencia local, no runtime.
- Cambios de admin general, transacciones, bookings/reportes y skin global.
- `docker-compose.yml`, `.env.example` y ajustes de entorno no necesarios para runtime.
- `routes/admin.php`, porque el diff actual agrega un shortcut de eventos admin no requerido para el perfil.

## Comandos de verificación antes de deploy

```bash
php -l app/Http/Controllers/BackEnd/Organizer/OrganizerController.php
php -l app/Http/Controllers/BackEnd/Organizer/OrganizerManagementController.php
php -l app/Http/Controllers/FrontEnd/OrganizerController.php
php -l app/Http/Controllers/FrontEnd/SitemapController.php
php -l app/Http/Controllers/FrontEnd/AiIndexController.php
php -l app/Http/Controllers/FrontEnd/ImageSitemapController.php
php -l database/migrations/2026_07_22_000001_add_profile_fields_to_organizers_table.php
php -l resources/views/frontend/organizer/details.blade.php
php -l resources/views/organizer/edit-profile.blade.php
php -l resources/views/organizer/index.blade.php
node --check scripts/validate-production-seo-meta.js
node scripts/verify-organizer-profile-static.js
git diff --check -- $(cat docs/deploy/organizer-seo-meta-core-pathspec-2026-07-23.txt)
```

## Comandos post-deploy

```bash
php artisan migrate --force
php artisan optimize:clear
node scripts/validate-production-seo-meta.js
```

Luego cerrar manualmente:

- Search Console: enviar `sitemap.xml` y `sitemap-images.xml`.
- Rich Results Test: perfil de organizador y un evento vigente.
- Meta Sharing Debugger: perfil, "Scrape Again".
- Meta Events Manager: perfil real cuyo organizador haya cargado su Pixel ID propio, validar `PageView`, `ViewContent` y `Contact`.

## Riesgo principal

El pathspec core evita pagos, ARCA y reportes, pero sí incluye la migración de `organizers`. Antes de deployar, confirmar backup de base y que producción tenga permisos de escritura en:

- `public/assets/admin/img/organizer-photo/`
- `public/assets/admin/img/organizer-cover-photo/`
