// TukiPass — Audit baseline capture (Citrus Console)
// Uso: node scripts/audit-baseline.mjs
// Genera screenshots + mediciones DOM en docs/auditorias/admin-citrus-console-2026-08-22/baseline/
import { chromium } from '@playwright/test';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(__dirname, '..');
const BASE_URL = process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8801';
const OUT_DIR = path.join(ROOT, 'docs/auditorias/admin-citrus-console-2026-08-22/baseline');

const ORG_USER = process.env.E2E_ORGANIZER_USERNAME || 'audit-citrus';
const ORG_PASS = process.env.E2E_ORGANIZER_PASSWORD || 'AuditCitrus2026!';
const ADM_USER = process.env.E2E_ADMIN_USERNAME || 'adminaudit';
const ADM_PASS = process.env.E2E_ADMIN_PASSWORD || 'AdminAudit2026!';

const VIEWPORTS = [
  [1440, 900], [1366, 768], [1280, 800], [1024, 768], [768, 1024],
  [430, 932], [390, 844], [375, 812], [360, 800],
];

const ORGANIZER_ROUTES = [
  { slug: 'dashboard', url: '/organizer/dashboard' },
  { slug: 'events-list', url: '/organizer/event-management/events' },
  { slug: 'wizard', url: '/organizer/add-event?type=venue', wizard: true },
  { slug: 'edit-event', url: '/organizer/edit-event/126' },
  { slug: 'bookings', url: '/organizer/event-booking' },
  { slug: 'transactions', url: '/organizer/transaction' },
  { slug: 'profile', url: '/organizer/edit-profile' },
  { slug: 'support', url: '/organizer/support-tikcet/tickets' },
];

const ADMIN_ROUTES = [
  { slug: 'dashboard', url: '/admin/dashboard' },
  { slug: 'events-list', url: '/admin/event-management/events' },
  { slug: 'add-event', url: '/admin/add-event' },
];

const DARK_ROUTES = [
  { slug: 'org-dashboard', url: '/organizer/dashboard', panel: 'organizer' },
  { slug: 'org-wizard', url: '/organizer/add-event?type=venue', panel: 'organizer', wizard: true },
  { slug: 'org-edit-event', url: '/organizer/edit-event/126', panel: 'organizer' },
  { slug: 'adm-dashboard', url: '/admin/dashboard', panel: 'admin' },
  { slug: 'adm-add-event', url: '/admin/add-event', panel: 'admin' },
];

async function nativeFill(page, selector, value) {
  await page.evaluate(({ sel, val }) => {
    const set = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value').set;
    const el = document.querySelector(sel);
    set.call(el, val);
    el.dispatchEvent(new Event('input', { bubbles: true }));
  }, { sel: selector, val: value });
}

async function organizerLogin(page) {
  for (let attempt = 1; attempt <= 3; attempt++) {
    await page.goto(`${BASE_URL}/organizer/login`, { waitUntil: 'load' });
    const alreadyIn = await page.evaluate(() => !!document.querySelector('.sidebar'));
    if (alreadyIn) return;
    await nativeFill(page, '#username', ORG_USER);
    await nativeFill(page, '#password', ORG_PASS);
    await page.getByRole('button', { name: /Ingresar al panel/i }).click();
    await page.waitForURL('**/organizer/dashboard', { timeout: 15000 }).catch(() => {});
    const authed = await page.evaluate(() => !!document.querySelector('.sidebar'));
    if (authed) return;
  }
  throw new Error('No se pudo autenticar al organizer');
}

async function adminLogin(page) {
  await page.goto(`${BASE_URL}/admin`, { waitUntil: 'load' });
  const alreadyIn = await page.evaluate(() => !!document.querySelector('.sidebar'));
  if (alreadyIn) return;
  await nativeFill(page, 'input[name="username"]', ADM_USER);
  await nativeFill(page, 'input[name="password"]', ADM_PASS);
  await page.click('form button[type="submit"], form input[type="submit"]');
  await page.waitForURL('**/admin/**', { timeout: 15000 }).catch(() => {});
  const authed = await page.evaluate(() => !!document.querySelector('.sidebar'));
  if (!authed) throw new Error('No se pudo autenticar al admin');
}

async function measure(page) {
  return page.evaluate(() => {
    const doc = document.documentElement;
    const offenders = [];
    const els = document.querySelectorAll('body *');
    const vw = doc.clientWidth;
    for (const el of els) {
      const r = el.getBoundingClientRect();
      if (r.width > 0 && r.right > vw + 1 && r.left < vw) {
        const cls = (el.className && typeof el.className === 'string') ? el.className.split(' ').slice(0, 2).join('.') : '';
        offenders.push({ tag: el.tagName.toLowerCase(), cls, right: Math.round(r.right) });
        if (offenders.length >= 8) break;
      }
    }
    const gs = (sel) => {
      const el = document.querySelector(sel);
      if (!el) return null;
      const cs = getComputedStyle(el);
      return { height: cs.height, radius: cs.borderRadius, padding: cs.padding, fontSize: cs.fontSize, fontWeight: cs.fontWeight, display: cs.display };
    };
    const rootStyle = getComputedStyle(doc);
    return {
      scrollWidth: doc.scrollWidth,
      clientWidth: doc.clientWidth,
      overflowCount: offenders.length,
      offenders,
      tokens: {
        admPrimary: rootStyle.getPropertyValue('--adm-primary').trim(),
        surfacePage: rootStyle.getPropertyValue('--surface-page').trim(),
        surfaceCard: rootStyle.getPropertyValue('--surface-card').trim(),
        textPrimary: rootStyle.getPropertyValue('--text-primary').trim(),
      },
      bodyFont: getComputedStyle(document.body).fontFamily,
      samples: { btn: gs('.btn'), formControl: gs('.form-control'), card: gs('.card'), badge: gs('.badge') },
    };
  });
}

async function measureWizard(page) {
  return page.evaluate(() => {
    const modal = document.querySelector('.event-wizard-modal');
    if (!modal) return null;
    const content = modal.querySelector('.modal-content');
    const header = modal.querySelector('.event-wizard__titlebar');
    const body = modal.querySelector('.modal-body');
    const footer = modal.querySelector('.modal-footer');
    const stepper = modal.querySelector('.event-wizard-stepper');
    const node = modal.querySelector('.event-wizard-stepper__node');
    const nextBtn = modal.querySelector('#ewNextBtn');
    const rect = (el) => {
      if (!el) return null;
      const r = el.getBoundingClientRect();
      const cs = getComputedStyle(el);
      return { x: Math.round(r.x), y: Math.round(r.y), w: Math.round(r.width), h: Math.round(r.height), radius: cs.borderRadius, maxHeight: cs.maxHeight };
    };
    return {
      content: rect(content), header: rect(header), body: rect(body), footer: rect(footer),
      stepper: rect(stepper), node: rect(node), nextBtn: rect(nextBtn),
      labelSize: node ? getComputedStyle(node).fontSize : null,
    };
  });
}

async function settle(page) {
  await page.waitForTimeout(700);
}

function slugify(s) { return s.replace(/[^a-z0-9-]+/gi, '-').toLowerCase(); }

async function openWizard(page) {
  const btn = page.locator('#ewOpenWizardBtn');
  if (await btn.count()) {
    await btn.click().catch(() => {});
    await page.waitForTimeout(800);
  }
}

async function screenshot(page, panel, slug, w, h, suffix = '') {
  const dir = path.join(OUT_DIR, panel, slug);
  fs.mkdirSync(dir, { recursive: true });
  const file = `${w}x${h}${suffix}.png`;
  await page.screenshot({ path: path.join(dir, file), fullPage: true });
  return `docs/auditorias/admin-citrus-console-2026-08-22/baseline/${panel}/${slug}/${file}`;
}

const results = [];
const errors = [];

async function run() {
  fs.mkdirSync(OUT_DIR, { recursive: true });
  const browser = await chromium.launch();
  console.log(`Base URL: ${BASE_URL}`);

  // ---- single login per panel → storageState reutilizado (evita throttle /admin/auth 5/min) ----
  async function getState(panel) {
    const stateFile = path.join(OUT_DIR, `.state-${panel}.json`);
    if (fs.existsSync(stateFile)) return stateFile;
    const context = await browser.newContext({ viewport: { width: 1440, height: 900 }, locale: 'es-AR' });
    const page = await context.newPage();
    if (panel === 'organizer') await organizerLogin(page);
    else await adminLogin(page);
    await context.storageState({ path: stateFile });
    await context.close();
    return stateFile;
  }

  const orgState = await getState('organizer');
  const admState = await getState('admin');
  console.log('Sesiones guardadas:', path.basename(orgState), path.basename(admState));

  // ---- light pass: viewport matrix ----
  for (const [w, h] of VIEWPORTS) {
    const context = await browser.newContext({ viewport: { width: w, height: h }, locale: 'es-AR', storageState: orgState });
    const page = await context.newPage();
    const row = { viewport: `${w}x${h}`, organizer: [], admin: [] };
    try {
      for (const route of ORGANIZER_ROUTES) {
        try {
          await page.goto(BASE_URL + route.url, { waitUntil: 'load' });
          await settle(page);
          if (route.wizard) await openWizard(page);
          const m = await measure(page);
          const wiz = route.wizard ? await measureWizard(page) : null;
          const shot = await screenshot(page, 'organizer', route.slug, w, h);
          row.organizer.push({ slug: route.slug, overflow: m.overflowCount, m, wiz, shot });
          console.log(`  [org] ${route.slug} @ ${w}x${h} overflow=${m.overflowCount}`);
        } catch (e) {
          errors.push(`organizer/${route.slug} @ ${w}x${h}: ${e.message.split('\n')[0]}`);
          console.log(`  [org] ${route.slug} @ ${w}x${h} ERROR: ${e.message.split('\n')[0]}`);
        }
      }
    } catch (e) {
      errors.push(`organizer @ ${w}x${h}: ${e.message.split('\n')[0]}`);
    }
    await context.close();

    const admContext = await browser.newContext({ viewport: { width: w, height: h }, locale: 'es-AR', storageState: admState });
    const admPage = await admContext.newPage();
    try {
      for (const route of ADMIN_ROUTES) {
        try {
          await admPage.goto(BASE_URL + route.url, { waitUntil: 'load' });
          await settle(admPage);
          const m = await measure(admPage);
          const shot = await screenshot(admPage, 'admin', route.slug, w, h);
          row.admin.push({ slug: route.slug, overflow: m.overflowCount, m, shot });
          console.log(`  [adm] ${route.slug} @ ${w}x${h} overflow=${m.overflowCount}`);
        } catch (e) {
          errors.push(`admin/${route.slug} @ ${w}x${h}: ${e.message.split('\n')[0]}`);
          console.log(`  [adm] ${route.slug} @ ${w}x${h} ERROR: ${e.message.split('\n')[0]}`);
        }
      }
    } catch (e) {
      errors.push(`admin @ ${w}x${h}: ${e.message.split('\n')[0]}`);
    }
    results.push(row);
    await admContext.close();
  }

  // ---- low-height pass (wizard) ----
  for (const [w, h] of [[1366, 650], [375, 667]]) {
    const context = await browser.newContext({ viewport: { width: w, height: h }, locale: 'es-AR', storageState: orgState });
    const page = await context.newPage();
    try {
      await page.goto(`${BASE_URL}/organizer/add-event?type=venue`, { waitUntil: 'load' });
      await settle(page);
      await openWizard(page);
      const wiz = await measureWizard(page);
      const shot = await screenshot(page, 'organizer', 'wizard', w, h, '-lowheight');
      results.push({ viewport: `${w}x${h}`, lowHeightWizard: { wiz, shot } });
      console.log(`  [low] wizard @ ${w}x${h} contentH=${wiz?.content?.h}`);
    } catch (e) {
      errors.push(`lowheight ${w}x${h}: ${e.message.split('\n')[0]}`);
    }
    await context.close();
  }

  // ---- dark pass ----
  for (const [w, h] of [[1440, 900], [390, 844]]) {
    const context = await browser.newContext({ viewport: { width: w, height: h }, locale: 'es-AR', storageState: orgState });
    await context.addInitScript(() => {
      localStorage.setItem('tuki-theme', 'dark');
    });
    const page = await context.newPage();
    try {
      for (const route of DARK_ROUTES.filter((r) => r.panel === 'organizer')) {
        await page.goto(BASE_URL + route.url, { waitUntil: 'load' });
        await page.evaluate(() => { document.documentElement.dataset.theme = 'dark'; document.body.setAttribute('data-background-color', 'dark'); });
        await settle(page);
        if (route.wizard) await openWizard(page);
        const m = await measure(page);
        const shot = await screenshot(page, 'organizer', `${route.slug}-dark`, w, h);
        results.push({ viewport: `${w}x${h}`, dark: { slug: route.slug, overflow: m.overflowCount, shot } });
        console.log(`  [dark-org] ${route.slug} @ ${w}x${h} overflow=${m.overflowCount}`);
      }
    } catch (e) {
      errors.push(`dark organizer @ ${w}x${h}: ${e.message.split('\n')[0]}`);
    }
    await context.close();

    const admContext = await browser.newContext({ viewport: { width: w, height: h }, locale: 'es-AR', storageState: admState });
    await admContext.addInitScript(() => {
      localStorage.setItem('tuki-theme', 'dark');
    });
    const admPage = await admContext.newPage();
    try {
      for (const route of DARK_ROUTES.filter((r) => r.panel === 'admin')) {
        await admPage.goto(BASE_URL + route.url, { waitUntil: 'load' });
        await admPage.evaluate(() => { document.documentElement.dataset.theme = 'dark'; document.body.setAttribute('data-background-color', 'dark'); });
        await settle(admPage);
        const m = await measure(admPage);
        const shot = await screenshot(admPage, 'admin', `${route.slug}-dark`, w, h);
        results.push({ viewport: `${w}x${h}`, dark: { slug: route.slug, overflow: m.overflowCount, shot } });
        console.log(`  [dark-adm] ${route.slug} @ ${w}x${h} overflow=${m.overflowCount}`);
      }
    } catch (e) {
      errors.push(`dark admin @ ${w}x${h}: ${e.message.split('\n')[0]}`);
    }
    await admContext.close();
  }

  await browser.close();

  fs.writeFileSync(path.join(OUT_DIR, 'measurements.json'), JSON.stringify({ generated: new Date().toISOString(), baseUrl: BASE_URL, results, errors }, null, 2));
  console.log(`\nBaseline completo. Screenshots y mediciones en ${OUT_DIR}`);
  console.log(`Errores: ${errors.length}`);
  errors.forEach((e) => console.log('  ERR: ' + e));
}

run().catch((e) => { console.error(e); process.exit(1); });
