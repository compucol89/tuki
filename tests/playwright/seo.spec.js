// @ts-check
const { test, expect, request } = require('@playwright/test');

const pageLocs = (xml) => [...xml.matchAll(/<url>\s*<loc>([^<]+)<\/loc>/g)].map((match) => match[1]);

const pathFromLoc = (loc) => {
  const normalized = loc.startsWith('http://') || loc.startsWith('https://')
    ? loc
    : `https://${loc}`;

  return new URL(normalized).pathname.replace(/\/$/, '') || '/';
};

test('@seo preguntas frecuentes no emite FAQPage deprecated', async ({ page }) => {
  await page.goto('/preguntas-frecuentes', { waitUntil: 'load' });

  const schemaTypes = await page.evaluate(() =>
    Array.from(document.querySelectorAll('script[type="application/ld+json"]'))
      .flatMap((script) => {
        try {
          const parsed = JSON.parse(script.textContent || '{}');
          const graph = Array.isArray(parsed['@graph']) ? parsed['@graph'] : [parsed];
          return graph.map((node) => node['@type']).filter(Boolean);
        } catch (error) {
          return ['INVALID_JSON_LD'];
        }
      })
  );

  expect(schemaTypes).toContain('BreadcrumbList');
  expect(schemaTypes).not.toContain('FAQPage');
  expect(schemaTypes).not.toContain('INVALID_JSON_LD');
});

test('@seo auth de organizador no expone placeholders SEO en inglés', async ({ page }) => {
  for (const path of ['/organizer/signup', '/organizer/forget-password', '/organizer/reset-password']) {
    await page.goto(path, { waitUntil: 'load' });

    const metadata = await page.evaluate(() => ({
      title: document.title,
      description: document.querySelector('meta[name="description"]')?.getAttribute('content') || '',
      h1: document.querySelector('h1')?.textContent || '',
      robots: document.querySelector('meta[name="robots"]')?.getAttribute('content') || '',
    }));
    const visibleSeoText = `${metadata.title} ${metadata.description} ${metadata.h1}`;

    expect(metadata.robots).toContain('noindex');
    expect(visibleSeoText).not.toMatch(/\bOrganizer\b|\bSignup\b|\bforget password\b|\bReset Password\b/);
  }
});

test('@seo sitemap de imágenes usa URLs absolutas y canónicas', async () => {
  const context = await request.newContext({ baseURL: 'http://localhost:8801' });
  const [sitemapResponse, imageSitemapResponse] = await Promise.all([
    context.get('/sitemap.xml'),
    context.get('/sitemap-images.xml'),
  ]);

  expect(sitemapResponse.ok()).toBeTruthy();
  expect(imageSitemapResponse.ok()).toBeTruthy();

  const sitemapXml = await sitemapResponse.text();
  const imageSitemapXml = await imageSitemapResponse.text();
  const sitemapPaths = new Set(pageLocs(sitemapXml).map(pathFromLoc));
  const imagePageLocs = pageLocs(imageSitemapXml);

  expect(imagePageLocs.length).toBeGreaterThan(0);
  expect(imagePageLocs.every((loc) => loc.startsWith('https://'))).toBe(true);

  const imageLocs = [...imageSitemapXml.matchAll(/<image:loc>([^<]+)<\/image:loc>/g)].map((match) => match[1]);
  expect(imageLocs.length).toBeGreaterThan(0);
  expect(imageLocs.every((loc) => loc.startsWith('https://'))).toBe(true);

  const imageOnlyPaths = imagePageLocs
    .map(pathFromLoc)
    .filter((path) => !sitemapPaths.has(path));

  expect(imageOnlyPaths, 'sitemap-images.xml no debe publicar páginas ausentes del sitemap canónico').toEqual([]);

  await context.dispose();
});
