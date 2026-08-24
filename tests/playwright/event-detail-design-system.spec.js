// @ts-check
const { test, expect } = require('@playwright/test');

const FINISHED_EVENT_PATH = '/colombia-vs-suiza-por-gol-caracol/122';

test.describe('@event-detail-design-system contrato visual ficha publica', () => {
  test('desktop usa tokens semanticos para panel, media, estado cerrado y sombras', async ({ page }) => {
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

    const contract = await page.evaluate(() => {
      const bodyStyle = getComputedStyle(document.body);
      const bookingCardStyle = getComputedStyle(document.querySelector('#event-booking-card'));
      const soldOutStyle = getComputedStyle(document.querySelector('.ed-ticket-option__status--sold-out'));
      const gallery = document.querySelector('.ed-gallery-main');
      const galleryStyle = gallery ? getComputedStyle(gallery) : null;

      return {
        radiusLg: bodyStyle.getPropertyValue('--ed-radius-lg').trim(),
        radiusPanel: bodyStyle.getPropertyValue('--ed-radius-panel').trim(),
        radiusMedia: bodyStyle.getPropertyValue('--ed-radius-media').trim(),
        stateClosedInk: bodyStyle.getPropertyValue('--ed-state-closed-ink').trim(),
        shadowPanel: bodyStyle.getPropertyValue('--ed-shadow-panel').trim(),
        cardRadius: bookingCardStyle.borderRadius,
        cardShadow: bookingCardStyle.boxShadow,
        galleryRadius: galleryStyle?.borderRadius || null,
        soldOutColor: soldOutStyle.color,
      };
    });

    expect(contract.radiusLg).toBe('16px');
    expect(contract.radiusPanel).toBe('22px');
    expect(contract.radiusMedia).toBe('20px');
    expect(contract.stateClosedInk).toBe('#991b1b');
    expect(contract.cardRadius).toBe('22px');
    expect(contract.shadowPanel).toBe('0 18px 44px rgba(30, 37, 50, 0.12)');
    expect(contract.cardShadow).toContain('rgba(30, 37, 50, 0.12)');
    expect(contract.cardShadow).toContain('18px 44px');
    if (contract.galleryRadius !== null) {
      expect(contract.galleryRadius).toBe('20px');
    }
    expect(contract.soldOutColor).toBe('rgb(153, 27, 27)');
  });

  test('mobile conserva layout plano sin overflow y CTA cerrado accesible', async ({ page }) => {
    for (const viewport of [
      { width: 390, height: 844 },
      { width: 320, height: 568 },
    ]) {
      await page.setViewportSize(viewport);
      await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

      const geometry = await page.evaluate(() => {
        const bookingCardStyle = getComputedStyle(document.querySelector('#event-booking-card'));
        return {
          overflow: document.documentElement.scrollWidth > window.innerWidth,
          cardRadius: bookingCardStyle.borderRadius,
        };
      });

      expect(geometry.overflow, `overflow horizontal en ${viewport.width}x${viewport.height}`).toBe(false);
      expect(geometry.cardRadius).toBe('0px');
      await expect(page.locator('.ed-mobile-bar__cta')).toHaveAttribute('aria-disabled', 'true');
    }
  });

  test('evento finalizado no emite Event JSON-LD con offers activos', async ({ page }) => {
    await page.setViewportSize({ width: 1024, height: 768 });
    await page.goto(FINISHED_EVENT_PATH, { waitUntil: 'load' });

    const structuredData = await page.evaluate(() => Array.from(document.querySelectorAll('script[type="application/ld+json"]'))
      .map((node) => {
        try {
          return JSON.parse(node.textContent || '{}');
        } catch (error) {
          return null;
        }
      })
      .filter(Boolean));

    const eventSchema = structuredData.find((entry) => entry['@type'] === 'Event');
    const breadcrumbSchema = structuredData.find((entry) => entry['@type'] === 'BreadcrumbList');

    expect(eventSchema).toBeUndefined();
    expect(breadcrumbSchema).toBeTruthy();
  });
});
