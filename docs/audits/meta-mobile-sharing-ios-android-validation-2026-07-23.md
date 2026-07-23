# Meta mobile sharing iOS/Android - validacion Tukipass

Fecha: 2026-07-23

## Alcance

Validacion de la documentacion adjunta de Meta para:

- Sharing on iOS.
- Share Button for iOS.
- Sharing on Android.

El alcance tecnico actual de Tukipass es el sitio web Laravel. No se encontro una app nativa iOS/Android dentro de este repositorio.

## Estado encontrado

- No hay directorios/proyectos nativos `ios/` o `android/`.
- No hay `Info.plist`, `.xcodeproj`, `.xcworkspace`, `AndroidManifest.xml`, `build.gradle`, Swift, Kotlin ni Java de app movil.
- No hay implementacion de Facebook iOS SDK o Android SDK en el repo.
- No hay App Links nativos (`al:ios:*`, `al:android:*`) en el sitio.
- El sitio si tiene Open Graph global, Twitter Card, canonical y previews corregidos localmente en el layout publico.
- Los botones web de compartir encontrados usan URLs de pagina web, no contenido prefijado por SDK nativo.

## Que aplica hoy al sitio web

Para shares desde iOS/Android hacia URLs web de Tukipass, el preview depende de:

- `og:url`
- `og:title`
- `og:description`
- `og:image`
- `og:image:width`
- `og:image:height`
- `og:image:type`
- `og:image:alt`
- `twitter:card`
- Canonical HTTPS
- Respuesta comprimida `gzip`/`deflate`
- Acceso de `facebookexternalhit` y `Facebot`

Esto ya esta cubierto en el codigo local del sitio publico con el trabajo de Open Graph sitewide.

## Correccion aplicada

Se normalizaron los links web de compartir para evitar URLs protocol-relative:

- `resources/views/frontend/shop/details.blade.php`
- `resources/views/frontend/journal/blog-details.blade.php`
- `resources/views/frontend/partials/modals.blade.php`

Ahora los links de Facebook, Twitter/X y LinkedIn usan `https://...` explicito. En shop tambien se agrego `target="_blank"` y `rel="noopener noreferrer"` a Twitter/X y LinkedIn para no sacar al usuario del flujo de compra.

## Que no se debe implementar sin app nativa real

No agregar App Links ni SDK mobile con datos inventados.

Para iOS se requiere:

- Facebook App ID real.
- Facebook SDK for iOS.
- `FBSDKShareKit`.
- Configuracion en `Info.plist`.
- URL scheme / bundle ID validos.
- App Links iOS si se quiere deep linking desde Facebook hacia la app.

Para Android se requiere:

- Facebook App ID real.
- Facebook Sharing SDK dependency.
- Android package name real.
- Key hashes.
- `ContentProvider` en `AndroidManifest.xml`.
- Facebook Activity / package visibility queries si corresponde.
- App Links Android si se quiere deep linking desde Facebook hacia la app.

Sin esos datos, publicar tags `al:ios:*` o `al:android:*` seria incorrecto y podria degradar la experiencia de usuarios moviles.

## Buenas practicas registradas

- Las apps no deben prellenar texto que el usuario no haya escrito.
- Para link sharing nativo, compartir solamente el `contentURL` canonico de Tukipass.
- Las imagenes y descripciones del preview deben venir del Open Graph del sitio, no de parametros de SDK deprecados.
- En iOS/Android, el SDK puede caer a dialogo web si no esta instalada la app nativa de Facebook.
- Para shares de fotos/videos nativos aplican limites propios de SDK:
  - iOS fotos: menor a 12 MB.
  - iOS videos: menor a 50 MB.
  - Android fotos/videos requieren app Facebook segun tipo de contenido.
- Si se crea app nativa, revisar Automatic App Event Logging y documentar si se mantiene activo o se desactiva.

## Pendiente condicional

Solo si Tukipass lanza app movil nativa:

1. Definir si el objetivo es compartir links web o abrir deep links hacia app.
2. Crear/confirmar Facebook App en Meta Developers.
3. Definir Bundle ID iOS y Package Name Android.
4. Obtener App ID, Client Token y key hashes.
5. Implementar SDK de sharing nativo.
6. Implementar App Links reales:
   - `al:ios:url`
   - `al:ios:app_store_id`
   - `al:ios:app_name`
   - `al:android:url`
   - `al:android:package`
   - `al:android:app_name`
7. Validar shares en dispositivos reales, no solo simuladores.
8. Probar links compartidos con Sharing Debugger.

## Validacion ejecutada

- Busqueda de proyectos nativos iOS/Android: sin resultados.
- Busqueda de SDK/App Links existentes: sin implementacion nativa.
- Busqueda de share links web: 3 superficies corregidas a HTTPS.
- Validacion OG sitewide heredada de auditoria Meta Open Graph: 39 vistas frontend cubiertas por layout publico.
