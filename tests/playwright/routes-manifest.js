// @ts-check
/**
 * @manifest — Fuente única de verdad de las rutas públicas auditadas.
 * Compartido por @e2e, @a11y y @aria (docs/reference/playwright).
 * Si una ruta del contrato cambia, se actualiza SOLO acá.
 */
const PUBLIC_PAGES = [
  { name: 'home', path: '/' },
  { name: 'eventos', path: '/eventos' },
  { name: 'blog', path: '/blog' },
  { name: 'contacto', path: '/contacto' },
  { name: 'sobre-nosotros', path: '/sobre-nosotros' },
  { name: 'organizadores', path: '/organizadores' },
  { name: 'faqs', path: '/preguntas-frecuentes' },
  { name: 'login', path: '/login' },
  { name: 'registro', path: '/registro' },
  { name: 'recuperar-contrasena', path: '/recuperar-contrasena' },
  { name: 'restablecer-contrasena', path: '/usuario/reset-password' },
  { name: 'organizer-login', path: '/organizer/login' },
  { name: 'organizer-signup', path: '/organizer/signup' },
  { name: 'organizer-forget-password', path: '/organizer/forget-password' },
  { name: 'organizer-reset-password', path: '/organizer/reset-password' },
];

module.exports = { PUBLIC_PAGES };
