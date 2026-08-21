# Production Data Policy — TukiPass

> Versión: 1.0 · 2026-08-21 · Estado: vigente

## Separación de entornos

```text
production
staging
development
test/fixture/seed
```

- Los datos de cada entorno **no se mezclan**. Las cuentas, eventos, organizadores y
  bookings de prueba **nunca** llegan a producción.
- Seeds/fixtures E2E se ejecutan únicamente con `APP_ENV=testing` (o entorno aislado) y
  nunca contra la BD de producción.
- Dump de producción **no** se importa a desarrollo para testear (riesgo de fuga de datos
  personales de clientes).

## Production Data Integrity Tests (organizadores y contenido público)

Para toda entidad pública (organizador, evento, blog) un test de integridad debe verificar
las condiciones de publicación:

```text
organizador público:
    name != patrón de test (ej. "test", "prueba", "demo", "asd", userN aleatorios)
    slug válido
    profile/public = true
    active = true
    approved = true
    no soft-deleted
    cuenta de propietario válida
```

```text
evento público:
    published = true
    fecha válida (no caducado si el negocio lo requiere)
    organizador publicable
    imagen/título no placeholder
    no soft-deleted
```

```text
post de blog público:
    existe en blog_informations con el idioma activo
    NO tiene columnas de publicación (status/published_at/softDeletes) —
    el schema real solo tiene image y serial_number (verificado en tukipass.sql:649-720)
    no es un post demo (slugs del seed original)
```

**Regla de oro:** el contador visible de una categoría/sección debe ser igual a la cantidad
de elementos que devuelve la **misma política de publicación** subyacente. Si el front
muestra 6 y la query real devuelve 0, es un bug de inconsistencia de filtros — no se parchea
el número, se corrige la política.

**Definición de organizador "listable" (decisión 2026-08-21, F-005):**
relajación del gate `listable()` — se elimina el requisito de "evento publicado ya
realizado". Condiciones vigentes:
- email verificado (`email_verified_at NOT NULL`)
- foto (`photo NOT NULL`) y portada (`cover_photo NOT NULL`)
- al menos una red/sitio (website|instagram|tiktok|facebook|twitter|linkedin)
- `organizer_info`: name NOT NULL y != username, `details >= 80` chars, ubicación NOT NULL
- NO soft-deleted (cuando aplique)

## Basura y cuentas fantasma

- Revisión periódica (script + reporte) de organizadores/cuentas inactivas o con datos de
  prueba (`test@`, dominios de ejemplo, nombres generados).
- La limpieza de datos existentes en producción requiere aprobación explícita y backup.

## Qué documentar

- Cada claim de estadística: ver `content-integrity-policy.md` (owner + query + fecha).
- Cada seed: etiquetado con entorno de destino y contenido de prueba claramente marcado.
