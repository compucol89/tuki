// @ts-check
const { test, expect } = require('@playwright/test');
const { PUBLIC_PAGES } = require('./routes-manifest');

/**
 * @aria — Estructura semántica (headings, landmarks) de páginas públicas.
 * Complementa Axe: congela la jerarquía (toMatchAriaSnapshot en GATE de regresión).
 */

const HIERARCHY_PAGES = [
  { name: 'home', path: '/' },
  { name: 'organizadores', path: '/organizadores' },
  { name: 'contacto', path: '/contacto' },
];

async function headingLevels(page) {
  return page.locator('h1, h2, h3, h4, h5, h6').evaluateAll((els) =>
    els.map((el) => Number(el.tagName.slice(1))),
  );
}

for (const p of PUBLIC_PAGES) {
  test(`@aria ${p.name} tiene un único h1`, async ({ page }) => {
    await page.goto(p.path, { waitUntil: 'load' });
    await expect(page.locator('h1')).toHaveCount(1);
  });
}

for (const p of HIERARCHY_PAGES) {
  test(`@aria ${p.name} mantiene jerarquía sin saltos`, async ({ page }) => {
    await page.goto(p.path, { waitUntil: 'load' });
    const levels = await headingLevels(page);
    for (let i = 1; i < levels.length; i++) {
      expect(levels[i] - levels[i - 1]).toBeLessThanOrEqual(1);
    }
  });
}
