// @ts-check
const { test, expect } = require('@playwright/test');

/**
 * @aria — Estructura semántica (headings, landmarks) de páginas públicas.
 * Complementa Axe: congela la jerarquía (toMatchAriaSnapshot en GATE de regresión).
 */

test('@aria home tiene un único h1 y jerarquía sin saltos', async ({ page }) => {
  await page.goto('/', { waitUntil: 'networkidle' });
  const levels = await page.locator('h1, h2, h3, h4, h5, h6').evaluateAll((els) =>
    els.map((el) => Number(el.tagName.slice(1))),
  );
  expect(levels.filter((l) => l === 1)).toHaveLength(1);
  for (let i = 1; i < levels.length; i++) {
    expect(levels[i] - levels[i - 1]).toBeLessThanOrEqual(1);
  }
});

test('@aria organizadores tiene un único h1 y jerarquía sin saltos', async ({ page }) => {
  await page.goto('/organizadores', { waitUntil: 'networkidle' });
  const levels = await page.locator('h1, h2, h3, h4, h5, h6').evaluateAll((els) =>
    els.map((el) => Number(el.tagName.slice(1))),
  );
  expect(levels.filter((l) => l === 1)).toHaveLength(1);
  for (let i = 1; i < levels.length; i++) {
    expect(levels[i] - levels[i - 1]).toBeLessThanOrEqual(1);
  }
});

test('@aria contacto tiene un único h1 y jerarquía sin saltos', async ({ page }) => {
  await page.goto('/contacto', { waitUntil: 'networkidle' });
  const levels = await page.locator('h1, h2, h3, h4, h5, h6').evaluateAll((els) =>
    els.map((el) => Number(el.tagName.slice(1))),
  );
  expect(levels.filter((l) => l === 1)).toHaveLength(1);
  for (let i = 1; i < levels.length; i++) {
    expect(levels[i] - levels[i - 1]).toBeLessThanOrEqual(1);
  }
});

test('@aria sobre-nosotros tiene un único h1', async ({ page }) => {
  await page.goto('/sobre-nosotros', { waitUntil: 'networkidle' });
  await expect(page.locator('h1')).toHaveCount(1);
});
