// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;
const { PUBLIC_PAGES } = require('./routes-manifest');

/**
 * @a11y — Axe (WCAG 2.1 A/AA) sobre páginas públicas clave.
 *
 * Aserción canónica: violations == [] (docs/reference/playwright/accessibility-testing.md).
 * Tags: wcag2a/wcag2aa + wcag21a/wcag21aa (reglas nuevas de WCAG 2.1).
 *
 * WAIVERS documentados (regla → motivo → dueño → fecha):
 * - `exclude('.phpdebugbar')`: Laravel Debugbar es una herramienta de desarrollo
 *   (composer require-dev, APP_DEBUG local); no existe en producción.
 * - Reglas automatizadas no detectan todos los problemas (AXE PASS ≠ WCAG PASS):
 *   la evaluación manual queda documentada en docs/auditorias/ (GATE 7).
 */

async function axeScan(page, context) {
  const results = await new AxeBuilder({ page })
    .exclude('.phpdebugbar')
    .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
    .analyze();

  const violations = results.violations.map((v) => `${v.id}(${v.nodes.length})`).join(', ');
  const incomplete = results.incomplete.map((v) => `${v.id}(${v.nodes.length})`).join(', ');

  if (incomplete) {
    console.warn(`[incomplete-axe] ${context}: ${incomplete} (requiere revisión manual, GATE 7)`);
  }

  expect(
    results.violations,
    `Violaciones en ${context}: ${violations}`,
  ).toEqual([]);
}

for (const p of PUBLIC_PAGES) {
  test(`@a11y ${p.name} sin violaciones axe (wcag2a/2aa + wcag21a/21aa)`, async ({ page }) => {
    await page.goto(p.path, { waitUntil: 'load' });
    await page.waitForTimeout(300);
    await axeScan(page, p.path);
  });
}

test('@a11y faqs con acordeón expandido (estado interactivo) sin violaciones axe', async ({ page }) => {
  await page.goto('/preguntas-frecuentes', { waitUntil: 'load' });
  const trigger = page.locator('.faq-premium__trigger.collapsed').first();
  await trigger.click();
  await page.waitForTimeout(400);
  await axeScan(page, '/preguntas-frecuentes (acordeón expandido)');
});

/* ── Dashboard del Organizer (requiere autenticación) ──────────────────── */
const USER = {
  username: process.env.E2E_ORGANIZER_USERNAME,
  password: process.env.E2E_ORGANIZER_PASSWORD,
};

async function organizerLogin(page) {
  if (!USER.username || !USER.password) {
    throw new Error('Configurá E2E_ORGANIZER_USERNAME y E2E_ORGANIZER_PASSWORD para test:theme/a11y.');
  }
  // Bajo paralelismo la sesión file de Laravel puede perder la carrera de login
  // (lock de sesión) → reintento + verificación real de sesión antes de continuar.
  for (let attempt = 1; attempt <= 3; attempt++) {
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
    const authed = await page.evaluate(() => !!document.querySelector('.sidebar'));
    if (authed) return;
  }
  throw new Error('No se pudo autenticar al organizer (3 intentos, sesión no persistida).');
}

test.describe('@a11y rutas del organizer', () => {
  test.skip(!USER.username || !USER.password, 'Requiere E2E_ORGANIZER_USERNAME y E2E_ORGANIZER_PASSWORD.');

  // Serial: el login usa la sesión file de Laravel (lock por request); con
  // login concurrente la sesión se pierde bajo carga (flaky verificado).
  test.describe.configure({ mode: 'serial' });

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

      await axeScan(page, `organizer-dashboard (${theme})`);
    });
  }

  const bookingRoutes = [
    { name: 'organizer-event-booking', path: '/organizer/event-booking' },
    { name: 'organizer-event-booking-focused', path: '/organizer/event-booking/evento/118' },
    { name: 'organizer-event-booking-ticket-types', path: '/organizer/event-booking/evento/119' },
    { name: 'organizer-event-booking-details', path: '/organizer/event-booking/details/214' },
  ];

  for (const route of bookingRoutes) {
    for (const theme of ['light', 'dark']) {
      test(`@a11y ${route.name} (${theme}) sin violaciones axe`, async ({ page }) => {
        await organizerLogin(page);
        await page.goto(route.path, { waitUntil: 'load' });
        await page.evaluate((t) => {
          document.documentElement.dataset.theme = t;
          document.body.setAttribute('data-background-color', t === 'dark' ? 'dark' : 'white');
          const s = document.querySelector('.sidebar');
          if (s) s.setAttribute('data-background-color', t === 'dark' ? 'dark2' : 'white');
        }, theme);
        await page.waitForTimeout(400);

        await axeScan(page, `${route.name} (${theme})`);
      });
    }
  }
});
