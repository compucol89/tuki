// @ts-check
const { test, expect } = require('@playwright/test');

/**
 * @visual — Regresión visual (toHaveScreenshot, maxDiffPixels en config).
 * Baselines generados en el MISMO entorno que las comparaciones
 * (docs/reference/playwright/test-snapshots.md). Solo light: el runtime no
 * implementa prefers-color-scheme dark (verificado en GATE 6).
 *
 * Primera corrida: npx playwright test --update-snapshots (genera baselines).
 * Se ocultan overlays no deterministas (popup de newsletter, cookie banner).
 */
const PAGES = [
  { name: 'home', path: '/' },
  { name: 'sobre-nosotros', path: '/sobre-nosotros' },
  { name: 'contacto', path: '/contacto' },
];

async function stabilizePage(page) {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  // Forzar carga de imágenes lazy: scroll completo y esperar a que completen
  await page.evaluate(async () => {
    await new Promise((resolve) => {
      let y = 0;
      const step = () => {
        y += 800;
        window.scrollTo(0, y);
        if (y >= document.body.scrollHeight) {
          window.scrollTo(0, 0);
          resolve();
        } else {
          setTimeout(step, 60);
        }
      };
      step();
    });
  });
  await page.evaluate(async () => {
    // Forzar swap de imágenes lazy (plugin .lazy con data-src) para determinismo
    document.querySelectorAll('img[data-src]').forEach((img) => {
      const src = img.getAttribute('data-src');
      if (src) img.src = src;
    });
    const imgs = Array.from(document.querySelectorAll('img'));
    await Promise.all(
      imgs.map((img) => {
        if (img.complete || img.src.startsWith('data:')) return Promise.resolve();
        return Promise.race([
          new Promise((r) => { img.onload = r; img.onerror = r; }),
          new Promise((r) => setTimeout(r, 3000)),
        ]);
      }),
    );
    for (const sel of ['.mfp-bg', '.mfp-wrap', '.announcement-popup', '.popup-wrapper', '.cookie-alert', '.cookie-consent', '[id*="cookie"]']) {
      document.querySelectorAll(sel).forEach((el) => {
        el.remove();
      });
    }
    document.documentElement.classList.remove('mfp-ready', 'mfp-removing');
    document.body.classList.remove('mfp-zoom-out-cur');
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
  });
  await page.waitForTimeout(400);
}

for (const p of PAGES) {
  test(`@visual ${p.name} desktop`, async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto(p.path, { waitUntil: 'networkidle' });
    await page.waitForTimeout(1200);
    await stabilizePage(page);
    await page.waitForTimeout(300);
    await expect(page).toHaveScreenshot(`${p.name}-desktop.png`, { fullPage: true });
  });
}

test('@visual home mobile', async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 667 });
  await page.goto('/', { waitUntil: 'networkidle' });
  await page.waitForTimeout(1200);
  await stabilizePage(page);
  await page.waitForTimeout(300);
  await expect(page).toHaveScreenshot('home-mobile.png', { fullPage: true });
});
