// @ts-check
const { test, expect } = require('@playwright/test');
const { PUBLIC_PAGES } = require('./routes-manifest');

/**
 * @e2e — Navegación de las páginas públicas clave + consola sin errores.
 * Separación explícita: console.error (gate) vs pageerror (gate) vs
 * requestfailed (inventario no bloqueante, se registra en el reporte/trace).
 */
for (const p of PUBLIC_PAGES) {
  test(`@e2e ${p.name} carga sin errores`, async ({ page }) => {
    const consoleErrors = [];
    const pageErrors = [];
    const failedRequests = [];
    page.on('console', (msg) => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });
    page.on('pageerror', (err) => pageErrors.push(String(err)));
    page.on('requestfailed', (req) => {
      failedRequests.push(`${req.url()} :: ${req.failure()?.errorText}`);
    });

    const response = await page.goto(p.path, { waitUntil: 'load' });
    expect(response.status()).toBe(200);

    const h1 = page.locator('h1');
    await expect(h1).toHaveCount(1);

    expect(consoleErrors, `console.error en ${p.path}`).toEqual([]);
    expect(pageErrors, `pageerror en ${p.path}`).toEqual([]);

    if (failedRequests.length) {
      console.warn(`[inventory] ${p.path} requestfailed: ${failedRequests.join(' | ')}`);
    }
  });
}

test('@e2e flujo home → eventos navega correctamente', async ({ page }) => {
  await page.goto('/', { waitUntil: 'load' });
  const cta = page.getByRole('link', { name: 'Explorar eventos' }).first();
  await cta.click();
  await page.waitForURL('**/eventos');
  await expect(page.locator('h1')).toHaveCount(1);
});

test('@e2e home mobile mantiene ancho usable en buscador', async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 667 });
  await page.goto('/', { waitUntil: 'load' });

  const searchGeometry = await page.locator('#hsSearchForm').evaluate((form) => {
    const keywordField = form.querySelector('.hs-sf__field--grow');
    const keywordInput = form.querySelector('input[name="search-input"]');
    const formRect = form.getBoundingClientRect();
    const fieldRect = keywordField ? keywordField.getBoundingClientRect() : null;
    const inputRect = keywordInput ? keywordInput.getBoundingClientRect() : null;

    return {
      formWidth: formRect.width,
      fieldWidth: fieldRect ? fieldRect.width : 0,
      inputWidth: inputRect ? inputRect.width : 0,
    };
  });

  expect(searchGeometry.fieldWidth).toBeGreaterThan(searchGeometry.formWidth * 0.9);
  expect(searchGeometry.inputWidth).toBeGreaterThan(240);
});

test('@e2e sobre-nosotros mantiene ritmo vertical de bandas', async ({ page }) => {
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.goto('/sobre-nosotros', { waitUntil: 'load' });

  const bands = await page.evaluate(() => {
    const ids = [
      'contenido-principal-sobre-nosotros',
      'para-organizadores',
      'caracteristicas',
      'testimonios',
      'aliados',
    ];

    return ids.map((id) => {
      const el = document.getElementById(id);
      const styles = el ? window.getComputedStyle(el) : null;

      return {
        id,
        paddingTop: styles ? Number.parseFloat(styles.paddingTop) : 0,
        paddingBottom: styles ? Number.parseFloat(styles.paddingBottom) : 0,
      };
    });
  });

  for (const band of bands) {
    expect(band.paddingTop, `${band.id} padding-top`).toBeGreaterThanOrEqual(64);
    expect(band.paddingBottom, `${band.id} padding-bottom`).toBeGreaterThanOrEqual(64);
  }
});

test('@e2e sobre-nosotros renderiza reseñas o estado vacío compacto', async ({ page }) => {
  await page.goto('/sobre-nosotros', { waitUntil: 'load' });

  const reviewState = await page.locator('#testimonios').evaluate((section) => {
    const wrap = section.querySelector('.testimonial-wrap');
    const empty = section.querySelector('.testimonial-empty');
    const emptyStyles = empty ? window.getComputedStyle(empty) : null;

    return {
      cards: section.querySelectorAll('.testimonial-item').length,
      emptyVisible: Boolean(empty && emptyStyles && emptyStyles.display !== 'none' && emptyStyles.visibility !== 'hidden'),
      wrapHeight: wrap ? wrap.getBoundingClientRect().height : 0,
    };
  });

  if (reviewState.cards === 0) {
    expect(reviewState.emptyVisible).toBe(true);
    expect(reviewState.wrapHeight).toBeLessThan(260);
  } else {
    expect(reviewState.cards).toBeGreaterThan(0);
    expect(reviewState.emptyVisible).toBe(false);
  }
});
