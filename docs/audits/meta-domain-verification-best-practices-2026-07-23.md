# Meta Domain Verification y buenas practicas de sharing - Tukipass

Fecha: 2026-07-23

## Alcance

Auditoria y plan pendiente para todo el sitio publico de Tukipass:

- Home
- Listado de eventos
- Detalle de evento
- Blog y detalle de blog
- Paginas legales y paginas custom
- Organizadores y perfiles publicos
- Shop publico
- Login/registro publico

Esto no es una tarea aislada de organizadores. Afecta a todo enlace publico compartido en Facebook, Instagram, Messenger, WhatsApp, anuncios Meta y crawlers de Meta AI.

## Documentacion revisada

- Adjuntos `Verifying your Domain`.
- Documentacion de Meta Web Crawlers.
- Guia de Facebook Sharing for Webmasters.
- Best Practices para websites/mobile sharing.
- Ad Link Editing.

## Requisitos y buenas practicas clave

- Verificar `tukipass.com` en Meta Business Manager para demostrar propiedad del dominio.
- Hacer la verificacion desde el Business Manager propietario real del dominio. Si una agencia necesita acceso, se comparte luego desde ese Business Manager.
- Usar un solo metodo de verificacion:
  - DNS TXT.
  - Archivo HTML en la raiz publica.
  - Meta tag en el `<head>` de la home.
- Dejar el metodo elegido activo de forma permanente, porque Meta puede revalidar periodicamente.
- Asociar las Facebook Pages oficiales al dominio verificado.
- Configurar `Domain Access` / Ad Link Editing para controlar que terceros no puedan editar titulo, descripcion o imagen de anuncios que apunten a Tukipass.
- Mantener Open Graph en HTML inicial, sin depender de JavaScript.
- Servir paginas con `gzip` o `deflate`.
- Usar `og:image`, `og:image:width` y `og:image:height` para que Meta pueda renderizar preview desde el primer scrape.
- Usar imagenes al menos `600x315`; recomendado `1200x630` y ratio cercano a `1.91:1`.
- Pre-cachear URLs en Sharing Debugger despues de deploy o cambio de imagen.
- Usar URL nueva/versionada para imagenes actualizadas; Meta cachea por URL.
- Revisar trafico movil desde Facebook usando referer `facebook.com` y user agents `FB_IAB/FB4A` y `FBAN/FBIOS` si se implementa atribucion avanzada.

## Estado tecnico local

Ya existe soporte tecnico para verificacion por meta tag:

- `config/services.php` lee `services.facebook.domain_verification`.
- `.env.example` contiene `FACEBOOK_DOMAIN_VERIFICATION=`.
- `resources/views/frontend/layout.blade.php` emite `<meta name="facebook-domain-verification">` cuando el valor existe.

Ya existe soporte tecnico de previews en todo el sitio publico:

- `prefix="og: https://ogp.me/ns#"` en el layout.
- Tags OG/Twitter globales.
- Fallback global de descripcion.
- Fallback global de imagen institucional `1200x630`.
- `og:image:url`, `og:image:secure_url`, `og:image:width`, `og:image:height`, `og:image:type`, `og:image:alt`.
- Imagen institucional versionada con `?v=filemtime`.
- Generador de imagen social para eventos en `1200x630`.
- `robots.txt` permite `facebookexternalhit`, `Facebot`, `meta-webindexer`, `meta-externalads`, `meta-externalfetcher`.
- `robots.txt` bloquea `meta-externalagent` para entrenamiento de modelos.

## Validacion ejecutada

Auditoria local de implementacion:

- Layout OG/Twitter completo.
- Imagen institucional: JPEG `1200x630`, 45.459 bytes, menor a 8 MB.
- Cero referencias `og:image` a logo, breadcrumb o `assets/admin/img`.
- 39 vistas frontend que extienden `frontend.layout` cubiertas por fallback global.
- Politica Meta crawler presente en `robots.txt`.
- `EventSocialImage` genera JPEG `1200x630` menor a 8 MB.

Auditoria de produccion con `facebookexternalhit` sobre las 17 URLs del sitemap:

- 17/17 URLs respondieron.
- 17/17 respuestas llegaron comprimidas y compatibles con `curl --compressed`.
- 0/17 URLs limpias en produccion antes de deploy porque produccion todavia sirve el codigo anterior.
- Las incidencias de produccion detectadas son: falta de prefix OGP, dimensiones declaradas distintas a las reales, imagenes breadcrumb `1600x1067`, logos `250x49` y evento con imagen vertical.

## Pendiente operativo obligatorio

1. Entrar a Meta Business Manager del propietario de Tukipass.
2. Ir a `Business Settings > Brand Safety > Domains`.
3. Agregar `tukipass.com`.
4. Elegir un solo metodo de verificacion.
5. Preferencia recomendada: DNS TXT, porque no depende del deploy del sitio.
6. Si se usa meta tag, copiar el token en `FACEBOOK_DOMAIN_VERIFICATION` y desplegar.
7. Si se usa HTML file upload, subir el archivo exacto de Meta a la raiz publica y dejarlo permanente.
8. Click en `Verify` cuando DNS/deploy haya propagado.
9. Asignar Facebook Pages oficiales al dominio verificado.
10. En `Domain Access`, restringir Ad Link Editing:
    - Opcion recomendada: permitir solo Pages oficiales de Tukipass y aliados autorizados.
    - Revisar Pages que actualmente crean anuncios hacia `tukipass.com`.
11. Despues del deploy, pasar por Sharing Debugger:
    - `https://www.tukipass.com/`
    - `https://www.tukipass.com/eventos`
    - 2-3 eventos activos reales
    - `https://www.tukipass.com/blog`
    - paginas legales principales
    - 2-3 perfiles publicos de organizadores
12. Usar `Scrape Again` para pre-cachear cambios y refrescar previews.

## Decisiones pendientes

- Definir Business Manager propietario del dominio.
- Definir metodo de verificacion final: DNS TXT, meta tag o HTML file.
- Obtener token/registro exacto desde Meta.
- Definir Pages oficiales autorizadas para editar links de anuncios.
- Definir si terceros organizadores podran editar previews en anuncios propios o si todo queda restringido a Tukipass.
