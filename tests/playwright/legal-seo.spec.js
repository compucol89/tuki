// @ts-check
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

/**
 * @legal — SEO + accesibilidad de las 6 páginas legales.
 * Verifica: title, H1 único, canonical, JSON-LD WebPage+BreadcrumbList,
 * TOC funcional, sin H2 duplicado del H1, nav legal, axe 0 violaciones.
 */
const LEGAL_PAGES = [
  { name: 'cookies', path: '/politica-de-cookies', slug: 'politica-de-cookies' },
  { name: 'privacidad', path: '/politica-de-privacidad', slug: 'politica-de-privacidad' },
  { name: 'terminos', path: '/terminos-y-condiciones', slug: 'terminos-y-condiciones' },
  { name: 'reembolsos', path: '/politica-de-reembolsos', slug: 'politica-de-reembolsos' },
  { name: 'eliminacion', path: '/eliminacion-de-datos', slug: 'eliminacion-de-datos' },
  { name: 'defensa-consumidor', path: '/defensa-al-consumidor', slug: 'defensa-al-consumidor' },
];

for (const p of LEGAL_PAGES) {
  test(`@legal ${p.name} — SEO y accesibilidad`, async ({ page }) => {
    const response = await page.goto(p.path, { waitUntil: 'load' });
    expect(response.status()).toBe(200);

    // SEO
    await expect(page).toHaveTitle(new RegExp('TukiPass'));
    expect(await page.locator('h1').count()).toBe(1);

    const canonical = await page.locator('link[rel="canonical"]').getAttribute('href');
    expect(canonical).toContain(p.path);

    const metaDesc = await page.locator('meta[name="description"]').getAttribute('content');
    expect(metaDesc && metaDesc.length > 20).toBeTruthy();

    const ld = await page.evaluate(() => {
      const types = [];
      document.querySelectorAll('script[type="application/ld+json"]').forEach((s) => {
        try {
          const j = JSON.parse(s.textContent);
          if (j['@graph']) j['@graph'].forEach((g) => types.push(g['@type']));
          else types.push(j['@type']);
        } catch (e) { /* noop */ }
      });
      return types;
    });
    expect(ld).toContain('WebPage');
    expect(ld).toContain('BreadcrumbList');

    // sin H2 duplicado del H1
    const h1 = (await page.locator('h1').textContent()).trim();
    const dupH2 = await page.locator('.summernote-content h2', { hasText: h1 }).count();
    expect(dupH2).toBe(0);

    // TOC presente (contenido tiene >=4 secciones) y links válidos
    const tocLinks = await page.locator('.legal-toc__item a').all();
    expect(tocLinks.length).toBeGreaterThanOrEqual(4);
    for (const link of tocLinks) {
      const href = await link.getAttribute('href');
      expect(href.startsWith('#seccion-')).toBeTruthy();
      const targetId = href.slice(1);
      expect(await page.locator(`#${targetId}`).count()).toBe(1);
    }

    // nav legal: 6 documentos, el actual marcado
    expect(await page.locator('.legal-nav__link').count()).toBe(6);
    expect(await page.locator('.legal-nav__link.is-current').count()).toBe(1);

    // accesibilidad
    const results = await new AxeBuilder({ page })
      .exclude('.phpdebugbar')
      .withTags(['wcag2a', 'wcag2aa'])
      .analyze();
    expect(results.violations, results.violations.map((v) => `${v.id}(${v.nodes.length})`).join(', ')).toEqual([]);
  });
}

test('@legal reembolsos — MerchantReturnPolicy con datos reales', async ({ page }) => {
  await page.goto('/politica-de-reembolsos', { waitUntil: 'load' });
  const policy = await page.evaluate(() => {
    let found = null;
    document.querySelectorAll('script[type="application/ld+json"]').forEach((s) => {
      try {
        const j = JSON.parse(s.textContent);
        const g = (j['@graph'] || []).find((x) => x['@type'] === 'MerchantReturnPolicy');
        if (g) found = g;
      } catch (e) { /* noop */ }
    });
    return found;
  });
  expect(policy).not.toBeNull();
  expect(policy.applicableCountry).toBe('AR');
  expect(policy.returnPolicyCategory).toContain('MerchantReturnUnspecified');
});

test('@legal no-reembolsos — sin MerchantReturnPolicy', async ({ page }) => {
  await page.goto('/politica-de-cookies', { waitUntil: 'load' });
  const hasPolicy = await page.evaluate(() => {
    let found = false;
    document.querySelectorAll('script[type="application/ld+json"]').forEach((s) => {
      try {
        const j = JSON.parse(s.textContent);
        if ((j['@graph'] || []).some((x) => x['@type'] === 'MerchantReturnPolicy')) found = true;
      } catch (e) { /* noop */ }
    });
    return found;
  });
  expect(hasPolicy).toBe(false);
});
