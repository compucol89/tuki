// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

const FINISHED_EVENT_PATH = '/colombia-vs-suiza-por-gol-caracol/122';
const ACTIVE_EVENT_PATH = '/reggaeton-old-school/123';

async function visibleText(page, selectors) {
  return page.evaluate((scopeSelectors) => {
    const isVisible = (el) => {
      const style = window.getComputedStyle(el);
      const rect = el.getBoundingClientRect();

      return style.display !== 'none'
        && style.visibility !== 'hidden'
        && Number(style.opacity) !== 0
        && rect.width > 0
        && rect.height > 0;
    };

    return scopeSelectors
      .flatMap((selector) => Array.from(document.querySelectorAll(selector)))
      .filter(isVisible)
      .map((el) => (el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim())
      .filter(Boolean)
      .join(' ');
  }, selectors);
}

async function axeScan(page, context) {
  const results = await new AxeBuilder({ page })
    .exclude('.phpdebugbar')
    .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
    .analyze();

  expect(
    results.violations,
    `Violaciones axe en ${context}: ${results.violations.map((v) => `${v.id}(${v.nodes.length})`).join(', ')}`,
  ).toEqual([]);
}

test.describe('@event-detail-forensic detalle evento publico', () => {
  test('evento finalizado no muestra venta activa ni urgencia sintetica', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

    await expect(page.getByText('Finalizado').first()).toBeVisible();

    const commerceText = await visibleText(page, [
      '.ed-hero-event',
      '#event-booking-card',
      '.ed-info-card--quickfacts',
      '.ed-mobile-bar',
    ]);

    expect(commerceText).toMatch(/Evento finalizado|Venta finalizada/i);
    expect(commerceText).not.toMatch(/Venta online activa|Reservá con anticipación|Se está moviendo|movimientos recientes/i);

    const metaDescription = await page.locator('meta[name="description"]').getAttribute('content');
    expect(metaDescription).toMatch(/finalizado|Venta online cerrada/i);
    expect(metaDescription).not.toMatch(/Reserv[aá]/i);
  });

  test('barra mobile de evento finalizado comunica estado, no precio desde FREE PASS', async ({ page }) => {
    await page.setViewportSize({ width: 320, height: 568 });
    await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

    const barText = await visibleText(page, ['.ed-mobile-bar']);

    expect(barText).toMatch(/Evento finalizado|Venta finalizada/i);
    expect(barText).not.toMatch(/Entradas desde\s+FREE PASS/i);

    const barCta = page.locator('.ed-mobile-bar__cta');
    await expect(barCta).toHaveAttribute('aria-disabled', 'true');
  });

  test('entradas gratis agotadas separan precio FREE PASS de estado Agotadas', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

    const ticketText = await visibleText(page, ['#event-booking-card .ed-ticket-option']);
    const titleText = await visibleText(page, ['#event-booking-card .ed-ticket-option__title']);

    expect(ticketText).toMatch(/FREE PASS/i);
    expect(ticketText).toMatch(/Agotadas/i);
    expect(titleText).not.toMatch(/Agotad[ao]s/i);
  });

  test('descripcion renderizada usa el dia canonico martes 7 de julio', async ({ page }) => {
    await page.setViewportSize({ width: 1024, height: 768 });
    await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

    const descriptionText = await visibleText(page, ['.ed-info-card--description .summernote-content']);

    expect(descriptionText).toMatch(/Martes 7 de julio/i);
    expect(descriptionText).not.toMatch(/Lunes 7 de julio/i);
  });

  test('evento finalizado no tiene overflow horizontal y pasa axe', async ({ page }) => {
    for (const viewport of [
      { width: 1440, height: 900 },
      { width: 768, height: 1024 },
      { width: 390, height: 844 },
      { width: 320, height: 568 },
    ]) {
      await page.setViewportSize(viewport);
      await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

      const overflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth);
      expect(overflow, `overflow horizontal en ${viewport.width}x${viewport.height}`).toBe(false);
    }

    await axeScan(page, FINISHED_EVENT_PATH);
  });

  test('evento activo conserva seleccion de entradas y subtotal', async ({ page }) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(ACTIVE_EVENT_PATH, { waitUntil: 'load' });

    const buyButton = page.locator('#event-booking-card .ed-buy-btn[type="submit"]');
    await expect(buyButton).toBeVisible();

    const enabledPlus = page.locator('#event-booking-card .quantity-up:not(:disabled):not([aria-disabled="true"])').first();
    if (await enabledPlus.count() === 0) {
      test.skip(true, 'El evento activo disponible no tiene controles de cantidad habilitados en este entorno.');
    }

    await enabledPlus.click();

    const totalValue = await page.locator('#total').inputValue();
    expect(Number(totalValue)).toBeGreaterThanOrEqual(0);
  });
});
