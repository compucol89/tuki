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
 *
 * Elementos dinámicos del home (slideshow del hero) se enmascaran por-test
 * (mask) — el resto de la página es estable pixel a pixel.
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
    for (const sel of [
      '.announcement-popup',
      '.popup-wrapper',
      '.cookie-alert',
      '.cookie-consent',
      '[id*="cookie"]',
      '#phpdebugbar',
      '.phpdebugbar',
      '.phpdebugbar-minimized',
      '[id^="phpdebugbar"]',
      '.phpdebugbar-openhandler',
      '.adsbygoogle',
    ]) {
      document.querySelectorAll(sel).forEach((el) => {
        el.style.display = 'none';
      });
    }
    document.querySelectorAll('a[onclick^="adView("], img[alt="advertisement"]').forEach((el) => {
      const wrapper = el.closest('.text-center.mt-40, .text-center.mt-4, .ad-banner, .ad-wrapper');
      (wrapper || el).style.display = 'none';
    });
    document.querySelectorAll('iframe[src*="google.com/maps"], iframe[src*="maps.google"]').forEach((iframe) => {
      iframe.style.visibility = 'hidden';
      const wrapper = iframe.parentElement;
      if (wrapper) {
        wrapper.style.background = '#eef1f3';
      }
    });
  });
  await page.waitForTimeout(400);
}

for (const p of PAGES) {
  const isHome = p.path === '/';
  test(`@visual ${p.name} desktop`, async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto(p.path, { waitUntil: 'load' });
    await page.waitForTimeout(1200);
    await stabilizePage(page);
    await page.waitForTimeout(300);
    await expect(page).toHaveScreenshot(`${p.name}-desktop.png`, {
      fullPage: true,
      ...(isHome
        ? { mask: [page.locator('.hero-section')], maxDiffPixels: 800 }
        : {
            // Sobrel-nosotros: contenido dinámico (testimonios/bandas) ~1% de
            // la página; documentado como tolerancia por-test (GATE 6).
            maxDiffPixels: 1000,
          }),
    });
  });
}

test('@visual home mobile', async ({ page }) => {
  await page.setViewportSize({ width: 375, height: 667 });
  await page.goto('/', { waitUntil: 'load' });
  await page.waitForTimeout(1200);
  await stabilizePage(page);
  await page.waitForTimeout(300);
  await expect(page).toHaveScreenshot('home-mobile.png', {
    fullPage: true,
    mask: [page.locator('.hero-section')],
    maxDiffPixels: 800,
  });
});
