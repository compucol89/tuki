# Standards — Cómo se aplican los estándares en TukiPass

> Versión: 1.0 · 2026-08-21 · Estado: vigente

## Modelo de 5 niveles

Todo cambio de frontend/calidad debe atravesar esta cadena:

```text
DOCUMENTACIÓN OFICIAL        /docs/reference/      (source of truth, no editar)
        ↓  qué dice el estándar
POLÍTICA TUKIPASS            /docs/tukipass/        (cómo lo aplicamos)
        ↓
IMPLEMENTACIÓN               código
        ↓
TESTS                        demostramos que funciona
```

**Regla:** ningún estándar se cita "de memoria". Toda decisión de a11y/SEO/contenido debe
referenciar un archivo de `/docs/reference/`. Si la referencia contradice un hábito del
proyecto, gana la referencia (con waiver documentado).

## Stack congelado (no actualizar durante remediación)

| Capa | Versión | Evidencia |
|------|---------|-----------|
| Laravel | 12.x | composer.json |
| PHP | ^8.2 | composer.json |
| Laravel Mix | 6.x (`.version()` activo) | webpack.mix.js, mix-manifest.json |
| Bootstrap frontend | 4.5.3 | public/assets/front/ |
| Bootstrap admin/organizer | 4.3.1 | public/assets/admin/ |
| PHPUnit | 11 | composer.json |
| Playwright/Axe | ausentes (Fase 4) | — |

**No actualizar Bootstrap, Laravel Mix, jQuery, Popper, Laravel ni ninguna dependencia como
parte de la remediación, salvo hallazgo específico que lo requiera. Primero corregir el sistema
actual. Una migración de framework es otro proyecto.**

## Zonas de código

| Zona | Permiso |
|------|---------|
| `FrontEnd\CheckOutController@checkout2`, `FrontEnd\Event\BookingController`, `FrontEnd\PaymentGateway\*` | **NO MUTATE PAYMENT STATE** — lectura permitida, modificación prohibida |
| Zona checkout/pagos (vistas) | Auditoría pasiva permitida: render, labels, teclado, contraste, console errors. Nunca crear/confirmar pagos en tests |
| `config/auth.php` (guards) | No tocar |
| Campos HTML protegidos (`name="event_id"`, `pricing_type`, `quantity`, `date_type`, `event_date`, `data-price`, `data-stock`, `data-ticket_id`, `#total_price`, `#total`, `recalcTotal()`) | No tocar |
| Resto del código | Modificación mínima y trazable, sin refactors |

## Reglas generales de edición

1. Sin comentarios nuevos salvo necesidad funcional.
2. Diffs mínimos, sin reformatear archivos completos.
3. Cada cambio debe poder revertirse por separado.
4. Todo cambio de comportamiento requiere test (RED → GREEN).
