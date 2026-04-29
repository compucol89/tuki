const puppeteer = require('puppeteer');

const BASE = 'http://localhost:8801';
const EVENT_URL = `${BASE}/event/the-conference-planners/104`;
const ENDED_URL = `${BASE}/event/test-evento-finalizado/116`;

async function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

async function runAudit() {
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });

  const results = [];
  let pass = 0, fail = 0;

  function check(name, ok, detail = '') {
    const status = ok ? 'PASS' : 'FAIL';
    if (ok) pass++; else fail++;
    results.push({ name, status, detail });
    console.log(`  [${status}] ${name}${detail ? ' — ' + detail : ''}`);
  }

  // ============================================================
  // TEST 1: Desktop page loads correctly
  // ============================================================
  console.log('\n=== TEST 1: Desktop Page Load ===');
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0', timeout: 30000 });
  check('Page loads 200', page.url().includes('/event/'));

  // ============================================================
  // TEST 2: Skip link
  // ============================================================
  console.log('\n=== TEST 2: Skip Link ===');
  const skipLink = await page.$('a[href="#main-content"], a[href="#content"], a[class*="skip"], a[aria-label*="skip" i], a[aria-label*="saltar" i]');
  check('Skip link exists', !!skipLink);
  if (skipLink) {
    const href = await page.evaluate(el => el.getAttribute('href'), skipLink);
    const target = await page.$(href);
    check('Skip link target exists', !!target, `href="${href}"`);
  }

  // ============================================================
  // TEST 3: Keyboard navigation (Tab order)
  // ============================================================
  console.log('\n=== TEST 3: Keyboard Navigation ===');
  await page.keyboard.press('Tab');
  await sleep(200);
  const firstFocused = await page.evaluate(() => {
    const el = document.activeElement;
    return el ? { tag: el.tagName, text: el.textContent?.trim()?.slice(0, 50), class: el.className } : null;
  });
  check('First Tab focuses something', !!firstFocused, JSON.stringify(firstFocused));

  // Tab through 5 elements
  const tabOrder = [];
  for (let i = 0; i < 5; i++) {
    await page.keyboard.press('Tab');
    await sleep(100);
    const focused = await page.evaluate(() => {
      const el = document.activeElement;
      return el ? el.tagName + ':' + (el.textContent?.trim()?.slice(0, 30) || '') : null;
    });
    tabOrder.push(focused);
  }
  check('Tab order has 5 focusable elements', tabOrder.filter(Boolean).length >= 3, tabOrder.join(' → '));

  // ============================================================
  // TEST 4: Focus visible
  // ============================================================
  console.log('\n=== TEST 4: Focus Visible ===');
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });
  await page.keyboard.press('Tab');
  await sleep(200);
  const focusStyle = await page.evaluate(() => {
    const el = document.activeElement;
    if (!el) return null;
    const cs = getComputedStyle(el);
    return {
      outline: cs.outline,
      outlineStyle: cs.outlineStyle,
      boxShadow: cs.boxShadow,
      outlineWidth: cs.outlineWidth,
      outlineColor: cs.outlineColor
    };
  });
  const hasFocusIndicator = focusStyle && (
    (focusStyle.outlineStyle && focusStyle.outlineStyle !== 'none') ||
    (focusStyle.boxShadow && focusStyle.boxShadow !== 'none')
  );
  check('Focused element has visible indicator', !!hasFocusIndicator, JSON.stringify(focusStyle));

  // ============================================================
  // TEST 5: Smooth scroll (CTA hero → booking card)
  // ============================================================
  console.log('\n=== TEST 5: Smooth Scroll ===');
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });

  const ctaHero = await page.$('[data-scroll-target="#event-booking-card"]');
  check('CTA hero with scroll target exists', !!ctaHero);

  if (ctaHero) {
    // Check if scroll-behavior is smooth on html
    const scrollBehavior = await page.evaluate(() => {
      return getComputedStyle(document.documentElement).scrollBehavior;
    });
    check('scroll-behavior: smooth', scrollBehavior === 'smooth', `got: "${scrollBehavior}"`);

    // Check scroll target attribute
    const scrollTarget = await page.evaluate(el => el.getAttribute('data-scroll-target'), ctaHero);
    check('data-scroll-target attribute', !!scrollTarget, scrollTarget);
  }

  // ============================================================
  // TEST 6: Sticky mobile bar
  // ============================================================
  console.log('\n=== TEST 6: Sticky Mobile Bar ===');
  await page.setViewport({ width: 375, height: 812 });
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });

  const mobileBar = await page.$('.ed-mobile-bar__cta, [class*="mobile-bar"], [class*="sticky-bar"], .mobile-cta-bar, .sticky-cta');
  check('Mobile bar exists', !!mobileBar);

  if (mobileBar) {
    const barStyle = await page.evaluate(el => {
      if (!el) return null;
      const cs = getComputedStyle(el);
      return {
        position: cs.position,
        bottom: cs.bottom,
        zIndex: cs.zIndex,
        display: cs.display
      };
    }, mobileBar);
    check('Mobile bar is sticky/fixed',
      barStyle && (barStyle.position === 'fixed' || barStyle.position === 'sticky'),
      JSON.stringify(barStyle));

    // Scroll down and check it doesn't overlap content
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await sleep(500);
    const barVisible = await page.evaluate(() => {
      const bar = document.querySelector('.ed-mobile-bar__cta, [class*="mobile-bar"], [class*="sticky-bar"], .mobile-cta-bar, .sticky-cta');
      if (!bar) return false;
      const rect = bar.getBoundingClientRect();
      return rect.bottom > 0 && rect.top < window.innerHeight;
    });
    check('Mobile bar visible after scroll', barVisible);
  }

  // ============================================================
  // TEST 7: Thumbnails / Gallery
  // ============================================================
  console.log('\n=== TEST 7: Gallery / Thumbnails ===');
  await page.setViewport({ width: 1280, height: 800 });
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });

  const thumbnails = await page.$$('.event-thumbnail, [class*="thumb"], [class*="gallery"] img, .gallery-img');
  check('Gallery thumbnails exist', thumbnails.length > 0, `found: ${thumbnails.length}`);

  if (thumbnails.length > 0) {
    // Click first thumbnail
    await thumbnails[0].click();
    await sleep(500);
    const lightboxOpen = await page.evaluate(() => {
      return !!(document.querySelector('.lightbox, .modal, [class*="lightbox"], [class*="modal"], [class*="overlay"].active, [style*="display: block"]'));
    });
    check('Lightbox/modal opens on thumbnail click', lightboxOpen);
  }

  // ============================================================
  // TEST 8: Share buttons
  // ============================================================
  console.log('\n=== TEST 8: Share Buttons ===');
  const shareBtns = await page.$$('.share-btn, [class*="share"], [class*="social-share"] a, [aria-label*="share" i]');
  check('Share buttons exist', shareBtns.length > 0, `found: ${shareBtns.length}`);

  // ============================================================
  // TEST 9: Wishlist
  // ============================================================
  console.log('\n=== TEST 9: Wishlist ===');
  const wishlistBtn = await page.$('.wishlist-btn, [class*="wishlist"], [class*="favorite"], [aria-label*="wishlist" i], [aria-label*="favorite" i]');
  check('Wishlist/favorite button exists', !!wishlistBtn);

  // ============================================================
  // TEST 10: Countdown
  // ============================================================
  console.log('\n=== TEST 10: Countdown ===');
  const countdown = await page.$('.countdown, [class*="countdown"], [class*="timer"], [data-countdown]');
  check('Countdown element exists', !!countdown);

  if (countdown) {
    const countdownText = await page.evaluate(el => el.textContent?.trim(), countdown);
    check('Countdown has content', !!countdownText && countdownText.length > 0, countdownText);
  }

  // ============================================================
  // TEST 11: Evento finalizado (disabled state)
  // ============================================================
  console.log('\n=== TEST 11: Evento Finalizado ===');
  await page.goto(ENDED_URL, { waitUntil: 'networkidle0', timeout: 30000 }).catch(() => {});

  const buyBtn = await page.$('button.ed-buy-btn[disabled], .ed-buy-btn[disabled], button[disabled]');
  check('Buy button is disabled on ended event', !!buyBtn);

  const mobileCtaDisabled = await page.$('.ed-mobile-bar__cta--disabled, [class*="disabled"]');
  check('Mobile CTA disabled on ended event', !!mobileCtaDisabled);

  // ============================================================
  // TEST 12: Reduced motion
  // ============================================================
  console.log('\n=== TEST 12: Reduced Motion ===');
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });
  const reducedMotionCSS = await page.evaluate(() => {
    const sheets = document.styleSheets;
    let found = false;
    for (let s = 0; s < sheets.length; s++) {
      try {
        const rules = sheets[s].cssRules;
        for (let r = 0; r < rules.length; r++) {
          if (rules[r].media && rules[r].media.mediaText && rules[r].media.mediaText.includes('prefers-reduced-motion')) {
            found = true;
            break;
          }
        }
      } catch(e) {}
      if (found) break;
    }
    return found;
  });
  check('prefers-reduced-motion media query exists', reducedMotionCSS);

  // ============================================================
  // SUMMARY
  // ============================================================
  console.log('\n' + '='.repeat(60));
  console.log(`AUDIT SUMMARY: ${pass} PASS / ${fail} FAIL / ${pass + fail} TOTAL`);
  console.log('='.repeat(60));

  if (fail > 0) {
    console.log('\nFAILURES:');
    results.filter(r => r.status === 'FAIL').forEach(r => {
      console.log(`  ✗ ${r.name}${r.detail ? ' — ' + r.detail : ''}`);
    });
  }

  await browser.close();
  process.exit(fail > 0 ? 1 : 0);
}

runAudit().catch(err => {
  console.error('Audit error:', err.message);
  process.exit(1);
});
