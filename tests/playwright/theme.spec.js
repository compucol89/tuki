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

const USER = { username: 'Rumba Colombiana', password: '1234567890' };

async function login(page) {
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
