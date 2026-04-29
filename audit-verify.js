const puppeteer = require('puppeteer');

const BASE = 'http://localhost:8801';
const EVENT_URL = `${BASE}/event/the-conference-planners/104`;

async function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

async function run() {
  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  const page = await browser.newPage();
  await page.setViewport({ width: 1280, height: 800 });

  // ============================================================
  // VERIFY 1: scroll-behavior smooth in CSS
  // ============================================================
  console.log('\n=== VERIFY: scroll-behavior ===');
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });

  // Check CSS rule directly (not computed, which may be overridden by reduced-motion)
  const cssScrollBehavior = await page.evaluate(() => {
    const sheets = document.styleSheets;
    for (let s = 0; s < sheets.length; s++) {
      try {
        const rules = sheets[s].cssRules;
        for (let r = 0; r < rules.length; r++) {
          if (rules[r].selectorText === 'html' && rules[r].style.scrollBehavior) {
            return rules[r].style.scrollBehavior;
          }
        }
      } catch(e) {}
    }
    return 'NOT FOUND';
  });
  console.log('  CSS rule html { scroll-behavior }:', cssScrollBehavior);

  // Check JS scroll handler
  const jsScrollSmooth = await page.evaluate(() => {
    const links = document.querySelectorAll('[data-scroll-target]');
    if (links.length === 0) return 'NO LINKS';
    // Check if click handler is registered by checking the onclick behavior
    const link = links[0];
    // Simulate: check if scrollIntoView is called with smooth
    // We can't directly check event listeners, but we can check the source
    const scripts = document.querySelectorAll('script');
    for (const s of scripts) {
      if (s.textContent && s.textContent.includes('scrollIntoView') && s.textContent.includes('smooth')) {
        return 'JS HANDLER WITH SMOOTH';
      }
    }
    return 'NO JS HANDLER';
  });
  console.log('  JS scroll handler:', jsScrollSmooth);

  // Check prefers-reduced-motion status in headless
  const reducedMotionActive = await page.evaluate(() => {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  });
  console.log('  prefers-reduced-motion active in headless:', reducedMotionActive);

  // ============================================================
  // VERIFY 2: Wishlist button with Spanish aria-label
  // ============================================================
  console.log('\n=== VERIFY: Wishlist/Favorites button ===');
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });

  // Check for the actual button in the HTML
  const wishlistInfo = await page.evaluate(() => {
    // Look for the hero wishlist button
    const btn = document.querySelector('.ed-hero__btn[aria-label*="favoritos" i], .ed-hero__btn[title*="favoritos" i]');
    if (!btn) {
      // Try broader search
      const btn2 = document.querySelector('a[href*="wishlist"]');
      if (btn2) {
        return {
          found: true,
          href: btn2.getAttribute('href'),
          ariaLabel: btn2.getAttribute('aria-label'),
          title: btn2.getAttribute('title'),
          classes: btn2.className,
          hasSrOnly: !!btn2.querySelector('.sr-only'),
          innerHTML: btn2.innerHTML.trim()
        };
      }
      return { found: false, reason: 'No wishlist link found' };
    }
    return {
      found: true,
      href: btn.getAttribute('href'),
      ariaLabel: btn.getAttribute('aria-label'),
      title: btn.getAttribute('title'),
      classes: btn.className,
      hasSrOnly: !!btn.querySelector('.sr-only'),
      innerHTML: btn.innerHTML.trim()
    };
  });
  console.log('  Wishlist button:', JSON.stringify(wishlistInfo, null, 2));

  // Also check all links with "wishlist" in href
  const wishlistLinks = await page.evaluate(() => {
    const links = document.querySelectorAll('a[href*="wishlist"]');
    return Array.from(links).map(l => ({
      href: l.getAttribute('href'),
      ariaLabel: l.getAttribute('aria-label'),
      title: l.getAttribute('title'),
      hasSrOnly: !!l.querySelector('.sr-only'),
      text: l.textContent?.trim()
    }));
  });
  console.log('  All wishlist links:', JSON.stringify(wishlistLinks, null, 2));

  // ============================================================
  // VERIFY 3: Skip link focus detail
  // ============================================================
  console.log('\n=== VERIFY: Skip link focus detail ===');
  await page.goto(EVENT_URL, { waitUntil: 'networkidle0' });
  await page.keyboard.press('Tab');
  await sleep(200);
  const skipFocusDetail = await page.evaluate(() => {
    const el = document.activeElement;
    if (!el || !el.classList.contains('skip-link')) return 'NOT FOCUSED ON SKIP LINK';
    const cs = getComputedStyle(el);
    return {
      top: cs.top,
      outline: cs.outline,
      outlineStyle: cs.outlineStyle,
      outlineWidth: cs.outlineWidth,
      boxShadow: cs.boxShadow,
      visible: cs.top !== '-48px' && cs.top !== 'auto'
    };
  });
  console.log('  Skip link focus state:', JSON.stringify(skipFocusDetail, null, 2));

  // ============================================================
  // VERIFY 4: Reduced motion CSS
  // ============================================================
  console.log('\n=== VERIFY: Reduced motion CSS ===');
  const reducedMotionCSS = await page.evaluate(() => {
    const sheets = document.styleSheets;
    let found = false;
    let details = [];
    for (let s = 0; s < sheets.length; s++) {
      try {
        const rules = sheets[s].cssRules;
        for (let r = 0; r < rules.length; r++) {
          if (rules[r].media && rules[r].media.mediaText && rules[r].media.mediaText.includes('prefers-reduced-motion')) {
            found = true;
            details.push({
              media: rules[r].media.mediaText,
              ruleCount: rules[r].cssRules?.length || 0,
              firstRule: rules[r].cssRules?.[0]?.cssText?.slice(0, 100)
            });
          }
        }
      } catch(e) {}
    }
    return { found, details };
  });
  console.log('  Reduced motion CSS:', JSON.stringify(reducedMotionCSS, null, 2));

  // ============================================================
  // VERIFY 5: SEO intact
  // ============================================================
  console.log('\n=== VERIFY: SEO intact ===');
  const seoCheck = await page.evaluate(() => {
    return {
      title: document.title,
      metaDescription: document.querySelector('meta[name="description"]')?.content || 'MISSING',
      canonical: document.querySelector('link[rel="canonical"]')?.href || 'MISSING',
      ogTitle: document.querySelector('meta[property="og:title"]')?.content || 'MISSING',
      ogDescription: document.querySelector('meta[property="og:description"]')?.content || 'MISSING',
      ogImage: document.querySelector('meta[property="og:image"]')?.content || 'MISSING',
      twitterCard: document.querySelector('meta[name="twitter:card"]')?.content || 'MISSING',
      jsonLdCount: document.querySelectorAll('script[type="application/ld+json"]').length,
      jsonLdTypes: Array.from(document.querySelectorAll('script[type="application/ld+json"]')).map(s => {
        try { return JSON.parse(s.textContent)['@type']; } catch(e) { return 'PARSE ERROR'; }
      })
    };
  });
  console.log('  SEO:', JSON.stringify(seoCheck, null, 2));

  // ============================================================
  // VERIFY 6: Ticket form unchanged
  // ============================================================
  console.log('\n=== VERIFY: Ticket form ===');
  const formCheck = await page.evaluate(() => {
    const form = document.querySelector('.sidebar-sticky form');
    if (!form) return { found: false };
    const action = form.getAttribute('action');
    const hasCsrf = !!form.querySelector('input[name="_token"]');
    const hasEventId = !!form.querySelector('input[name="event_id"]');
    const buyBtn = form.querySelector('.ed-buy-btn[type="submit"]');
    const qtyBtns = form.querySelectorAll('.quantity-up, .quantity-down');
    const qtyInputs = form.querySelectorAll('.quantity[aria-label]');
    return {
      found: true,
      action: action,
      hasCsrf,
      hasEventId,
      buyBtnDisabled: buyBtn?.hasAttribute('disabled') || false,
      buyBtnText: buyBtn?.textContent?.trim(),
      quantityBtns: qtyBtns.length,
      qtyInputsWithAria: qtyInputs.length
    };
  });
  console.log('  Ticket form:', JSON.stringify(formCheck, null, 2));

  await browser.close();
}

run().catch(err => {
  console.error('Error:', err.message);
  process.exit(1);
});
