// @ts-check
const { test, expect } = require('@playwright/test');

/**
 * @theme — Contrato de theming del Panel de Organizador (light × dark).
 * Detección runtime (computed styles), no screenshots:
 *   - 0 iconos #1572E8 (regresión del bug de selector leakage)
 *   - 0 superficies blancas en dark (islas light)
 *   - 0 texto oscuro sobre dark
 *   - 0 overflow horizontal
 * Ejecutar: npm run test:theme
 */
const { chromium } = require('@playwright/test');

const ROUTES = [
  { name: 'dashboard', path: '/organizer/dashboard' },
  { name: 'eventos', path: '/organizer/event-management/events?language=es' },
  { name: 'choose-type', path: '/organizer/choose-event-type?language=es' },
  { name: 'booking', path: '/organizer/event-booking' },
  { name: 'booking-focused', path: '/organizer/event-booking/evento/118' },
  { name: 'booking-details', path: '/organizer/event-booking/details/214' },
  { name: 'monthly-income', path: '/organizer/monthly-income' },
  { name: 'telegram', path: '/organizer/telegram-bot' },
  { name: 'withdraw', path: '/organizer/withdraw?language=es' },
];

const PUBLIC_DARK_ROUTES = [
  {
    name: 'home',
    path: '/',
    surfaces: [
      '.hero-collage-section',
      '.hero-slideshow',
      '.hs-search-form',
      '.events-section .ev-card',
      '.events-section .ev-card__body-panel',
    ],
  },
  {
    name: 'sobre-nosotros',
    path: '/sobre-nosotros',
    surfaces: [
      '.about-metrics--dashboard',
      '.about-organizer-pitch__pullquote',
      '.feature-item--about-premium',
      '.feature-item--about-premium .feature-item__icon',
      '.testimonial-item',
      '.total-client-reviews',
    ],
  },
  {
    name: 'preguntas-frecuentes',
    path: '/preguntas-frecuentes',
    surfaces: [
      '.faq-premium',
      '.faq-premium__accordion .card',
      '.faq-premium__trigger-icon',
      '.faq-premium__accordion .card-body',
    ],
  },
];

const USER = {
  username: process.env.E2E_ORGANIZER_USERNAME,
  password: process.env.E2E_ORGANIZER_PASSWORD,
};

function requireOrganizerCredentials() {
  if (!USER.username || !USER.password) {
    throw new Error('Configurá E2E_ORGANIZER_USERNAME y E2E_ORGANIZER_PASSWORD para ejecutar test:theme.');
  }
}

async function login(page) {
  requireOrganizerCredentials();
  // Bajo paralelismo la sesión file de Laravel puede perder la carrera de login
  // (lock de sesión) → reintento + verificación real de sesión antes de continuar.
  for (let attempt = 1; attempt <= 3; attempt++) {
    await page.goto('/organizer/login', { waitUntil: 'load' });
    const alreadyIn = await page.evaluate(() => !!document.querySelector('.sidebar'));
    if (alreadyIn) {
      return;
    }
    // El login usa bindings propietarios; forzamos el setter nativo + evento input
    await page.evaluate(({ u, p }) => {
      const set = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
      const fill = (sel, val) => {
        const el = document.querySelector(sel);
        set.call(el, val);
        el.dispatchEvent(new Event('input', { bubbles: true }));
      };
      fill('#username', u);
      fill('#password', p);
    }, { u: USER.username, p: USER.password });
    await page.getByRole('button', { name: /Ingresar al panel/i }).click();
    await page.waitForURL('**/organizer/dashboard', { timeout: 15_000 }).catch(() => {});
    const authed = await page.evaluate(() => !!document.querySelector('.sidebar'));
    if (authed) {
      return;
    }
  }
  throw new Error('No se pudo autenticar al organizer (3 intentos, sesión no persistida).');
}

async function setTheme(page, theme) {
  await page.evaluate((t) => {
    document.documentElement.dataset.theme = t;
    document.body.setAttribute('data-background-color', t === 'dark' ? 'dark' : 'white');
    const s = document.querySelector('.sidebar');
    if (s) s.setAttribute('data-background-color', t === 'dark' ? 'dark2' : 'white');
  }, theme);
  await page.waitForTimeout(200);
}

async function setPublicTheme(page, theme) {
  await page.addInitScript((t) => localStorage.setItem('tuki-theme', t), theme);
  await page.evaluate((t) => {
    localStorage.setItem('tuki-theme', t);
    document.documentElement.dataset.theme = t;
  }, theme).catch(() => {});
}

async function publicThemeAudit(page, selectors) {
  return page.evaluate((surfaceSelectors) => {
    const normalize = (values) => {
      const rgba = values.map(Number);
      const rgb = rgba.slice(0, 3).map((n) => (n <= 1 ? n * 255 : n));
      const alpha = rgba.length > 3 ? rgba[3] : 1;
      return { rgb, alpha };
    };

    const brightPaint = (paint) => {
      const matches = String(paint).match(/(?:rgba?|color\(srgb)\([^)]+\)/g) || [];
      return matches.some((token) => {
        const values = token.match(/[\d.]+/g);
        if (!values || values.length < 3) return false;
        const { rgb, alpha } = normalize(values);
        return alpha >= 0.3 && rgb[0] > 238 && rgb[1] > 238 && rgb[2] > 238;
      });
    };

    return surfaceSelectors.flatMap((selector) =>
      Array.from(document.querySelectorAll(selector)).map((el) => {
        const cs = getComputedStyle(el);
        const paint = `${cs.backgroundColor} ${cs.backgroundImage}`;
        return brightPaint(paint)
          ? {
              selector,
              className: el.className.toString().slice(0, 80),
              backgroundColor: cs.backgroundColor,
              backgroundImage: cs.backgroundImage.slice(0, 220),
            }
          : null;
      }).filter(Boolean)
    );
  }, selectors);
}

async function themeAudit(page) {
  return page.evaluate(() => {
    const parse = (c) => String(c).match(/[\d.]+/g).map(Number);
    const isWhite = (c) => {
      const r = parse(c);
      return r && r.length >= 3 && r[0] > 240 && r[1] > 240 && r[2] > 240;
    };
    const isDarkText = (c) => {
      const r = parse(c);
      return r && r.length >= 3 && r[0] < 60 && r[1] < 60 && r[2] < 60;
    };

    const whiteSurfaces = [];
    document.querySelectorAll('.card, .oe-panel, .oe-toolbar, .oe-metric, .oe-mobile-event, .ob-event-row, .ob-mobile-booking, .ob-event-summary-card, .ob-ticket-card, .oi-panel, .oi-metric, .oi-mobile-month, .bod-panel, .bod-hero, .bod-kpi, .bod-ledger, .bod-ticket-mobile-card, .tb-card, .tb-token, .ticket-free-limit, .ticket-form-intro, .ticket-form-content-intro, .ticket-form-language .version, .event-cover-box, .ai-assistant-card, .ai-generate-panel, .ai-status-card, .async-progress-panel, .create-cover-ai-panel').forEach((el) => {
      const bg = getComputedStyle(el).backgroundColor;
      if (bg && bg !== 'rgba(0, 0, 0, 0)' && isWhite(bg)) {
        whiteSurfaces.push(el.className.toString().slice(0, 40));
      }
    });

    const darkText = [];
    document.querySelectorAll('.oe-panel__title, .oe-metric__value, .ob-event-row__title, .ob-mobile-booking__title, .ob-detail-value, .ob-ticket-card__title, .ob-ticket-stat__value, .ob-ticket-card__money, .oi-panel__title, .oi-metric__value, .oi-money, .bod-title, .bod-value, .bod-ticket-name, .bod-ticket-mobile-card__title, .tb-status, .ticket-form-header__title, .ai-status-card__title').forEach((el) => {
      const c = getComputedStyle(el).color;
      if (isDarkText(c)) darkText.push(el.className.toString().slice(0, 40));
    });

    const blueIcons = Array.from(document.querySelectorAll('.sidebar .nav i, .sidebar .nav-collapse a i'))
      .filter((i) => getComputedStyle(i).color === 'rgb(21, 114, 232)')
      .map((i) => Array.from(i.classList).find((c) => c.startsWith('fa-')));

    const overflow = document.documentElement.scrollWidth > document.documentElement.clientWidth;

    return { whiteSurfaces, darkText, blueIcons, overflow };
  });
}

test('@theme detalle evento dark: entradas no muestran islas claras al interactuar', async ({ page }) => {
  await page.setViewportSize({ width: 600, height: 900 });
  await page.addInitScript(() => localStorage.setItem('tuki-theme', 'dark'));
  await page.goto('/reggaeton-old-school/123', { waitUntil: 'load' });

  const option = page.locator('.ed-ticket-option').first();
  await expect(option).toBeVisible();
  await option.hover();

  const hover = await option.evaluate((el) => {
    const isLightSurface = (color) => {
      const nums = (String(color).match(/[\d.]+/g) || []).map(Number);
      if (nums.length < 3) return false;
      const rgb = nums.slice(0, 3).map((n) => (n <= 1 ? n * 255 : n));
      return rgb[0] > 240 && rgb[1] > 240 && rgb[2] > 240;
    };
    const bg = getComputedStyle(el).backgroundColor;
    return { theme: document.documentElement.dataset.theme, bg, isLight: isLightSurface(bg) };
  });

  expect(hover.theme).toBe('dark');
  expect(hover.isLight, `hover claro en entrada dark: ${hover.bg}`).toBe(false);

  await option.locator('.quantity-up').click();

  const focused = await option.evaluate((el) => {
    const isLightSurface = (color) => {
      const nums = (String(color).match(/[\d.]+/g) || []).map(Number);
      if (nums.length < 3) return false;
      const rgb = nums.slice(0, 3).map((n) => (n <= 1 ? n * 255 : n));
      return rgb[0] > 240 && rgb[1] > 240 && rgb[2] > 240;
    };
    const bg = getComputedStyle(el).backgroundColor;
    return { bg, isLight: isLightSurface(bg) };
  });

  expect(focused.isLight, `focus/cantidad claro en entrada dark: ${focused.bg}`).toBe(false);
});

test.describe('@theme contrato theming público', () => {
  for (const route of PUBLIC_DARK_ROUTES) {
    test(`${route.name} dark sin islas light`, async ({ page }) => {
      await setPublicTheme(page, 'dark');
      await page.goto(route.path, { waitUntil: 'load' });
      await setPublicTheme(page, 'dark');

      const brightSurfaces = await publicThemeAudit(page, route.surfaces);

      expect(brightSurfaces, `islas claras en dark: ${route.name}`).toEqual([]);
    });
  }
});

test('@theme home dark conserva hero con fondo y buscador separado', async ({ page }) => {
  await setPublicTheme(page, 'dark');
  await page.goto('/', { waitUntil: 'load' });
  await setPublicTheme(page, 'dark');

  const hero = await page.locator('body.home-page .hero-collage-section').evaluate((el) => {
    const slideshow = el.querySelector('.hero-slideshow');
    const heroRect = el.getBoundingClientRect();
    const slideRect = slideshow ? slideshow.getBoundingClientRect() : null;
    const slideStyle = slideshow ? getComputedStyle(slideshow) : null;

    return {
      heroPosition: getComputedStyle(el).position,
      slidePosition: slideStyle ? slideStyle.position : '',
      slideWidth: slideRect ? slideRect.width : 0,
      slideHeight: slideRect ? slideRect.height : 0,
      heroWidth: heroRect.width,
      heroHeight: heroRect.height,
      searchInHero: !!el.querySelector('#hsSearchForm'),
    };
  });

  expect(hero.heroPosition).toBe('relative');
  expect(hero.slidePosition).toBe('absolute');
  expect(hero.slideWidth).toBeGreaterThanOrEqual(hero.heroWidth);
  expect(hero.slideHeight).toBeGreaterThanOrEqual(hero.heroHeight);
  expect(hero.searchInHero).toBe(false);
  await expect(page.locator('body.home-page .hs-search-wrap #hsSearchForm')).toBeVisible();

  const searchButton = await page.locator('body.home-page .hs-sf__btn').evaluate((el) => {
    const cs = getComputedStyle(el);
    const rect = el.getBoundingClientRect();

    return {
      backgroundColor: cs.backgroundColor,
      backgroundImage: cs.backgroundImage,
      color: cs.color,
      width: rect.width,
      height: rect.height,
    };
  });

  expect(searchButton.width).toBeGreaterThan(80);
  expect(searchButton.height).toBeGreaterThan(40);
  expect(`${searchButton.backgroundColor} ${searchButton.backgroundImage}`).not.toContain('rgba(0, 0, 0, 0) none');
  expect(searchButton.color).toBe('rgb(255, 255, 255)');
});

test.describe('@theme contrato theming organizer', () => {
  test.skip(!USER.username || !USER.password, 'Requiere E2E_ORGANIZER_USERNAME y E2E_ORGANIZER_PASSWORD.');

  // Serial: el login usa la sesión file de Laravel (lock por request); con
  // login concurrente la sesión se pierde bajo carga (flaky verificado).
  test.describe.configure({ mode: 'serial' });

  for (const route of ROUTES) {
    for (const theme of ['light', 'dark']) {
      test(`${route.name} × ${theme} sin islas blancas / azules / texto oscuro`, async ({ page }) => {
        test.setTimeout(60_000);
        await login(page);
        await page.goto(route.path, { waitUntil: 'load' });
        await setTheme(page, theme);

        // Presencia real: si el login no persistió (página pública), FALLA.
        await expect(page.locator('.sidebar')).toBeVisible();

        const audit = await themeAudit(page);

        expect(audit.blueIcons, `iconos azules #1572E8 en ${route.name} (${theme})`).toEqual([]);
        expect(audit.overflow, `overflow horizontal en ${route.name} (${theme})`).toBe(false);
        if (theme === 'dark') {
          expect(audit.whiteSurfaces, `islas blancas en dark: ${route.name}`).toEqual([]);
          expect(audit.darkText, `texto oscuro sobre dark: ${route.name}`).toEqual([]);
        }
      });
    }
  }

  test('@theme monthly-income footer queda en flujo scrolleable', async ({ page }) => {
    await login(page);
    await page.setViewportSize({ width: 538, height: 872 });
    await page.goto('/organizer/monthly-income', { waitUntil: 'load' });
    await setTheme(page, 'light');

    const flow = await page.evaluate(() => {
      const footer = document.querySelector('.footer');
      const rect = footer.getBoundingClientRect();

      return {
        panelClass: document.querySelector('.main-panel').className,
        footerPosition: getComputedStyle(footer).position,
        canReachFooter: document.documentElement.scrollHeight >= rect.bottom + window.scrollY - 2,
      };
    });

    expect(flow.panelClass).toContain('main-panel--flow-footer');
    expect(flow.footerPosition).toBe('static');
    expect(flow.canReachFooter).toBe(true);

    await page.evaluate(() => window.scrollTo(0, document.documentElement.scrollHeight));
    await expect(page.locator('.footer')).toBeVisible();

    const footerText = await page.locator('.footer').innerText();
    expect(footerText).not.toContain('TAYRONA GROUP SAS');
    expect(footerText).not.toContain('CUIT');
    expect(footerText).not.toContain('WhatsApp');
    expect(footerText).toMatch(/©\s*20\d{2}\s+Tukipass\.com\s*\|\s*Todos los derechos reservados\./);
  });

  test('@theme dashboard mantiene geometría visual de módulos', async ({ page }) => {
    await login(page);

    const viewports = [
      { width: 1440, height: 900, rows: [4, 2], gap: 16, maxProfile: 210 },
      { width: 768, height: 1024, rows: [2, 2, 1, 1], gap: 16, maxProfile: 430 },
      { width: 538, height: 872, rows: [2, 2, 1, 1], gap: 16, maxProfile: 430 },
      { width: 390, height: 844, rows: [1, 1, 1, 1, 1, 1], gap: 12, maxProfile: 480 },
    ];

    for (const viewport of viewports) {
      await page.setViewportSize({ width: viewport.width, height: viewport.height });
      await page.goto('/organizer/dashboard', { waitUntil: 'networkidle' });
      await setTheme(page, 'light');

      const geom = await page.evaluate(() => {
        const rect = (el) => {
          const r = el.getBoundingClientRect();
          return {
            top: Math.round(r.top),
            width: Math.round(r.width),
            height: Math.round(r.height),
            bottom: Math.round(r.bottom),
          };
        };
        const items = Array.from(document.querySelectorAll('.dashboard-items > [class*="col-"]')).map(rect);
        const rows = Object.values(items.reduce((acc, item) => {
          acc[item.top] = (acc[item.top] || 0) + 1;
          return acc;
        }, {}));
        const gaps = [];
        for (let i = 1; i < items.length; i += 1) {
          if (Math.abs(items[i].top - items[i - 1].top) > 3) {
            gaps.push(items[i].top - items[i - 1].bottom);
          }
        }
        const statCards = Array.from(document.querySelectorAll('.dashboard-items .card-stats')).map(rect);
        const statFonts = Array.from(document.querySelectorAll('.dashboard-items .card-stats .card-title'))
          .map((el) => getComputedStyle(el).fontFamily);
        const chartTicks = window.__tukiCharts && window.__tukiCharts.TotalEventBookingChart
          ? window.__tukiCharts.TotalEventBookingChart.scales['y-axis-0'].ticks.length
          : 0;

        return {
          overflowX: document.documentElement.scrollWidth > window.innerWidth,
          rows,
          gaps,
          statHeights: statCards.map((card) => card.height),
          statFonts,
          profileHeight: rect(document.querySelector('.od-profile-score')).height,
          chartTicks,
        };
      });

      expect(geom.overflowX, `dashboard overflow ${viewport.width}`).toBe(false);
      expect(geom.rows, `dashboard grid rows ${viewport.width}`).toEqual(viewport.rows);
      expect(Math.max(...geom.statHeights) - Math.min(...geom.statHeights), `stat height drift ${viewport.width}`).toBeLessThanOrEqual(4);
      expect(geom.gaps.every((gap) => gap === viewport.gap), `dashboard gaps ${viewport.width}: ${geom.gaps.join(',')}`).toBe(true);
      expect(geom.profileHeight, `profile score height ${viewport.width}`).toBeLessThanOrEqual(viewport.maxProfile);
      expect(geom.statFonts.every((font) => font.includes('IBM Plex Mono')), `stat font ${viewport.width}`).toBe(true);
      expect(geom.chartTicks, `booking chart tick density ${viewport.width}`).toBeGreaterThan(0);
      expect(geom.chartTicks, `booking chart tick density ${viewport.width}`).toBeLessThanOrEqual(6);
    }
  });

  test('@theme eventos mobile: cards cierran limpio y tipo no empuja título', async ({ page }) => {
    await login(page);
    await page.setViewportSize({ width: 538, height: 872 });
    await page.goto('/organizer/event-management/events?language=es&event_type=venue', { waitUntil: 'networkidle' });
    await setTheme(page, 'light');

    const geom = await page.evaluate(() => {
      const rect = (el) => {
        const r = el.getBoundingClientRect();
        return {
          top: Math.round(r.top),
          bottom: Math.round(r.bottom),
          height: Math.round(r.height),
        };
      };
      const cards = Array.from(document.querySelectorAll('.oe-mobile-event'));
      const first = cards[0];
      const last = cards[cards.length - 1];
      const panel = document.querySelector('.organizer-events .oe-panel:nth-of-type(2)');
      const status = first?.querySelector('.oe-mobile-event__badges .badge');
      const type = first?.querySelector('.oe-mobile-event__badges .oe-pill');
      const title = first?.querySelector('.oe-title');
      const footer = document.querySelector('.organizer-events .oe-panel:nth-of-type(2) > .card-footer');

      return {
        cardCount: cards.length,
        hasEmptyFooter: !!footer && footer.innerText.trim() === '',
        typeInsideTitleColumn: !!first?.querySelector('.oe-mobile-event__main .oe-pill'),
        typeBelowStatus: type && status ? rect(type).top >= rect(status).bottom : false,
        titleStartsWithStatus: title && status ? Math.abs(rect(title).top - rect(status).top) <= 2 : false,
        panelBottomGap: panel && last ? Math.round(panel.getBoundingClientRect().bottom - last.getBoundingClientRect().bottom) : null,
        overflowX: document.documentElement.scrollWidth > window.innerWidth,
      };
    });

    expect(geom.cardCount).toBeGreaterThan(0);
    expect(geom.hasEmptyFooter).toBe(false);
    expect(geom.typeInsideTitleColumn).toBe(false);
    expect(geom.typeBelowStatus).toBe(true);
    expect(geom.titleStartsWithStatus).toBe(true);
    expect(geom.panelBottomGap).toBeGreaterThanOrEqual(12);
    expect(geom.panelBottomGap).toBeLessThanOrEqual(24);
    expect(geom.overflowX).toBe(false);
  });

  test('@theme reservas mobile: filas profesionales y datos mono', async ({ page }) => {
    await login(page);
    await page.setViewportSize({ width: 538, height: 872 });
    await page.goto('/organizer/event-booking', { waitUntil: 'networkidle' });
    await setTheme(page, 'light');

    const geom = await page.evaluate(() => {
      const rows = Array.from(document.querySelectorAll('.ob-event-row'));
      const first = rows[0];
      const second = rows[1];
      const stats = first ? Array.from(first.querySelectorAll('.ob-event-row__stat')) : [];
      const statRows = Object.values(stats.reduce((acc, item) => {
        const top = Math.round(item.getBoundingClientRect().top);
        acc[top] = (acc[top] || 0) + 1;
        return acc;
      }, {}));
      const panel = document.querySelector('.ob-type-summary');
      const last = rows[rows.length - 1];
      const cta = first?.querySelector('.ob-event-row__cta');
      const thumb = first?.querySelector('.ob-event-row__thumb');
      const status = first?.querySelector('.ob-event-row__badge--status');
      const type = first?.querySelector('.ob-event-row__badge--type');
      const grid = first?.querySelector('.ob-event-row__grid');
      const settlement = first?.querySelector('.ob-event-row__settlement');
      const fonts = [
        ...Array.from(document.querySelectorAll('.ob-metric__value')),
        ...Array.from(document.querySelectorAll('.ob-event-row__value')),
        ...Array.from(document.querySelectorAll('.ob-event-row__muted .tuki-data')),
        ...Array.from(document.querySelectorAll('.ob-event-row__money')),
      ].map((el) => getComputedStyle(el).fontFamily);
      const dateText = first?.querySelector('.ob-event-row__date')?.innerText || '';
      const categoryText = first?.querySelector('.ob-event-row__category')?.innerText || '';
      const settlementText = first?.querySelector('.ob-event-row__settlement')?.innerText || '';
      const styleOf = (selector) => {
        const el = document.querySelector(selector);
        const cs = getComputedStyle(el);
        return {
          fontSize: parseFloat(cs.fontSize),
          fontWeight: Number(cs.fontWeight),
          textTransform: cs.textTransform,
        };
      };

      return {
        rowCount: rows.length,
        firstBg: first ? getComputedStyle(first).backgroundColor : null,
        secondBg: second ? getComputedStyle(second).backgroundColor : null,
        statRows,
        allDataFonts: fonts.length > 0 && fonts.every((font) => font.includes('IBM Plex Mono')),
        ctaRatio: first && cta ? cta.getBoundingClientRect().width / first.getBoundingClientRect().width : 0,
        thumbSize: thumb ? {
          width: Math.round(thumb.getBoundingClientRect().width),
          height: Math.round(thumb.getBoundingClientRect().height),
        } : null,
        typeBelowStatus: status && type ? type.getBoundingClientRect().top > status.getBoundingClientRect().top : false,
        gridColumns: grid ? getComputedStyle(grid).gridTemplateColumns.split(' ').length : 0,
        hasSettlement: !!settlement,
        dateReadsLikeEvents: /^Función:\s+/.test(dateText),
        categoryReadsLikeEvents: /^Categoría:\s+/.test(categoryText),
        settlementReadsAsNet: /Ingreso neto\s+Neto:/.test(settlementText.replace(/\n/g, ' ')),
        panelBottomGap: panel && last ? Math.round(panel.getBoundingClientRect().bottom - last.getBoundingClientRect().bottom) : null,
        pageTitle: styleOf('.organizer-booking-admin .page-title'),
        sectionTitle: styleOf('.ob-type-summary__title'),
        rowTitle: styleOf('.ob-event-row__title'),
        metricLabel: styleOf('.ob-metric__label'),
        metricValue: styleOf('.ob-metric__value'),
        statLabel: styleOf('.ob-event-row__label'),
        statValue: styleOf('.ob-event-row__value'),
        overflowX: document.documentElement.scrollWidth > window.innerWidth,
      };
    });

    expect(geom.rowCount).toBeGreaterThan(0);
    expect(geom.firstBg).not.toBe(geom.secondBg);
    expect(geom.thumbSize).toEqual({ width: 54, height: 54 });
    expect(geom.typeBelowStatus).toBe(true);
    expect(geom.gridColumns).toBe(2);
    expect(geom.statRows).toEqual([2]);
    expect(geom.hasSettlement).toBe(true);
    expect(geom.allDataFonts).toBe(true);
    expect(geom.ctaRatio).toBeGreaterThan(0.85);
    expect(geom.dateReadsLikeEvents).toBe(true);
    expect(geom.categoryReadsLikeEvents).toBe(true);
    expect(geom.settlementReadsAsNet).toBe(true);
    expect(geom.panelBottomGap).toBeGreaterThanOrEqual(12);
    expect(geom.panelBottomGap).toBeLessThanOrEqual(24);
    expect(geom.pageTitle.fontSize).toBeGreaterThan(geom.sectionTitle.fontSize);
    expect(geom.sectionTitle.fontSize).toBeGreaterThan(geom.rowTitle.fontSize);
    expect(geom.rowTitle.fontWeight).toBe(700);
    expect(geom.metricLabel.textTransform).toBe('uppercase');
    expect(geom.metricLabel.fontSize).toBeLessThan(geom.metricValue.fontSize);
    expect(geom.metricValue.fontWeight).toBeGreaterThanOrEqual(740);
    expect(geom.statLabel.textTransform).toBe('none');
    expect(geom.statLabel.fontWeight).toBe(600);
    expect(geom.statLabel.fontSize).toBeLessThan(geom.statValue.fontSize);
    expect(geom.statValue.textTransform).toBe('none');
    expect(geom.statValue.fontWeight).toBe(700);
    expect(geom.overflowX).toBe(false);

    const bookingRow = page.locator('.ob-event-row').first();
    const bookingCta = bookingRow.locator('.ob-event-row__cta');
    const readButtonStyle = async (locator) => locator.evaluate((el) => {
      const cs = getComputedStyle(el);
      const rect = el.getBoundingClientRect();
      return {
        backgroundColor: cs.backgroundColor,
        borderRadius: cs.borderRadius,
        color: cs.color,
        fontWeight: Number(cs.fontWeight),
        height: Math.round(rect.height),
      };
    });

    const ctaBeforeHover = await readButtonStyle(bookingCta);
    await bookingRow.hover();
    await expect.poll(async () => readButtonStyle(bookingCta)).toEqual(ctaBeforeHover);

    await page.goto('/organizer/event-management/events?language=es&event_type=venue', { waitUntil: 'networkidle' });
    const createButtonStyle = await readButtonStyle(page.locator('#organizerEventCreateDropdown'));
    expect(ctaBeforeHover).toEqual(createButtonStyle);
  });

  test('@theme reservas evento mobile: ventas ordenadas y panel plano', async ({ page }) => {
    await login(page);
    await page.setViewportSize({ width: 538, height: 872 });

    for (const theme of ['light', 'dark']) {
      await page.goto('/organizer/event-booking/evento/118', { waitUntil: 'networkidle' });
      await setTheme(page, theme);

      const geom = await page.evaluate(() => {
        const panel = document.querySelector('.ob-panel--flat');
        const cards = Array.from(document.querySelectorAll('.ob-mobile-booking'));
        const first = cards[0];
        const heading = first?.querySelector('.ob-mobile-booking__heading');
        const title = first?.querySelector('.ob-mobile-booking__title');
        const ref = first?.querySelector('.ob-mobile-booking__ref');
        const meta = first?.querySelector('.ob-mobile-booking__meta');
        const status = first?.querySelector('.ob-mobile-booking__badges .ob-status');
        const buyer = first?.querySelector('.ob-mobile-buyerline');
        const grid = first?.querySelector('.ob-mobile-booking__grid');
        const amountPair = first?.querySelector('.ob-mobile-stat--amount .ob-mobile-stat__line--primary');
        const amountLabel = amountPair?.querySelector('.ob-mobile-stat__label');
        const amountValue = amountPair?.querySelector('.ob-mobile-stat__value');
        const payment = first?.querySelector('.ob-mobile-payment');
        const paymentLabel = first?.querySelector('.ob-mobile-payment__label');
        const tickets = first?.querySelector('.ob-mobile-extra--tickets');
        const ticketTitle = first?.querySelector('.ob-mobile-extra--tickets .ob-mini-title');
        const controls = first?.querySelector('.ob-mobile-controls');
        const actionLabels = controls ? Array.from(controls.querySelectorAll('a, button')).map((el) => el.getAttribute('aria-label') || '') : [];
        const dataFonts = first ? Array.from(first.querySelectorAll('.tuki-data, .ob-money')).map((el) => getComputedStyle(el).fontFamily) : [];
        const buttonHeights = controls ? Array.from(controls.querySelectorAll('a, button')).map((el) => Math.round(el.getBoundingClientRect().height)) : [];
        const order = first ? Array.from(first.children).map((el) => el.className.toString()) : [];
        const panelStyles = panel ? getComputedStyle(panel) : null;
        const headingStyles = heading ? getComputedStyle(heading) : null;
        const buyerRows = buyer
          ? Array.from(buyer.querySelectorAll('.ob-mobile-buyerline__identity, .ob-mobile-buyerline__contact, .ob-mobile-buyerline__badge'))
            .map((el) => el.getBoundingClientRect().top)
            .sort((a, b) => a - b)
            .reduce((rows, top) => {
              if (rows.length === 0 || Math.abs(top - rows[rows.length - 1]) > 10) {
                rows.push(top);
              }
              return rows;
            }, []).length
          : 0;

        return {
          cardCount: cards.length,
          panelFlat: panelStyles
            ? panelStyles.boxShadow === 'none' && panelStyles.backgroundColor === 'rgba(0, 0, 0, 0)'
            : false,
          titleText: title?.textContent?.trim() || '',
          headingText: heading?.textContent?.trim() || '',
          titleClamp: headingStyles?.webkitLineClamp || headingStyles?.lineClamp || '',
          titleOverflow: headingStyles?.textOverflow || '',
          headingLines: heading ? Math.round(heading.getBoundingClientRect().height / parseFloat(headingStyles.lineHeight)) : 0,
          refInHeading: !!ref && !!heading && ref.parentElement === heading,
          metaText: meta?.textContent?.trim() || '',
          metaHasDataFont: !!meta?.querySelector('.tuki-data'),
          statusRightColumn: status && title ? status.getBoundingClientRect().left > title.getBoundingClientRect().left : false,
          hasBuyerBlock: !!buyer,
          buyerBadgeRight: !!buyer?.querySelector('.ob-mobile-buyerline__badge')
            && buyer.querySelector('.ob-mobile-buyerline__badge').getBoundingClientRect().left > buyer.querySelector('.ob-mobile-buyerline__main').getBoundingClientRect().right,
          buyerRows,
          gridColumns: grid ? getComputedStyle(grid).gridTemplateColumns.split(' ').length : 0,
          gridText: grid?.textContent?.trim().replace(/\s+/g, ' ') || '',
          amountGap: amountLabel && amountValue
            ? Math.round(amountValue.getBoundingClientRect().left - amountLabel.getBoundingClientRect().right)
            : -1,
          amountLabelTransform: amountLabel ? getComputedStyle(amountLabel).textTransform : '',
          hasPaymentChip: !!payment,
          paymentText: payment?.textContent?.trim() || '',
          paymentLabelText: paymentLabel?.textContent?.trim() || '',
          hasTicketsBlock: !!tickets,
          ticketTitle: ticketTitle?.textContent?.trim() || '',
          cardHeight: first ? Math.round(first.getBoundingClientRect().height) : 0,
          actionLabels,
          buttonHeights,
          allDataFonts: dataFonts.length > 0 && dataFonts.every((font) => font.includes('IBM Plex Mono')),
          order,
          overflowX: document.documentElement.scrollWidth > window.innerWidth,
        };
      });

      expect(geom.cardCount).toBeGreaterThan(0);
      expect(geom.panelFlat).toBe(true);
      expect(geom.titleText.length).toBeGreaterThan(44);
      expect(geom.titleText).not.toContain('...');
      expect(['none', 'initial', '']).toContain(geom.titleClamp);
      expect(geom.titleOverflow).not.toBe('ellipsis');
      expect(geom.headingLines).toBeGreaterThanOrEqual(2);
      expect(geom.refInHeading).toBe(true);
      expect(geom.headingText).toContain('#');
      expect(geom.metaText).not.toMatch(/funci[oó]n/i);
      expect(geom.metaHasDataFont).toBe(false);
      expect(geom.statusRightColumn).toBe(true);
      expect(geom.hasBuyerBlock).toBe(true);
      expect(geom.buyerBadgeRight).toBe(true);
      expect(geom.buyerRows).toBeLessThanOrEqual(2);
      expect(geom.gridColumns).toBe(2);
      expect(geom.gridText).toMatch(/Neto/);
      expect(geom.gridText).not.toMatch(/Recib/i);
      expect(geom.amountGap).toBeGreaterThanOrEqual(0);
      expect(geom.amountGap).toBeLessThanOrEqual(8);
      expect(geom.amountLabelTransform).toBe('none');
      expect(geom.hasPaymentChip).toBe(true);
      expect(geom.paymentLabelText).toMatch(/pago/i);
      expect(geom.paymentText).toMatch(/mercado|pago|-/i);
      expect(geom.hasTicketsBlock).toBe(true);
      expect(geom.ticketTitle).not.toMatch(/^entrada\s+/i);
      expect(geom.cardHeight).toBeLessThan(410);
      expect(geom.order[0]).toContain('ob-mobile-booking__head');
      expect(geom.order[1]).toContain('ob-mobile-buyerline');
      expect(geom.order[2]).toContain('ob-mobile-booking__grid');
      expect(geom.actionLabels.every((label) => /reserva/i.test(label))).toBe(true);
      expect(geom.buttonHeights.every((height) => height >= 40)).toBe(true);
      expect(geom.allDataFonts).toBe(true);
      expect(geom.overflowX).toBe(false);
    }
  });

  test('@theme reservas evento mobile: tipos de entrada con icono y tonos', async ({ page }) => {
    await login(page);
    await page.setViewportSize({ width: 503, height: 872 });

    for (const theme of ['light', 'dark']) {
      await page.goto('/organizer/event-booking/evento/119', { waitUntil: 'networkidle' });
      await setTheme(page, theme);

      const geom = await page.evaluate(() => {
        const cards = Array.from(document.querySelectorAll('.ob-ticket-card'));
        const first = cards[0];
        const head = first?.querySelector('.ob-ticket-card__head');
        const icon = first?.querySelector('.ob-ticket-card__icon');
        const title = first?.querySelector('.ob-ticket-card__title');
        const badge = first?.querySelector('.ob-ticket-card__badge');
        const dataFonts = first ? Array.from(first.querySelectorAll('.tuki-data, .ob-ticket-card__money, .ob-ticket-stat__value')).map((el) => getComputedStyle(el).fontFamily) : [];
        const tones = cards.map((card) => Array.from(card.classList).find((className) => className.startsWith('ob-ticket-card--'))).filter(Boolean);

        return {
          cardCount: cards.length,
          tones,
          headColumns: head ? getComputedStyle(head).gridTemplateColumns.split(' ').length : 0,
          iconSize: icon ? [Math.round(icon.getBoundingClientRect().width), Math.round(icon.getBoundingClientRect().height)] : null,
          hasIconGlyph: !!icon?.querySelector('i.fas'),
          titleText: title?.textContent?.trim() || '',
          titleLines: title ? Math.round(title.getBoundingClientRect().height / parseFloat(getComputedStyle(title).lineHeight)) : 0,
          badgeText: badge?.textContent?.trim() || '',
          badgeRight: !!badge && !!title && badge.getBoundingClientRect().left > title.getBoundingClientRect().left,
          allDataFonts: dataFonts.length > 0 && dataFonts.every((font) => font.includes('IBM Plex Mono')),
          overflowX: document.documentElement.scrollWidth > window.innerWidth,
        };
      });

      expect(geom.cardCount).toBeGreaterThan(0);
      expect(new Set(geom.tones).size).toBeGreaterThanOrEqual(2);
      expect(geom.headColumns).toBe(3);
      expect(geom.iconSize).toEqual([54, 54]);
      expect(geom.hasIconGlyph).toBe(true);
      expect(geom.titleText.length).toBeGreaterThan(3);
      expect(geom.titleLines).toBeLessThanOrEqual(2);
      expect(geom.badgeText.length).toBeGreaterThan(0);
      expect(geom.badgeRight).toBe(true);
      expect(geom.allDataFonts).toBe(true);
      expect(geom.overflowX).toBe(false);
    }
  });

  test('@theme reserva detalle mobile: entradas usan card citrus', async ({ page }) => {
    await login(page);
    await page.setViewportSize({ width: 503, height: 872 });

    for (const theme of ['light', 'dark']) {
      await page.goto('/organizer/event-booking/details/214', { waitUntil: 'networkidle' });
      await setTheme(page, theme);

      const geom = await page.evaluate(() => {
        const panel = document.querySelector('#bod-tickets-title')?.closest('.bod-panel');
        const table = panel?.querySelector('.bod-table--tickets');
        const cards = Array.from(panel?.querySelectorAll('.bod-ticket-mobile-card') || []);
        const first = cards[0];
        const head = first?.querySelector('.bod-ticket-mobile-card__head');
        const thumb = first?.querySelector('.bod-ticket-thumb');
        const title = first?.querySelector('.bod-ticket-mobile-card__title');
        const badges = first ? Array.from(first.querySelectorAll('.bod-ticket-mobile-card__badges .bod-pill')) : [];
        const grid = first?.querySelector('.bod-ticket-mobile-grid');
        const progress = first?.querySelector('.bod-progress');
        const dataFonts = first ? Array.from(first.querySelectorAll('.tuki-data, .bod-money, .bod-data-value')).map((el) => getComputedStyle(el).fontFamily) : [];
        const cardStyles = first ? getComputedStyle(first) : null;

        return {
          cardCount: cards.length,
          tableDisplay: table ? getComputedStyle(table).display : null,
          headColumns: head ? getComputedStyle(head).gridTemplateColumns.split(' ').length : 0,
          thumbSize: thumb ? [Math.round(thumb.getBoundingClientRect().width), Math.round(thumb.getBoundingClientRect().height)] : null,
          titleText: title?.textContent?.trim() || '',
          titleLines: title ? Math.round(title.getBoundingClientRect().height / parseFloat(getComputedStyle(title).lineHeight)) : 0,
          badgesRight: badges.length > 0 && title ? badges.every((badge) => badge.getBoundingClientRect().left > title.getBoundingClientRect().left) : false,
          gridColumns: grid ? getComputedStyle(grid).gridTemplateColumns.split(' ').length : 0,
          progressWidth: progress ? Math.round(progress.getBoundingClientRect().width) : 0,
          cardRadius: cardStyles?.borderTopLeftRadius || '',
          allDataFonts: dataFonts.length > 0 && dataFonts.every((font) => font.includes('IBM Plex Mono')),
          overflowX: document.documentElement.scrollWidth > window.innerWidth,
          text: first?.textContent?.trim().replace(/\s+/g, ' ') || '',
        };
      });

      expect(geom.cardCount).toBeGreaterThan(0);
      expect(geom.tableDisplay).toBe('none');
      expect(geom.headColumns).toBe(3);
      expect(geom.thumbSize).toEqual([54, 54]);
      expect(geom.titleText).not.toMatch(/^entrada\s+/i);
      expect(geom.titleLines).toBeLessThanOrEqual(2);
      expect(geom.badgesRight).toBe(true);
      expect(geom.gridColumns).toBe(2);
      expect(geom.progressWidth).toBeGreaterThan(100);
      expect(geom.cardRadius).toBe('16px');
      expect(geom.text).toMatch(/Subtotal/i);
      expect(geom.text).toMatch(/Escaneo/i);
      expect(geom.allDataFonts).toBe(true);
      expect(geom.overflowX).toBe(false);
    }
  });

  test('@theme reservas contraste WCAG AA en light y dark', async ({ page }) => {
    await login(page);
    await page.setViewportSize({ width: 538, height: 872 });

    const selectors = [
      '.organizer-booking-admin .page-title',
      '.ob-context-note',
      '.ob-metric__label',
      '.ob-metric__value',
      '.ob-metric__hint',
      '.ob-type-summary__title',
      '.ob-muted',
      '.ob-type-summary__formula',
      '.ob-event-row__title',
      '.ob-event-row__date',
      '.ob-event-row__category',
      '.ob-event-row__badge',
      '.ob-event-row__badge--status',
      '.ob-event-row__badge--type',
      '.ob-event-row__label',
      '.ob-event-row__value',
      '.ob-event-row__muted',
      '.ob-event-row__money',
      '.ob-event-row__cta',
    ];

    for (const theme of ['light', 'dark']) {
      await page.goto('/organizer/event-booking', { waitUntil: 'networkidle' });
      await setTheme(page, theme);

      const audit = await page.evaluate((selectors) => {
        const parseColor = (value) => {
          const nums = String(value).match(/[\d.]+/g)?.map(Number) || [];
          if (nums.length < 3) return null;
          return {
            r: nums[0] <= 1 ? nums[0] * 255 : nums[0],
            g: nums[1] <= 1 ? nums[1] * 255 : nums[1],
            b: nums[2] <= 1 ? nums[2] * 255 : nums[2],
            a: nums.length >= 4 ? nums[3] : 1,
          };
        };
        const composite = (top, bottom) => ({
          r: top.r * top.a + bottom.r * (1 - top.a),
          g: top.g * top.a + bottom.g * (1 - top.a),
          b: top.b * top.a + bottom.b * (1 - top.a),
          a: 1,
        });
        const luminance = ({ r, g, b }) => {
          const linear = [r, g, b].map((value) => {
            value /= 255;
            return value <= 0.03928 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
          });
          return 0.2126 * linear[0] + 0.7152 * linear[1] + 0.0722 * linear[2];
        };
        const contrast = (fg, bg) => {
          const f = luminance(fg);
          const b = luminance(bg);
          return (Math.max(f, b) + 0.05) / (Math.min(f, b) + 0.05);
        };
        const effectiveBg = (el) => {
          const layers = [];
          for (let node = el; node && node.nodeType === 1; node = node.parentElement) {
            const parsed = parseColor(getComputedStyle(node).backgroundColor);
            if (parsed && parsed.a > 0) layers.push(parsed);
          }
          return layers.reverse().reduce((bg, layer) => composite(layer, bg), { r: 255, g: 255, b: 255, a: 1 });
        };

        return selectors.map((selector) => {
          const el = document.querySelector(selector);
          if (!el) return { selector, missing: true };
          const fg = parseColor(getComputedStyle(el).color);
          const bg = effectiveBg(el);
          return {
            selector,
            ratio: fg ? contrast(fg, bg) : 0,
          };
        });
      }, selectors);

      const failures = audit.filter((item) => item.missing || item.ratio < 4.5);
      expect(failures, `contraste AA texto en reservas ${theme}: ${JSON.stringify(failures)}`).toEqual([]);

      await page.locator('.ob-event-row').first().focus();
      const focus = await page.locator('.ob-event-row').first().evaluate((el) => ({
        borderColor: getComputedStyle(el).borderColor,
      }));

      expect(focus.borderColor, `borde de foco visible reservas ${theme}`).not.toBe('rgba(0, 0, 0, 0)');
    }
  });

  test('@theme sidebar: iconos de items activos = token (nunca #1572E8)', async ({ page }) => {
    await login(page);
    await page.goto('/organizer/event-management/events?language=es&event_type=venue', { waitUntil: 'load' });
    await setTheme(page, 'dark');

    // Presencia real: si el login no persistió (página pública), FALLA.
    await expect(page.locator('.sidebar')).toBeVisible();

    const colors = await page.evaluate(() => {
      const active = document.querySelector('.sidebar .nav-collapse li.active a i');
      const activeParent = document.querySelector('.sidebar .nav > .nav-item.active a i');
      const brothers = Array.from(document.querySelectorAll('.sidebar .nav-collapse a i'))
        .filter((i) => i.getBoundingClientRect().width > 0 && !i.closest('li').classList.contains('active'))
        .slice(0, 3)
        .map((i) => getComputedStyle(i).color);
      return {
        active: active ? getComputedStyle(active).color : null,
        activeParent: activeParent ? getComputedStyle(activeParent).color : null,
        brothers,
      };
    });

    // Presencia real (nunca vacuo): si el icono no existe, el test FALLA.
    expect(colors.active, 'icono de sub-item activo debe existir').toBeTruthy();
    expect(colors.activeParent, 'icono del item raíz activo debe existir').toBeTruthy();
    expect(colors.active).not.toBe('rgb(21, 114, 232)');
    expect(colors.activeParent).not.toBe('rgb(21, 114, 232)');
    for (const c of colors.brothers) {
      expect(c).not.toBe('rgb(21, 114, 232)');
    }
  });
});
