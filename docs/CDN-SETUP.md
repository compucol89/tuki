# CDN Configuration — TukiPass

## Estado Actual

Todos los assets usan el helper `asset()` de Laravel, que respeta la variable `ASSET_URL` en `.env`.

**CDN-ready: ✅ Sí** — Solo necesitas configurar `ASSET_URL` en `.env`.

## Cómo Activar CDN

### 1. Configurar ASSET_URL en .env

```env
# Sin CDN (desarrollo)
ASSET_URL=null

# Con CDN (producción)
ASSET_URL=https://cdn.tukipass.com
```

### 2. Opciones de CDN

| Proveedor | Costo | Setup |
|---|---|---|
| **Cloudflare** | Gratis (plan free) | DNS → Proxy ON |
| **CloudFront (AWS)** | Pay-as-you-go | S3 bucket + Distribution |
| **BunnyCDN** | ~$0.01/GB | Pull zone |
| **DigitalOcean CDN** | ~$0.01/GB | Spaces + CDN |

### 3. Cloudflare (Recomendado — más simple)

1. Agregar dominio a Cloudflare
2. Activar proxy (naranja) en DNS
3. En Rules → Page Rules:
   - `*.tukipass.com/assets/*` → Cache Level: Cache Everything
   - `*.tukipass.com/assets/*` → Edge Cache TTL: 1 month
4. En Speed → Optimization:
   - Auto Minify: CSS, JS
   - Brotli: ON
   - Early Hints: ON

### 4. CloudFront + S3

```bash
# Sync assets to S3
aws s3 sync public/assets/front/ s3://tukipass-cdn/assets/front/ --delete

# Invalidar cache después de deploy
aws cloudfront create-invalidation --distribution-id EXXXXX --paths "/*"
```

### 5. Verificar

```bash
# Sin CDN
curl -I http://localhost:8801/assets/front/css/style.css

# Con CDN
curl -I https://cdn.tukipass.com/assets/front/css/style.css
# Debe mostrar: x-cache: Hit, cache-control: public, immutable
```

## Impacto Esperado

| Métrica | Sin CDN | Con CDN |
|---|---|---|
| TTFB (CSS/JS) | ~200ms | ~20ms |
| Latencia global | Variable | <50ms worldwide |
| Ancho de banda servidor | 100% | ~5% (solo cache miss) |

## Notas

- Los nuevos archivos CSS/JS (home.css, events.css, etc.) se cachean automáticamente
- Para forzar refresh después de cambios, agregar query string: `?v=2`
- `asset()` helper ya soporta CDN — no hay que cambiar vistas
