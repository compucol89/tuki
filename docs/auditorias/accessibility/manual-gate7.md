# GATE 7 — Evaluación manual de accesibilidad (procedimiento obligatorio)

> **GATE 7** cierra el hueco de las reglas automatizadas: AXE PASS ≠ WCAG PASS.
> Los tests automatizados (`@a11y`, `@aria`) detectan un subconjunto de WCAG 2.1 A/AA;
> lo que no se puede automatizar se evalúa manualmente y se documenta aquí.
> (Ver `docs/reference/playwright/accessibility-testing.md`).

## 1. Cuándo se ejecuta

- **Obligatorio** antes de cada release que toque UI pública o del panel organizador.
- **Obligatorio** al agregar un componente interactivo nuevo (acordeones, modales, menús, selects, sliders).
- **Recomendado** al cambiar layout, navegación o formularios.

## 2. Alcance

| Superficie | Páginas | Evaluador |
|-----------|---------|-----------|
| Públicas | Home, Eventos, Evento detalle, Checkout, Blog, FAQ, Contacto, Sobre nosotros, Login/Registro/Recuperar contraseña | Frontend dev |
| Panel organizador | Login, Dashboard, Gestión de eventos, Reservas, Ajustes | Organizer dev |
| Transaccional | Checkout completo + confirmación post-compra | Backend dev + QA |

## 3. Checklist manual (WCAG 2.1 A/AA) — marcar cada ítem

### 3.1 Teclado (2.1.1, 2.1.2)
- [ ] Toda acción es operable solo con teclado (Tab/Shift+Tab/Enter/Escape/Arrow).
- [ ] No hay trampas de foco (focus queda atrapado sin escape).
- [ ] El orden de tabulación sigue el orden visual (2.4.3).

### 3.2 Foco visible (2.4.7)
- [ ] El indicador de foco es claramente visible en todos los elementos interactivos.
- [ ] No se removió el outline sin alternativa visible.

### 3.3 Semántica y landmarks (1.3.1)
- [ ] Un solo `<h1>` por página; jerarquía de headings sin saltos.
- [ ] Landmarks: header, nav, main, footer presentes y únicos.
- [ ] Listas reales (`ul/ol`) donde hay listas visuales.

### 3.4 Formularios (1.3.1, 3.3.1, 3.3.2)
- [ ] Todo campo tiene `<label>` asociado (no solo placeholder).
- [ ] Los errores se anuncian y se asocian al campo (`aria-describedby`).
- [ ] Mensajes de error indican qué corregir (3.3.3).

### 3.5 Imágenes y multimedia (1.1.1)
- [ ] `alt` descriptivo en imágenes informativas; `alt=""` en decorativas.
- [ ] Los íconos de acción tienen nombre accesible (aria-label/texto).

### 3.6 Contraste (1.4.3)
- [ ] Texto ≥ 4.5:1; texto grande ≥ 3:1 (verificar con axe color-contrast en estados hover/active/focus).

### 3.7 Contenido en movimiento (2.2.2)
- [ ] Sliders/animaciones tienen control de pausa o no superan 5 s de auto-avance.

### 3.8 Estado y notificaciones (4.1.3)
- [ ] Cambios de estado (toasts, validación, carrito) se anuncian a lectores de pantalla.

## 4. Evidencia mínima

1. Captura del árbol ARIA de una página representativa (Playwright: `page.locator(...).ariaSnapshot()`).
2. Lista de incompletos de Axe (`[incomplete-axe]` del reporte `@a11y`) con decisión por ítem.
3. Checklist 3.1–3.8 marcado, con fecha y firmante.

## 5. Responsable y registro

- **Dueño:** desarrollador a cargo del cambio + 1 reviewer.
- **Registro:** este documento se actualiza con la fecha y el resultado en cada release;
  las no conformidades se anotan como deuda con dueño y fecha límite.

## 6. Cierre

El GATE 7 se considera **aprobado** cuando: checklist completo sin ítems en blanco,
sin violaciones nuevas reportadas por `@a11y`/`@aria`, e incompletos de Axe
revisados y documentados.

---
Última revisión: 2026-08-21 · Estado: vigente
