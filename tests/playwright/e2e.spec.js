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
  const cta = page.getByRole('link', { name: 'Explorar eventos' }).first();
  await cta.click();
  await page.waitForURL('**/eventos');
  await expect(page.locator('h1')).toHaveCount(1);
});
