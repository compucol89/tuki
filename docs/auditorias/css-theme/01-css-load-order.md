# 01 — CSS Load Order

**Orden real de carga en el Organizer** (verificado en `resources/views/organizer/partials/styles.blade.php`):

```
1. mix('css/app.css')                       — global (fontsource, tokens base)
2. mix('css/fontawesome.css')               — FA6 self-hosted (SÍNCRONO desde 2026-08-21)
   + preload fa-solid-900.woff2 / fa-brands-400.woff2
3. fontawesome-iconpicker.min.css
4. bootstrap.min.css
5. bootstrap-tagsinput.css · jquery-ui.min.css · jquery.timepicker.min.css
6. bootstrap-datepicker.css · select2.min.css · dropzone.min.css
7. monokai-sublime.css
8. atlantis.css                             — VENDOR (14.692 líneas, 794 !important)
9. admin-main.css                           — Tuki (73 !important)
10. admin-skin.css                          — Tuki tokens + Vendor Overrides (~590 !important)
11. theme-dark.css                          — Tuki dark (226 !important → 1282 líneas)
12. <style> inline del blade (@yield('style'))  — gana por orden a todo lo externo
13. style=""                                — inline attr, gana a todo
```

**Reglas de cascada que gobiernan el proyecto:**
- `!important` ajeno (atlantis) solo se vence con: misma especificidad + `!important` + orden posterior (admin-skin), o mayor especificidad + `!important`.
- Un `<style>` inline de Blade vence a cualquier hoja externa sin `!important` — por eso los blades migrados usan **tokens** (los tokens cambian con `html[data-theme]`, no hacen falta overrides dark por blade).
- `html[data-theme="dark"]` en admin-skin/theme-dark eleva la especificidad por encima de los selectores planos de atlantis.
