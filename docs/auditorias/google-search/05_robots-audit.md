# 05 · Robots.txt Audit — TukiPass

**Fuente:** https://www.tukipass.com/robots.txt (público) + `public/robots.txt` (servido vía `routes/web.php:19-24`, `text/plain`, cache 300s).

## Estado

- Sintaxis válida (RFC 9309), UTF-8, sin wildcards malformados, ≤500 KiB.
- **Googlebot** (grupo "IA y buscadores"): `Allow: /`, `Allow: /organizer/details/`, Disallow de paneles/checkout/factura, **`Sitemap:` declarados** (sitemap.xml + sitemap-images.xml).
- **Entrenamiento bloqueado**: GPTBot, ClaudeBot, Google-Extended, meta-externalagent → `Disallow: /`.
- Orden: el grupo Googlebot aparece antes que `User-agent: *` → las reglas de Googlebot son las efectivas.

## Hallazgos

| ID | Sev | Hallazgo | Impacto |
|---|---|---|---|
| GS-P0-02 | 🔴 P0 | **`Allow: /organizer/details/`** en TODOS los grupos → el perfil fantasma `?admin=true` y todos los organizadores son rastreables (incluso suspended, y los que devuelven 404 noindex) | Googlebot rastrea superficie de perfiles sin gate |
| GS-P2-11 | 🟡 P2 | `/recuperar-contrasena`, `/login`, `/registro` no bloqueados → rastreo de auth (18 impresiones GSC) | Crawl waste + páginas sin valor SEO |

## Recomendación (per Google docs: robots ≠ noindex)

- **No** usar robots.txt para desindexar el perfil admin (ya devuelve 200 indexable): hay que
  **eliminar/404 + noindex** en la página (X-Robots-Tag o meta). robots.txt solo limita rastreo.
- Revisar si `/organizer/details/` debe seguir abierto tras el fix de organizadores (los públicos
  SÍ deben rastrearse; los privados/suspended deben devolver 404/noindex, no robots-block).
- `Disallow: /organizer/` ya cubre el panel; `/organizer/details/` es la excepción deliberada.
