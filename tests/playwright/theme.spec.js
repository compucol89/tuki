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
      const alpha = rgba.length >= 4 ? rgba[3] : 1;

      return { rgb, alpha };
    };
    const parseColor = (color) => {
      const values = (String(color).match(/[\d.]+/g) || []).map(Number);
      if (values.length < 3) return null;

      return normalize(values);
    };
    const luminance = ([r, g, b]) => {
      const convert = (channel) => {
        const value = channel / 255;

        return value <= 0.03928 ? value / 12.92 : ((value + 0.055) / 1.055) ** 2.4;
      };

      return 0.2126 * convert(r) + 0.7152 * convert(g) + 0.0722 * convert(b);
    };
    const isBrightSurface = (color, image) => {
      if (image && image !== 'none') {
        return false;
      }
      const parsed = parseColor(color);
      if (!parsed || parsed.alpha < 0.5) {
        return false;
      }

      return luminance(parsed.rgb) > 0.86;
    };

    const brightSurfaces = [];
    for (const selector of surfaceSelectors) {
      document.querySelectorAll(selector).forEach((el) => {
        const styles = getComputedStyle(el);
        if (isBrightSurface(styles.backgroundColor, styles.backgroundImage)) {
          brightSurfaces.push(`${selector} -> ${styles.backgroundColor}`);
        }
      });
    }

    return brightSurfaces;
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
    document.querySelectorAll('.card, .oe-panel, .oe-toolbar, .oe-metric, .oe-mobile-event, .ob-event-row, .ob-event-summary-card, .bod-panel, .bod-hero, .bod-kpi, .bod-ledger, .tb-card, .tb-token, .ticket-free-limit, .ticket-form-intro, .ticket-form-content-intro, .ticket-form-language .version, .event-cover-box, .ai-assistant-card, .ai-generate-panel, .ai-status-card, .async-progress-panel, .create-cover-ai-panel').forEach((el) => {
      const bg = getComputedStyle(el).backgroundColor;
      if (bg && bg !== 'rgba(0, 0, 0, 0)' && isWhite(bg)) {
        whiteSurfaces.push(el.className.toString().slice(0, 40));
      }
    });

    const darkText = [];
    document.querySelectorAll('.oe-panel__title, .oe-metric__value, .ob-event-row__title, .ob-detail-value, .bod-title, .bod-value, .bod-ticket-name, .tb-status, .ticket-form-header__title, .ai-status-card__title').forEach((el) => {
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
  for (const route of ROUTES) {
    for (const theme of ['light', 'dark']) {
      test(`${route.name} × ${theme} sin islas blancas / azules / texto oscuro`, async ({ page }) => {
        test.setTimeout(60_000);
        await login(page);
        await page.goto(route.path, { waitUntil: 'load' });
        await setTheme(page, theme);

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

  test('@theme sidebar: icono de sub-item activo = token (nunca #1572E8)', async ({ page }) => {
    await login(page);
    await page.goto('/organizer/event-management/events?language=es&event_type=venue', { waitUntil: 'load' });
    await setTheme(page, 'dark');

    const colors = await page.evaluate(() => {
      const active = document.querySelector('.sidebar .nav-collapse li.active a i');
      const brothers = Array.from(document.querySelectorAll('.sidebar .nav-collapse a i'))
        .filter((i) => i.getBoundingClientRect().width > 0 && !i.closest('li').classList.contains('active'))
        .slice(0, 3)
        .map((i) => getComputedStyle(i).color);
      return { active: active ? getComputedStyle(active).color : null, brothers };
    });

    expect(colors.active).not.toBe('rgb(21, 114, 232)');
    for (const c of colors.brothers) {
      expect(c).not.toBe('rgb(21, 114, 232)');
    }
  });
});
