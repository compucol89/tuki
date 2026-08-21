// @ts-check
const { test, expect } = require('@playwright/test');

/**
 * @e2e — Navegación de las páginas públicas clave + consola sin errores.
 */
const PAGES = [
  { name: 'home', path: '/' },
  { name: 'eventos', path: '/eventos' },
  { name: 'blog', path: '/blog' },
  { name: 'contacto', path: '/contacto' },
  { name: 'sobre-nosotros', path: '/sobre-nosotros' },
  { name: 'organizadores', path: '/organizadores' },
  { name: 'faqs', path: '/preguntas-frecuentes' },
];

async function dismissPublicOverlays(page) {
  await page.evaluate(() => {
    for (const sel of ['.mfp-bg', '.mfp-wrap', '.announcement-popup', '.popup-wrapper', '.cookie-alert', '.cookie-consent']) {
      document.querySelectorAll(sel).forEach((el) => el.remove());
    }
    document.documentElement.classList.remove('mfp-ready', 'mfp-removing');
    document.body.classList.remove('mfp-zoom-out-cur');
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
  });
}

for (const p of PAGES) {
  test(`@e2e ${p.name} carga sin errores`, async ({ page }) => {
    const consoleErrors = [];
    page.on('console', (msg) => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });
    page.on('pageerror', (err) => consoleErrors.push(String(err)));

    const response = await page.goto(p.path, { waitUntil: 'networkidle' });
    expect(response.status()).toBe(200);

    const h1 = page.locator('h1');
    await expect(h1).toHaveCount(1);

    expect(consoleErrors).toEqual([]);
  });
}

test('@e2e flujo home → eventos navega correctamente', async ({ page }) => {
  await page.goto('/', { waitUntil: 'load' });
  await dismissPublicOverlays(page);
  const cta = page.getByRole('link', { name: 'Explorar eventos' }).first();
  await cta.click();
  await page.waitForURL('**/eventos');
  await expect(page.locator('h1')).toHaveCount(1);
});

test('@e2e home destacados usan tabs, labels y cards semánticas', async ({ page }) => {
  await page.goto('/', { waitUntil: 'load' });
  await dismissPublicOverlays(page);

  await expect(page.getByLabel('Buscar por nombre del evento')).toBeVisible();
  await expect(page.getByLabel('Ciudad o ubicación')).toBeVisible();
  await expect(page.locator('#hsSearchForm')).toBeVisible();
  await expect(page.locator('.hero-collage-section #hsSearchForm')).toHaveCount(0);
  await expect(page.getByRole('heading', { name: 'Eventos destacados' })).toBeVisible();

  const heroImageSrc = await page.locator('#heroCollageBg .hero-slide__image').first().getAttribute('src');
  expect(heroImageSrc).toMatch(/^\/assets\//);

  const tabs = page.locator('#nav-tab [role="tab"]');
  await expect(tabs.first()).toHaveText('Todos');

  const tabCount = await tabs.count();
  if (tabCount > 1) {
    await tabs.nth(1).click();
    await expect(tabs.nth(1)).toHaveAttribute('aria-selected', 'true');
  }

  await expect(page.locator('.events-section .ev-card[data-event-url], .events-section .event-card[data-event-url]')).toHaveCount(0);

  const cards = page.locator('.events-section a.ev-card[href], .events-section a.event-card[href]');
  if (await cards.count()) {
    const firstCard = cards.first();
    await expect(firstCard).toBeVisible();

    const href = await firstCard.getAttribute('href');
    expect(href).toMatch(/\/[^/]+\/\d+$/);
  }
});
