# Hero home — imágenes de campaña (Tukipass)

## Dónde van los archivos

Colocá aquí **todas** las imágenes de campaña que quieras: el sitio **las detecta solo** (`.jpg`, `.jpeg`, `.png`, `.webp`).

El orden de rotación es el **orden alfabético natural** por nombre de archivo (ej. `01-conciertos.jpg` antes que `02-teatro.jpg`). No hace falta tocar `config/hero.php`.

Usá **JPG o WebP** exportados con las medidas de abajo.

## Medidas exactas para diseño

El hero usa **`background-size: cover`** y el slide tiene un margen interno de ~20px para parallax, así que el arte debe ser **un poco más ancho** que el viewport.

| Uso | Ancho × alto (px) | Notas |
|-----|-------------------|--------|
| **Desktop (referencia)** | **1920 × 440** | Coincide con `min-height` del hero en CSS. |
| **Retina / pantallas grandes** | **3840 × 880** | Export @2x desde el mismo diseño. |
| **Móvil** | El bloque pasa a `min-height: 500px`; `cover` recorta arriba/abajo. Diseñá **zona segura** de contenido importante en el **centro horizontal** y **tercio medio vertical** para que no se corte en ningún ancho. |

### Proporción recomendada

- Ratio aproximado **24:5,5** (1920:440). Si exportás **1920 × 500** o **1920 × 560**, tenés más aire para recortes en móvil sin perder la composición.

### Peso y formato

- Preferí **WebP** (calidad 80–85 %) o JPG optimizado.
- Objetivo **&lt; 350 KB** por slide en desktop; **&lt; 200 KB** si podés.

### Contenido visual

- El overlay oscuro (`rgba(30, 37, 50, 0.78)`) aclara el texto blanco encima: **no dependas de mucho contraste en la parte central** si querés que la foto “respire”.

## Rotación en la home

En `HomeController::buildHeroHomeSlideUrls()` el orden es:

1. Primera imagen de campaña  
2. Primera foto real de galería de eventos  
3. Segunda imagen de campaña  
4. Segunda foto de evento  
5. … hasta agotar una de las dos listas; después sigue solo la que quede.

Las fotos de eventos se toman de `assets/admin/img/event-gallery/` (imágenes existentes en disco), más recientes primero.
