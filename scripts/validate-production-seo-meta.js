#!/usr/bin/env node

const DEFAULT_BASE_URL = 'https://www.tukipass.com';
const DEFAULT_PROFILE_PATH = '/organizer/details/29/Rumba-Colombiana';

const baseUrl = normalizeBase(process.argv[2] || process.env.TUKIPASS_BASE_URL || DEFAULT_BASE_URL);
const profilePath = process.env.TUKIPASS_PROFILE_PATH || DEFAULT_PROFILE_PATH;
const profileUrl = new URL(profilePath, baseUrl).toString();
const timeoutMs = Number(process.env.TUKIPASS_SEO_TIMEOUT_MS || 15000);
const expectOrganizerPixel = process.env.TUKIPASS_EXPECT_ORGANIZER_PIXEL === '1';

const results = [];

function normalizeBase(value) {
  return String(value).replace(/\/+$/, '');
}

function record(level, label, detail = '') {
  results.push({ level, label, detail });
}

function pass(label, detail = '') {
  record('PASS', label, detail);
}

function warn(label, detail = '') {
  record('WARN', label, detail);
}

function fail(label, detail = '') {
  record('FAIL', label, detail);
}

async function fetchText(url, options = {}) {
  const response = await fetch(url, {
    redirect: 'follow',
    signal: AbortSignal.timeout(timeoutMs),
    headers: {
      'accept': 'text/html,application/xhtml+xml,application/xml,text/plain;q=0.9,*/*;q=0.8',
      'accept-encoding': 'gzip, deflate, br',
      'user-agent': options.userAgent || 'TukipassProductionSeoMetaValidator/1.0',
      ...(options.headers || {}),
    },
  });

  return {
    url,
    response,
    text: await response.text(),
  };
}

function absoluteUrl(path) {
  return new URL(path, baseUrl).toString();
}

function hasNoindex(html) {
  return /<meta[^>]+name=["']robots["'][^>]+content=["'][^"']*noindex/i.test(html);
}

function metaContent(html, attr, name) {
  const escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const attrPattern = attr === 'property'
    ? `property=["']${escaped}["']`
    : `name=["']${escaped}["']`;
  const match = html.match(new RegExp(`<meta[^>]+${attrPattern}[^>]+content=["']([^"']+)["'][^>]*>`, 'i'))
    || html.match(new RegExp(`<meta[^>]+content=["']([^"']+)["'][^>]+${attrPattern}[^>]*>`, 'i'));

  return match ? match[1].trim() : '';
}

function linkHref(html, rel) {
  const escaped = rel.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const match = html.match(new RegExp(`<link[^>]+rel=["']${escaped}["'][^>]+href=["']([^"']+)["'][^>]*>`, 'i'))
    || html.match(new RegExp(`<link[^>]+href=["']([^"']+)["'][^>]+rel=["']${escaped}["'][^>]*>`, 'i'));

  return match ? match[1].trim() : '';
}

function extractJsonLd(html) {
  const scripts = [...html.matchAll(/<script[^>]+type=["']application\/ld\+json["'][^>]*>([\s\S]*?)<\/script>/gi)];
  const documents = [];

  for (const script of scripts) {
    const raw = script[1].trim();
    try {
      documents.push(JSON.parse(raw));
    } catch (error) {
      fail('JSON-LD parseable', error.message);
    }
  }

  return documents;
}

function collectJsonLdTypes(value, types = new Set()) {
  if (!value || typeof value !== 'object') {
    return types;
  }

  if (Array.isArray(value)) {
    value.forEach((item) => collectJsonLdTypes(item, types));
    return types;
  }

  const type = value['@type'];
  if (Array.isArray(type)) {
    type.forEach((item) => types.add(item));
  } else if (type) {
    types.add(type);
  }

  Object.values(value).forEach((item) => collectJsonLdTypes(item, types));
  return types;
}

function checkStatus(name, response, expected = 200) {
  if (response.status === expected) {
    pass(`${name} responde ${expected}`, response.url);
    return true;
  }

  fail(`${name} responde ${expected}`, `Status ${response.status} en ${response.url}`);
  return false;
}

function checkIncludes(name, text, pattern, level = 'fail') {
  const ok = pattern instanceof RegExp ? pattern.test(text) : text.includes(pattern);
  if (ok) {
    pass(name);
  } else if (level === 'warn') {
    warn(name);
  } else {
    fail(name);
  }
}

async function validateStaticEndpoints() {
  const robots = await fetchText(absoluteUrl('/robots.txt'));
  checkStatus('robots.txt', robots.response);
  checkIncludes('robots.txt declara sitemap principal', robots.text, 'Sitemap: https://www.tukipass.com/sitemap.xml');
  checkIncludes('robots.txt declara sitemap de imágenes', robots.text, 'Sitemap: https://www.tukipass.com/sitemap-images.xml');
  checkIncludes('robots.txt permite OAI-SearchBot', robots.text, /User-agent:\s*OAI-SearchBot[\s\S]*?Allow:\s*\//i);
  checkIncludes('robots.txt bloquea GPTBot para entrenamiento', robots.text, /User-agent:\s*GPTBot[\s\S]*?Disallow:\s*\//i);

  if (/Disallow:\s*\/organizer\/details/i.test(robots.text)) {
    fail('robots.txt no bloquea perfiles públicos', 'Encontrado Disallow sobre /organizer/details');
  } else {
    pass('robots.txt no bloquea perfiles públicos');
  }

  const sitemap = await fetchText(absoluteUrl('/sitemap.xml'));
  checkStatus('sitemap.xml', sitemap.response);
  checkIncludes('sitemap.xml es XML de sitemap', sitemap.text, /<(urlset|sitemapindex)\b/i);
  checkIncludes('sitemap.xml incluye /eventos', sitemap.text, `${baseUrl}/eventos`, 'warn');
  checkIncludes('sitemap.xml incluye el perfil de prueba', sitemap.text, profileUrl, 'warn');

  const imageSitemap = await fetchText(absoluteUrl('/sitemap-images.xml'));
  checkStatus('sitemap-images.xml', imageSitemap.response);
  checkIncludes('sitemap-images.xml es XML de sitemap', imageSitemap.text, /<(urlset|sitemapindex)\b/i);

  const llms = await fetchText(absoluteUrl('/llms.txt'));
  checkStatus('llms.txt', llms.response);
  checkIncludes('llms.txt empieza con H1', llms.text, /^#\s+Tukipass/m);
  checkIncludes('llms.txt tiene bloque descriptivo', llms.text, /^>\s+/m);
  checkIncludes('llms.txt enlaza sitemap', llms.text, '/sitemap.xml');
  checkIncludes('llms.txt enlaza referencia completa', llms.text, '/llms-full.txt');

  const llmsFull = await fetchText(absoluteUrl('/llms-full.txt'));
  checkStatus('llms-full.txt', llmsFull.response);
  checkIncludes('llms-full.txt empieza con H1', llmsFull.text, /^#\s+Tukipass/m);
  checkIncludes('llms-full.txt documenta política IA', llmsFull.text, 'Politica de rastreo IA', 'warn');
}

async function validatePublicPages() {
  for (const path of ['/', '/eventos']) {
    const page = await fetchText(absoluteUrl(path));
    checkStatus(path, page.response);
    if (hasNoindex(page.text)) {
      fail(`${path} es indexable`, 'Encontrado meta robots noindex');
    } else {
      pass(`${path} es indexable`);
    }
  }
}

async function validateOrganizerProfile() {
  const page = await fetchText(profileUrl);
  checkStatus('perfil de organizador', page.response);

  const html = page.text;
  if (hasNoindex(html)) {
    fail('perfil no tiene noindex', 'Encontrado meta robots noindex');
  } else {
    pass('perfil no tiene noindex');
  }

  const canonical = linkHref(html, 'canonical');
  if (canonical) {
    pass('perfil tiene canonical', canonical);
  } else {
    fail('perfil tiene canonical');
  }

  [
    ['og:url', metaContent(html, 'property', 'og:url')],
    ['og:type', metaContent(html, 'property', 'og:type')],
    ['og:title', metaContent(html, 'property', 'og:title')],
    ['og:description', metaContent(html, 'property', 'og:description')],
    ['og:image', metaContent(html, 'property', 'og:image')],
    ['twitter:card', metaContent(html, 'name', 'twitter:card')],
  ].forEach(([tag, value]) => {
    if (value) {
      pass(`perfil publica ${tag}`, value);
    } else {
      fail(`perfil publica ${tag}`);
    }
  });

  const jsonLdDocs = extractJsonLd(html);
  const types = collectJsonLdTypes(jsonLdDocs);
  ['ProfilePage', 'Organization', 'ItemList', 'BreadcrumbList'].forEach((type) => {
    if (types.has(type)) {
      pass(`perfil JSON-LD incluye ${type}`);
    } else {
      fail(`perfil JSON-LD incluye ${type}`, `Tipos encontrados: ${[...types].join(', ') || 'ninguno'}`);
    }
  });

  const organizerPixelLevel = expectOrganizerPixel ? 'fail' : 'warn';
  checkIncludes('perfil inicializa Meta Pixel propio del organizador', html, /fbq\(['"]init['"]/i, organizerPixelLevel);
  checkIncludes('perfil trackea PageView con Pixel propio del organizador', html, /fbq\(['"]track(?:Single)?['"],\s*['"]PageView['"]/i, organizerPixelLevel);
  checkIncludes('perfil trackea ViewContent con Pixel propio del organizador', html, /fbq\(['"]track(?:Single)?['"],\s*['"]ViewContent['"]/i, organizerPixelLevel);
  checkIncludes('perfil prepara Contact con Pixel propio del organizador', html, /['"]Contact['"]/i, organizerPixelLevel);
  checkIncludes('perfil usa eventID para deduplicación del Pixel propio', html, /eventID/i, organizerPixelLevel);

  const facebookPage = await fetchText(profileUrl, {
    userAgent: 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
  });
  checkStatus('perfil con user-agent Facebook', facebookPage.response);
  checkIncludes('Facebook crawler recibe Open Graph', facebookPage.text, 'og:image');

  const encoding = facebookPage.response.headers.get('content-encoding') || '';
  if (/gzip|deflate|br/i.test(encoding)) {
    pass('servidor comprime respuesta para crawler', encoding);
  } else {
    warn('servidor comprime respuesta para crawler', 'Sin Content-Encoding gzip/deflate/br en esta respuesta');
  }
}

function printResults() {
  for (const result of results) {
    const detail = result.detail ? ` — ${result.detail}` : '';
    console.log(`[${result.level}] ${result.label}${detail}`);
  }

  const summary = results.reduce((carry, result) => {
    carry[result.level] = (carry[result.level] || 0) + 1;
    return carry;
  }, {});

  console.log('');
  console.log(`Resumen: ${summary.PASS || 0} PASS, ${summary.WARN || 0} WARN, ${summary.FAIL || 0} FAIL`);
  console.log('');
  console.log(`Rich Results Test: https://search.google.com/test/rich-results?url=${encodeURIComponent(profileUrl)}`);
  console.log(`Meta Sharing Debugger: https://developers.facebook.com/tools/debug/?q=${encodeURIComponent(profileUrl)}`);
  console.log('Meta Events Manager: https://business.facebook.com/events_manager2/list');
  console.log('Search Console: https://search.google.com/search-console');
}

async function main() {
  try {
    await validateStaticEndpoints();
    await validatePublicPages();
    await validateOrganizerProfile();
  } catch (error) {
    fail('validación ejecutada sin error inesperado', error.message);
  } finally {
    printResults();
  }

  const hasFailures = results.some((result) => result.level === 'FAIL');
  process.exit(hasFailures ? 1 : 0);
}

main();
