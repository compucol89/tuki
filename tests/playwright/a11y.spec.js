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
  { name: 'login', path: '/login' },
  { name: 'registro', path: '/registro' },
  { name: 'recuperar-contrasena', path: '/recuperar-contrasena' },
  { name: 'restablecer-contrasena', path: '/usuario/reset-password' },
  { name: 'organizer-login', path: '/organizer/login' },
  { name: 'organizer-signup', path: '/organizer/signup' },
  { name: 'organizer-forget-password', path: '/organizer/forget-password' },
  { name: 'organizer-reset-password', path: '/organizer/reset-password' },
];

for (const p of PAGES) {
  test(`@a11y ${p.name} sin violaciones axe (wcag2a + wcag2aa)`, async ({ page }) => {
    await page.goto(p.path, { waitUntil: 'load' });
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

/* ── Dashboard del Organizer (requiere autenticación) ──────────────────── */
const USER = {
  username: process.env.E2E_ORGANIZER_USERNAME,
  password: process.env.E2E_ORGANIZER_PASSWORD,
};

async function organizerLogin(page) {
  if (!USER.username || !USER.password) {
    throw new Error('Configurá E2E_ORGANIZER_USERNAME y E2E_ORGANIZER_PASSWORD para test:theme/a11y.');
  }
  await page.goto('/organizer/login', { waitUntil: 'load' });
  const alreadyIn = await page.evaluate(() => !!document.querySelector('.sidebar'));
  if (alreadyIn) return;
  await page.evaluate(({ u, p }) => {
    const set = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
    const fill = (sel, val) => {
      const el = document.querySelector(sel);
      set.call(el, val);
      el.dispatchEvent(new Event('input', { bubbles: true }));
    };
    fill('#username', u);
    fill('#password', p);
  }, { u: USER.username, p: USER.password });
  await page.getByRole('button', { name: /Ingresar al panel/i }).click();
  await page.waitForURL('**/organizer/dashboard', { timeout: 15_000 }).catch(() => {});
}

for (const theme of ['light', 'dark']) {
  test(`@a11y organizer-dashboard (${theme}) sin violaciones axe`, async ({ page }) => {
    await organizerLogin(page);
    await page.goto('/organizer/dashboard', { waitUntil: 'load' });
    await page.evaluate((t) => {
      document.documentElement.dataset.theme = t;
      document.body.setAttribute('data-background-color', t === 'dark' ? 'dark' : 'white');
      const s = document.querySelector('.sidebar');
      if (s) s.setAttribute('data-background-color', t === 'dark' ? 'dark2' : 'white');
    }, theme);
    await page.waitForTimeout(400);

    const results = await new AxeBuilder({ page })
      .exclude('.phpdebugbar')
      .withTags(['wcag2a', 'wcag2aa'])
      .analyze();

    expect(
      results.violations,
      `Violaciones dashboard ${theme}: ${results.violations.map((v) => `${v.id}(${v.nodes.length})`).join(', ')}`,
    ).toEqual([]);
  });
}
