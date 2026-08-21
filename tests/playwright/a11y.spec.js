// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

/**
 * @a11y — Axe (WCAG 2.1 A/AA) sobre páginas públicas clave.
 *
 * Aserción canónica: violations == [] (docs/reference/playwright/accessibility-testing.md).
 *
 * WAIVERS documentados (regla → motivo → dueño → fecha):
 * - `exclude('.phpdebugbar')`: Laravel Debugbar es una herramienta de desarrollo
 *   (composer require-dev, APP_DEBUG local); no existe en producción.
 * - Reglas automatizadas no detectan todos los problemas (AXE PASS ≠ WCAG PASS):
 *   la evaluación manual queda documentada en docs/auditorias/ (GATE 7).
 */
const PAGES = [
  { name: 'home', path: '/' },
  { name: 'eventos', path: '/eventos' },
  { name: 'blog', path: '/blog' },
  { name: 'contacto', path: '/contacto' },
  { name: 'sobre-nosotros', path: '/sobre-nosotros' },
  { name: 'organizadores', path: '/organizadores' },
];

for (const p of PAGES) {
  test(`@a11y ${p.name} sin violaciones axe (wcag2a + wcag2aa)`, async ({ page }) => {
    await page.goto(p.path, { waitUntil: 'networkidle' });
    await page.waitForTimeout(300);

    const results = await new AxeBuilder({ page })
      .exclude('.phpdebugbar')
      .withTags(['wcag2a', 'wcag2aa'])
      .analyze();

    expect(
      results.violations,
      `Violaciones en ${p.path}: ${results.violations.map((v) => `${v.id}(${v.nodes.length})`).join(', ')}`,
    ).toEqual([]);
  });
}
