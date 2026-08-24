// @ts-check
const fs = require('fs');
const path = require('path');
const crypto = require('crypto');
const { chromium } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;

const phase = process.argv[2] || 'before';
const root = process.cwd();
const baseURL = process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8801';
const eventPath = '/colombia-vs-suiza-por-gol-caracol/122';
const outDir = path.join(root, 'docs/auditorias/event-detail-122-design-system-consolidation-2026-08-23');
const phaseDir = path.join(outDir, phase);
const screenshotsDir = path.join(phaseDir, 'screenshots');

const cssFiles = [
  'resources/views/frontend/layout.blade.php',
  'resources/views/frontend/event/event-details.blade.php',
  'public/assets/front/css/style.css',
  'public/assets/front/css/event-detail.css',
];

const tokenNames = [
  '--brand-primary',
  '--background',
  '--foreground',
  '--card',
  '--muted',
  '--muted-foreground',
  '--primary',
  '--primary-text',
  '--primary-hover',
  '--primary-active',
  '--success',
  '--success-text',
  '--info',
  '--danger',
  '--border',
  '--radius-xs',
  '--radius-sm',
  '--radius-md',
  '--radius-lg',
  '--radius-xl',
  '--shadow-sm',
  '--shadow-md',
  '--shadow-lg',
  '--shadow-xl',
  '--tuki-space-1',
  '--tuki-space-2',
  '--tuki-space-3',
  '--tuki-space-4',
  '--tuki-space-5',
  '--tuki-space-6',
  '--tuki-space-8',
  '--tuki-space-10',
  '--tuki-space-12',
  '--ed-page',
  '--ed-surface',
  '--ed-surface-soft',
  '--ed-ink',
  '--ed-ink-strong',
  '--ed-muted',
  '--ed-muted-soft',
  '--ed-line',
  '--ed-line-strong',
  '--ed-orange',
  '--ed-orange-strong',
  '--ed-orange-soft',
  '--ed-green',
  '--ed-green-strong',
  '--ed-blue',
  '--ed-radius',
  '--ed-radius-lg',
  '--ed-radius-panel',
  '--ed-radius-panel-mobile',
  '--ed-radius-media',
  '--ed-line-control',
  '--ed-line-hairline',
  '--ed-success-bright',
  '--ed-status-over-on-dark',
  '--ed-overlay-counter',
  '--ed-shadow-panel',
  '--ed-shadow-panel-strong',
  '--ed-shadow-cover',
  '--ed-shadow-media-hero',
  '--ed-state-closed-ink',
  '--ed-state-closed-surface',
  '--ed-state-closed-border',
  '--ed-disabled-surface',
  '--ed-disabled-border',
  '--ed-disabled-ink',
  '--ed-danger-dark-surface',
  '--ed-shadow',
  '--ed-shadow-soft',
  '--ed-shadow-hero',
  '--ed-sidebar-rail',
  '--ed-card-stack-gap',
];

const componentSelectors = [
  ['html', 'html'],
  ['body', 'body.page-event-detail'],
  ['hero', '.ed-hero-event'],
  ['hero-overlay', '.ed-hero-event .hero-overlay--premium'],
  ['hero-title', '.ed-ev-title'],
  ['hero-status', '.ed-hero__status-pill'],
  ['hero-nudge', '.ed-hero-nudge'],
  ['commerce-card', '#event-booking-card'],
  ['commerce-head', '#event-booking-card .ed-ticket-card__head'],
  ['commerce-body', '#event-booking-card .ed-ticket-card__body'],
  ['ticket-option', '#event-booking-card .ed-ticket-option'],
  ['ticket-title', '#event-booking-card .ed-ticket-option__title'],
  ['ticket-status', '#event-booking-card .ed-ticket-option__status--sold-out'],
  ['ticket-price', '#event-booking-card .ed-ticket-option__price, #event-booking-card .ed-ticket-option__price--free'],
  ['quantity-minus', '#event-booking-card .quantity-down'],
  ['quantity-plus', '#event-booking-card .quantity-up'],
  ['total-row', '#event-booking-card .ed-total-row'],
  ['buy-button', '#event-booking-card .ed-buy-btn'],
  ['quickfacts-card', '.ed-info-card--quickfacts'],
  ['description-card', '.ed-info-card--description'],
  ['description-copy', '.ed-info-card--description .summernote-content'],
  ['refund-band', '.ed-refund-band'],
  ['gallery-main', '.ed-gallery-main'],
  ['mobile-bar', '.ed-mobile-bar'],
  ['mobile-bar-cta', '.ed-mobile-bar__cta'],
  ['footer', 'footer, .footer-area'],
];

const viewports = [
  [1440, 900],
  [1280, 800],
  [1024, 768],
  [992, 768],
  [991, 768],
  [768, 1024],
  [576, 900],
  [575, 900],
  [390, 844],
  [375, 812],
  [320, 568],
];

function ensureDir(dir) {
  fs.mkdirSync(dir, { recursive: true });
}

function read(file) {
  return fs.readFileSync(path.join(root, file), 'utf8');
}

function hash(content) {
  return crypto.createHash('md5').update(content).digest('hex');
}

function lineNumberForIndex(text, index) {
  return text.slice(0, index).split(/\r?\n/).length;
}

function staticInventory() {
  const patterns = [
    ['hex', /#[0-9a-fA-F]{3,8}\b/g],
    ['rgba', /rgba?\([^)]*\)/g],
    ['radius', /border-radius\s*:\s*[^;]+/g],
    ['font-size', /font-size\s*:\s*[^;]+/g],
    ['box-shadow', /box-shadow\s*:\s*[^;]+/g],
    ['custom-property', /--[a-zA-Z0-9_-]+\s*:\s*[^;]+/g],
  ];

  return cssFiles.flatMap((file) => {
    const text = read(file);
    return patterns.flatMap(([kind, regex]) => {
      return Array.from(text.matchAll(regex)).map((match) => ({
        kind,
        file,
        line: lineNumberForIndex(text, match.index || 0),
        value: match[0].trim(),
      }));
    });
  });
}

function extractTokenDefinitions() {
  return staticInventory()
    .filter((entry) => entry.kind === 'custom-property')
    .map((entry) => {
      const parts = entry.value.split(':');
      return {
        name: parts.shift().trim(),
        value: parts.join(':').trim(),
        file: entry.file,
        line: entry.line,
        dependencies: Array.from(entry.value.matchAll(/var\((--[a-zA-Z0-9_-]+)/g)).map((match) => match[1]),
      };
    });
}

async function fetchText(url) {
  const response = await fetch(url);
  return response.ok ? response.text() : '';
}

async function installImageFallback(page) {
  await page.route('**/assets/admin/img/**', async (route) => {
    const requestUrl = route.request().url();
    const productionUrl = requestUrl.replace(baseURL, 'https://www.tukipass.com');

    try {
      const response = await route.fetch({ url: productionUrl });
      await route.fulfill({ response });
    } catch (error) {
      await route.continue();
    }
  });
}

async function collectForViewport(browser, viewport, includeCoverage = false) {
  const [width, height] = viewport;
  const context = await browser.newContext({
    baseURL,
    viewport: { width, height },
    deviceScaleFactor: 1,
  });
  const page = await context.newPage();
  await installImageFallback(page);

  let coverage = null;
  if (includeCoverage && page.coverage?.startCSSCoverage) {
    await page.coverage.startCSSCoverage({ resetOnNavigation: true });
  }

  await page.goto(eventPath, { waitUntil: 'load' });
  await page.addStyleTag({ content: '.phpdebugbar, .phpdebugbar-openhandler { display: none !important; }' });
  await page.screenshot({
    path: path.join(screenshotsDir, `${width}x${height}.png`),
    fullPage: true,
  });

  if (includeCoverage && page.coverage?.stopCSSCoverage) {
    const entries = await page.coverage.stopCSSCoverage();
    coverage = entries.map((entry) => {
      const usedBytes = entry.ranges.reduce((sum, range) => sum + (range.end - range.start), 0);
      return {
        url: entry.url,
        totalBytes: entry.text.length,
        usedBytes,
        usedPct: entry.text.length > 0 ? Number(((usedBytes / entry.text.length) * 100).toFixed(2)) : 0,
      };
    });
  }

  const result = await page.evaluate(({ selectors, tokenList }) => {
    const readStyle = (el) => {
      const cs = getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      const tokens = Object.fromEntries(tokenList.map((name) => [name, cs.getPropertyValue(name).trim()]));

      return {
        exists: true,
        visible: cs.display !== 'none' && cs.visibility !== 'hidden' && rect.width > 0 && rect.height > 0,
        rect: {
          x: Number(rect.x.toFixed(2)),
          y: Number(rect.y.toFixed(2)),
          width: Number(rect.width.toFixed(2)),
          height: Number(rect.height.toFixed(2)),
          top: Number(rect.top.toFixed(2)),
          bottom: Number(rect.bottom.toFixed(2)),
        },
        display: cs.display,
        position: cs.position,
        inset: {
          top: cs.top,
          right: cs.right,
          bottom: cs.bottom,
          left: cs.left,
        },
        width: cs.width,
        height: cs.height,
        padding: `${cs.paddingTop} ${cs.paddingRight} ${cs.paddingBottom} ${cs.paddingLeft}`,
        margin: `${cs.marginTop} ${cs.marginRight} ${cs.marginBottom} ${cs.marginLeft}`,
        gap: `${cs.rowGap} ${cs.columnGap}`,
        fontFamily: cs.fontFamily,
        fontSize: cs.fontSize,
        fontWeight: cs.fontWeight,
        lineHeight: cs.lineHeight,
        letterSpacing: cs.letterSpacing,
        textTransform: cs.textTransform,
        color: cs.color,
        backgroundColor: cs.backgroundColor,
        backgroundImage: cs.backgroundImage,
        border: `${cs.borderTopWidth} ${cs.borderTopStyle} ${cs.borderTopColor}`,
        borderRadius: cs.borderRadius,
        boxShadow: cs.boxShadow,
        opacity: cs.opacity,
        zIndex: cs.zIndex,
        overflow: `${cs.overflowX} ${cs.overflowY}`,
        tokens,
      };
    };

    const styleMap = Object.fromEntries(selectors.map(([id, selector]) => {
      const el = document.querySelector(selector);
      return [id, el ? readStyle(el) : { exists: false }];
    }));

    const getText = (selector) => Array.from(document.querySelectorAll(selector))
      .filter((el) => {
        const cs = getComputedStyle(el);
        const rect = el.getBoundingClientRect();
        return cs.display !== 'none' && cs.visibility !== 'hidden' && rect.width > 0 && rect.height > 0;
      })
      .map((el) => (el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim())
      .filter(Boolean)
      .join(' ');

    const buttons = Array.from(document.querySelectorAll('#event-booking-card button, #event-booking-card input, .ed-mobile-bar a')).map((el) => ({
      selector: el.id ? `#${el.id}` : Array.from(el.classList).map((name) => `.${name}`).join(''),
      tag: el.tagName.toLowerCase(),
      type: el.getAttribute('type'),
      disabled: Boolean(el.disabled),
      ariaDisabled: el.getAttribute('aria-disabled'),
      tabIndex: el.getAttribute('tabindex'),
      value: el.value || '',
      text: (el.innerText || el.textContent || '').replace(/\s+/g, ' ').trim(),
    }));

    const jsonLd = Array.from(document.querySelectorAll('script[type="application/ld+json"]')).map((node) => {
      try {
        return JSON.parse(node.textContent || '{}');
      } catch (error) {
        return { parseError: String(error), text: node.textContent };
      }
    });

    return {
      url: location.href,
      title: document.title,
      htmlLang: document.documentElement.lang,
      bodyClass: document.body.className,
      viewport: { width: window.innerWidth, height: window.innerHeight },
      scroll: {
        documentWidth: document.documentElement.scrollWidth,
        windowWidth: window.innerWidth,
        horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
      },
      seo: {
        canonical: document.querySelector('link[rel="canonical"]')?.getAttribute('href') || null,
        description: document.querySelector('meta[name="description"]')?.getAttribute('content') || null,
        ogDescription: document.querySelector('meta[property="og:description"]')?.getAttribute('content') || null,
        twitterDescription: document.querySelector('meta[name="twitter:description"]')?.getAttribute('content') || null,
      },
      stateText: getText('.ed-hero-event, #event-booking-card, .ed-info-card--quickfacts, .ed-mobile-bar'),
      descriptionText: getText('.ed-info-card--description .summernote-content'),
      styleMap,
      buttons,
      jsonLd,
    };
  }, { selectors: componentSelectors, tokenList: tokenNames });

  const axeResults = await new AxeBuilder({ page })
    .exclude('.phpdebugbar')
    .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'])
    .analyze();

  result.axe = {
    violations: axeResults.violations.map((violation) => ({
      id: violation.id,
      impact: violation.impact,
      nodes: violation.nodes.length,
      targets: violation.nodes.flatMap((node) => node.target),
    })),
    incomplete: axeResults.incomplete.map((item) => ({
      id: item.id,
      nodes: item.nodes.length,
    })),
  };

  result.coverage = coverage;
  await context.close();
  return result;
}

async function collectStress(browser) {
  const context = await browser.newContext({ baseURL, viewport: { width: 320, height: 568 } });
  const page = await context.newPage();
  await installImageFallback(page);
  await page.goto(eventPath, { waitUntil: 'load' });
  await page.addStyleTag({ content: '.phpdebugbar, .phpdebugbar-openhandler { display: none !important; }' });

  const stress = {};
  stress.cssZoom200Synthetic = await page.evaluate(() => {
    document.documentElement.style.zoom = '2';
    return {
      scrollWidth: document.documentElement.scrollWidth,
      innerWidth: window.innerWidth,
      horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
      activeElementTag: document.activeElement?.tagName || null,
    };
  });

  await page.evaluate(() => {
    document.documentElement.style.zoom = '';
  });
  await page.setViewportSize({ width: 640, height: 568 });
  stress.reflowAt640CssPx = await page.evaluate(() => ({
    scrollWidth: document.documentElement.scrollWidth,
    innerWidth: window.innerWidth,
    horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
  }));

  await page.setViewportSize({ width: 320, height: 568 });
  await page.evaluate(() => {
    const title = document.querySelector('.ed-ev-title');
    if (title) title.textContent = 'Colombia VS. Suiza por Gol Caracol con titulo extendido para validar wrapping responsive y ritmo visual';
    const location = document.querySelector('.ed-info-item__value');
    if (location) location.textContent = 'Centro Cultural Metropolitano de Buenos Aires con direccion extremadamente larga y referencia de acceso extendida';
  });
  stress.longContent = await page.evaluate(() => ({
    scrollWidth: document.documentElement.scrollWidth,
    innerWidth: window.innerWidth,
    horizontalOverflow: document.documentElement.scrollWidth > window.innerWidth,
    titleRect: (() => {
      const rect = document.querySelector('.ed-ev-title')?.getBoundingClientRect();
      return rect ? { width: rect.width, height: rect.height } : null;
    })(),
  }));

  await context.close();
  return stress;
}

async function main() {
  ensureDir(phaseDir);
  ensureDir(screenshotsDir);

  const localCss = read('public/assets/front/css/event-detail.css');
  const servedCss = await fetchText(`${baseURL}/assets/front/css/event-detail.css?v=${hash(localCss).slice(0, 12)}`);
  const browser = await chromium.launch();

  const runtime = [];
  for (let index = 0; index < viewports.length; index += 1) {
    runtime.push(await collectForViewport(browser, viewports[index], index === 0));
  }
  const stress = await collectStress(browser);
  await browser.close();

  const report = {
    phase,
    generatedAt: new Date().toISOString(),
    repository: root,
    eventPath,
    baseURL,
    sourceServedParity: {
      localEventDetailCssMd5: hash(localCss),
      servedEventDetailCssMd5: hash(servedCss),
      matches: hash(localCss) === hash(servedCss),
    },
    tokenDefinitions: extractTokenDefinitions(),
    staticInventory: staticInventory().filter((entry) => entry.kind !== 'custom-property'),
    runtime,
    stress,
  };

  fs.writeFileSync(path.join(phaseDir, 'design-system-audit.json'), JSON.stringify(report, null, 2));
  console.log(JSON.stringify({
    phase,
    sourceServedParity: report.sourceServedParity,
    viewports: runtime.map((item) => ({
      viewport: item.viewport,
      overflow: item.scroll.horizontalOverflow,
      commerceRadius: item.styleMap['commerce-card']?.borderRadius,
      edRadiusLg: item.styleMap.body?.tokens?.['--ed-radius-lg'],
      axeViolations: item.axe.violations,
    })),
    cssCoverage: runtime.find((item) => item.coverage)?.coverage,
    stress,
  }, null, 2));
}

main().catch((error) => {
  console.error(error);
  process.exit(1);
});
