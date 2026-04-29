# INFORME TÉCNICO COMPLETO — TUKIPASS

> Documento de referencia para agentes de IA. Dominar este archivo = dominar el proyecto.  
> Actualizar cada vez que se agregue una ruta, modelo, controlador o convención nueva.

---

## 1. VISIÓN GENERAL

**TukiPass** es una plataforma SaaS de gestión de eventos y venta de entradas. Soporta tres tipos de usuario con paneles separados: Admin, Organizador y Cliente. Está en producción y en proceso de rediseño frontend activo.

- **Dominio de negocio principal:** Eventos, entradas, pagos, shop de productos.
- **Idioma del frontend:** Español (Argentina). TODO texto visible al cliente va en español.
- **Diseño objetivo:** "Modern SaaS" — minimalista, Inter font, naranja `#F97316`, gris oscuro `#1e2532`.

---

## 2. STACK TECNOLÓGICO

| Capa | Tecnología |
|---|---|
| Framework | Laravel 12.x |
| Lenguaje | PHP 8.2+ |
| Base de datos | MySQL |
| ORM | Eloquent |
| Templates | Blade |
| CSS frontend | Bootstrap 4 + CSS custom (`style.css`) |
| JS frontend | jQuery + plugins (syotimer, slick, magnific-popup, datatables) |
| Assets | Laravel Mix 6 |
| Auth | Guards custom (admin / organizer / customer) + Socialite (Google, Facebook) |
| Pagos | MercadoPago, Stripe, PayPal, Razorpay, Flutterwave, Mollie, Paystack, Paytm, Iyzipay, Midtrans, Xendit, MyFatoorah, Instamojo, Yoco, Phonepe, PerfectMoney, Toyyibpay, Paytabs, Offline |
| QR | simplesoftwareio/simple-qrcode |
| PDF / Excel | barryvdh/laravel-dompdf + maatwebsite/excel |
| Push notifications | laravel-notification-channels/webpush |
| Socialite | laravel/socialite ^5.5 |
| Fonts (npm) | @fontsource/inter, plus-jakarta-sans, sora |

---

## 3. ESTRUCTURA DE DIRECTORIOS CLAVE

```
tuki/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── FrontEnd/          ← Lógica pública (cliente)
│   │   │   │   ├── Event/         ← BookingController, CustomerBookingController
│   │   │   │   ├── PaymentGateway/  ← 19 gateways
│   │   │   │   └── Shop/          ← ShopController, OrderController, CustomerOrderController
│   │   │   └── BackEnd/           ← Admin + Organizer
│   │   │       ├── Event/         ← EventController, TicketController, EventBookingController
│   │   │       ├── Organizer/     ← OrganizerController, EventController, TicketController...
│   │   │       ├── BasicSettings/ ← Config global del sitio
│   │   │       ├── Administrator/ ← RolePermission, SiteAdmin
│   │   │       └── PaymentGateway/ ← Config de gateways desde admin
│   │   ├── Helpers/
│   │   │   └── Helper.php         ⚠️ ARCHIVO ENORME — siempre buscar función antes de leer
│   │   └── Middleware/
│   │       ├── ChangeLanguage.php
│   │       ├── Deactive.php        ← Bloquea cuentas inactivas
│   │       ├── EmailStatus.php     ← Verifica email confirmado
│   │       ├── HasPermission.php   ← RBAC del admin
│   │       └── SetLangMiddleware.php
│   └── Models/
│       ├── Admin.php
│       ├── Customer.php
│       ├── Organizer.php
│       ├── OrganizerInfo.php
│       ├── Transaction.php
│       ├── Withdraw.php
│       ├── Event/
│       │   ├── Event.php
│       │   ├── EventContent.php    ← Contenido multiidioma del evento
│       │   ├── EventCategory.php
│       │   ├── EventDates.php
│       │   ├── EventImage.php
│       │   ├── Ticket.php
│       │   ├── TicketContent.php
│       │   ├── VariationContent.php
│       │   ├── Booking.php
│       │   ├── Coupon.php
│       │   └── Wishlist.php
│       ├── ShopManagement/
│       │   ├── Product.php
│       │   ├── ProductContent.php
│       │   ├── ProductCategory.php
│       │   ├── ProductImage.php
│       │   ├── ProductOrder.php
│       │   ├── OrderItem.php
│       │   ├── ProductReview.php
│       │   ├── ShippingCharge.php
│       │   └── ShopCoupon.php
│       └── PaymentGateway/
│           ├── OnlineGateway.php
│           └── OfflineGateway.php
├── routes/
│   ├── web.php                    ← Entry point, incluye los sub-archivos
│   ├── frontend_auth.php
│   ├── frontend_events.php
│   ├── frontend_customer.php
│   ├── frontend_payments.php
│   ├── frontend_shop.php
│   ├── frontend_pages.php
│   ├── frontend_fallback.php
│   ├── admin.php                  ← Todas las rutas del panel admin
│   ├── organizer.php              ← Entry point organizer
│   ├── organizer_auth.php
│   ├── organizer_dashboard.php
│   ├── organizer_events.php
│   ├── organizer_finance.php
│   └── organizer_support.php
├── resources/
│   └── views/
│       ├── frontend/              ← Vistas públicas (cliente)
│       │   ├── layout.blade.php   ← Layout base frontend
│       │   ├── partials/          ← Header, footer, scripts, styles
│       │   ├── home/              ← index-v1.blade.php
│       │   ├── event/             ← event.blade.php, event-details.blade.php, invoice.blade.php
│       │   ├── customer/          ← Dashboard, mis entradas, wishlist, soporte
│       │   ├── payment/           ← Páginas de resultado de pago
│       │   └── shop/              ← Tienda, carrito, checkout
│       ├── organizer/             ← Panel organizer
│       │   ├── layout.blade.php
│       │   └── event/             ← CRUD de eventos del organizer
│       └── backend/               ← Panel admin (Atlantis theme)
│           ├── layout.blade.php
│           └── event/, basic-settings/, administrator/, ...
└── public/
    └── assets/
        ├── front/css/style.css    ← CSS principal del frontend (14.7k+ líneas)
        └── admin/css/
            ├── admin-skin.css     ← Overrides del rediseño admin
            └── atlantis.css       ← Theme base admin
```

---

## 4. SISTEMA DE AUTH — TRES GUARDS

| Guard | Modelo | Prefijo URL | Panel |
|---|---|---|---|
| `admin` | `App\Models\Admin` | `/admin/*` | Backend completo, RBAC por roles/permisos |
| `organizer` | `App\Models\Organizer` | `/organizer/*` | Panel de organizador |
| `customer` | `App\Models\Customer` | `/customer/*` | Dashboard público del cliente |

**Middlewares de auth:**
- `auth:admin` / `auth:organizer` / `auth:customer` — guard correspondiente
- `guest:admin` / `guest:organizer` / `guest:customer` — para rutas de login
- `Deactive:organizer` / `Deactive:customer` — bloquea cuentas desactivadas
- `EmailStatus:organizer` / `EmailStatus:customer` — requiere email verificado
- `permission:NombrePermiso` — RBAC granular para admin (ej: `permission:Event Management`)
- `adminLang` — setea idioma del panel
- `change.lang` — setea idioma del frontend

**Socialite (cliente):**
- Google: `GET /customer/auth/google` → `FrontEnd\CustomerController@googleRedirect`
- Facebook: `GET /customer/auth/facebook` → `FrontEnd\CustomerController@facebookRedirect`

---

## 5. MAPA DE RUTAS COMPLETO

### 5.1 Frontend Público

#### Auth cliente (`frontend_auth.php`)
```
GET  /customer/login                  customer.login
GET  /customer/signup                 customer.signup
POST /customer/create                 customer.create
POST /customer/store                  customer.authentication
GET  /customer/auth/google            auth.google
GET  /customer/auth/facebook          auth.facebook
GET  /login/google/callback           → CustomerController@handleGoogleCallback
GET  /login/facebook/callback         → CustomerController@handleFacebookCallback
GET  /customer/forget-password        customer.forget.password
POST /customer/send-forget-mail       customer.forget.mail
GET  /customer/reset-password         customer.reset.password
POST /customer/update-forget-password customer.update-forget-password
GET  /customer/logout                 customer.logout
GET  /customer/signup-verify/{token}  → CustomerController@signupVerify
GET  /admin/                          admin.login
POST /admin/auth                      admin.auth
```

#### Eventos (`frontend_events.php`)
```
GET  /                                index
GET  /eventos                         events
GET  /event/{slug}/{id}               event.details
GET  /addto/wishlist/{id}             addto.wishlist
GET  /remove/wishlist/{id}            remove.wishlist
GET  /organizer/details/{id}/{name}   frontend.organizer.details
GET  /organizers/                     frontend.all.organizer
POST /organizers/contact/send-mail    organizer.contact.send_mail
POST /ticket-booking/{id}             ticket.booking
GET  /event-booking/{id}/cancel       event_booking.cancel
GET  /event-booking-complete          event_booking.complete
GET  /booking/view/{id}               booking.guest_view
```

#### Cliente autenticado (`frontend_customer.php`)
```
GET  /customer/dashboard              customer.dashboard
GET  /customer/edit-profile           customer.edit.profile
POST /customer/update-profile         customer.profile.update
GET  /customer/lista-de-deseos        customer.wishlist
GET  /customer/mis-entradas           customer.booking.my_booking
GET  /customer/booking/details/{id}   customer.booking_details
GET  /customer/support-ticket         customer.support_tickert
GET  /customer/support-ticket/create  customer.support_tickert.create
POST /customer/support-ticket/store   customer.support_ticket.store
GET  /customer/support-ticket/message/{id}
POST /customer/support-ticket/reply/{id}
GET  /customer/my-orders              customer.my_orders
GET  /customer/my-orders/details/{id} customer.order_details
```

#### Checkout y pagos (`frontend_shop.php` + `frontend_payments.php`)
```
POST /check-out2                      check-out2   ← FORMULARIO PRINCIPAL DE COMPRA
GET  /checkout                        check-out
POST /event-booking/apply-coupon      apply-coupon
GET  /event-booking/mercadopago/notify
GET  /shop/                           shop
GET  /shop/details/{slug}/{id}        shop.details
GET  /shop/add-to-cart/{id}           add.cart
POST /shop/order-now                  order-now
GET  /shop/cart/                      shopping.cart
POST /shop/buy/                       shop.buy
GET  /product-order/{id}/cancel       product_order.cancel
GET  /product-order-complete/complete/{via?}
```

#### Páginas (`frontend_pages.php` + `frontend_fallback.php`)
```
GET  /blog                            blogs
GET  /blog/{slug}                     blog_details
GET  /faq                             faqs
GET  /contacto                        contact
POST /contact/send-mail               contact.send_mail
GET  /sobre-nosotros                  about
GET  /sitemap.xml                     sitemap
GET  /{slug}                          dynamic_page  ← CMS dinámico (último, excepto sobre-nosotros)
FALLBACK → errors.404
```

### 5.2 Panel Organizer (`/organizer/*`)

```
GET  /organizer/login                 organizer.login
GET  /organizer/signup                organizer.signup
POST /organizer/create                organizer.create
POST /organizer/store                 organizer.authentication
GET  /organizer/dashboard             organizer.dashboard
GET  /organizer/edit-profile
POST /organizer/update-profile
GET  /organizer/event-management/events/
GET  /organizer/add-event/
POST /organizer/event-store
GET  /organizer/edit-event/{id}
POST /organizer/event-update
GET  /organizer/edit-ticket-setting/{id}
GET  /organizer/event/ticket
GET  /organizer/event/add-ticket
POST /organizer/event/ticket/store-ticket
GET  /organizer/event-booking
GET  /organizer/event-booking/details/{id}
GET  /organizer/event-booking/report
GET  /organizer/event-booking/export
GET  /organizer/withdraw
POST /organizer/withdraw/send-request
POST /organizer/check-qrcode/         check-qrcode (escáner QR)
```

### 5.3 Panel Admin (`/admin/*`)

Grupos principales con sus middlewares de permiso:

| Grupo | Permiso | Descripción |
|---|---|---|
| `/admin/dashboard` | — | Dashboard principal |
| `/admin/event-management/*` | `Event Management` | CRUD eventos, tickets, categorías, bookings |
| `/admin/admin-management/*` | `Admin Management` | Roles, permisos, admins |
| `/admin/transcation` | `Transaction` | Transacciones globales |
| `/admin/monthly-profit` | `Total Profit` | Reporte ganancias |
| `/admin/monthly-earning` | `Lifetime Earning` | Reporte ingresos |
| `/admin/basic-settings/*` | `Basic Settings` | Config del sitio |
| `/admin/payment-gateways/*` | `Payment Gateways` | Configurar gateways |
| `/admin/organizer-management/*` | `Organizer Management` | CRUD organizadores |
| `/admin/customer-management/*` | `Customer Management` | CRUD clientes |
| `/admin/shop-management/*` | `Shop Management` | Productos, órdenes |
| `/admin/support-ticket/*` | `Support Ticket` | Soporte |
| `/admin/withdraw/*` | `Withdraw` | Retiros organizadores |
| `/admin/pwa` | `PWA Settings` | Config PWA |

---

## 6. CONTROLADORES FRONTEND — RESPONSABILIDADES

| Controlador | Responsabilidad principal |
|---|---|
| `FrontEnd\HomeController` | Homepage, about, páginas de error, callbacks de pagos especiales |
| `FrontEnd\EventController` | Listado de eventos, detalle, wishlist, coupon, `buildInterestIndicator()` |
| `FrontEnd\CheckOutController` | Proceso de checkout (`check-out2`) — CRÍTICO, no tocar sin análisis |
| `FrontEnd\CustomerController` | Auth cliente, perfil, wishlist, Socialite |
| `FrontEnd\OrganizerController` | Perfil público de organizador, contacto |
| `FrontEnd\Event\BookingController` | Iniciar booking, cancelar, completar |
| `FrontEnd\Event\CustomerBookingController` | Mis entradas, detalles booking, vista guest |
| `FrontEnd\BlogController` | Blog público |
| `FrontEnd\ContactController` | Formulario de contacto |
| `FrontEnd\PageController` | CMS dinámico (`/{slug}`) |
| `FrontEnd\SitemapController` | Genera sitemap.xml |
| `FrontEnd\Shop\ShopController` | Tienda, carrito, checkout shop |
| `FrontEnd\Shop\OrderController` | Crear orden, completar, cancelar |
| `FrontEnd\PaymentGateway\*` | Un controller por gateway de pago |

---

## 7. MODELOS CLAVE Y RELACIONES

### Evento (`Event\Event.php`)
Campos principales: `id`, `organizer_id`, `status`, `featured`, `pricing_type` (`free`/`paid`/`variation`), `date_type` (`single`/`multiple`), `start_date`, `end_date`, `start_time`, `end_time`, `event_type` (`venue`/`online`), `countdown_status`, `thumbnail`, `views_count`.

### EventContent (`Event\EventContent.php`)
Contenido localizado: `event_id`, `language_id`, `title`, `description`, `slug`, `city`, `country`, `address`. **Join obligatorio con Event para queries front.**

### Ticket / TicketContent
- `Ticket`: `event_id`, `pricing_type`, `price`, `early_bird_discount`, `max_ticket_buy_type`, `max_buy_ticket`, `stock_type`, `quantity`.
- `TicketContent`: contenido localizado del ticket.
- Variaciones: `VariationContent` — tickets con múltiples precios/nombres.

### Booking (`Event\Booking.php`)
`event_id`, `customer_id`, `organizer_id`, `ticket_id`, `quantity`, `paymentStatus` (`paid`/`pending`/etc.), `payment_method`, `total_price`, `access_token` (QR).

### Customer / Organizer / Admin
Guards separados, modelos independientes. No son `User.php` estándar de Laravel (aunque existe un `User.php` en Models, no es el modelo principal de auth).

---

## 8. ARCHIVOS CSS Y JS — FRONTEND

### CSS (en orden de cascada)
```
public/assets/front/css/style.css          ← CSS principal, 14.7k+ líneas
```
Dentro del blade de detalle de evento:
```
resources/views/frontend/partials/styles.blade.php  ← Overrides globales en <style>
@section('custom-style') en event-details.blade.php ← Overrides específicos del detalle
```

### Regla de cascada
`style.css` < `styles.blade.php` < `@section('custom-style')` del blade específico.  
Para overrides de detalle de evento: usar `@section('custom-style')`.  
Para features nuevos reutilizables: agregar al final de `style.css`.

### JS (en orden de carga)
```
jQuery (global)
Bootstrap 4
syotimer (countdown: .event-countdown con data-end_date / data-end_time)
slick (sliders)
magnific-popup (galería)
DataTables
Lazy loading (.lazy)
@push('scripts') del blade específico
```

### Clases CSS críticas — Detalle de evento
| Clase | Función |
|---|---|
| `.ed-hero-event` | Section hero del evento |
| `.ed-ev-kicker` | Chips de categoría + estado sobre el título |
| `.ed-ev-title` | H1 del evento |
| `.ed-ev-meta` | Fila de metadatos (fecha, lugar, organizador) |
| `.ed-hero-nudge` | Rotador de frases en el hero |
| `.ed-event-quickfacts` | Strip de datos rápidos (horario, precio, acceso, organiza) |
| `.ed-ticket-card` | Card de compra en la sidebar |
| `.ed-ticket-card__head` | Header oscuro de la card |
| `.ed-head-pill` | Pill de estado de venta |
| `.ed-buy-btn` | Botón CTA "Reservar mi lugar" |
| `.ed-countdown-wrap` | Wrapper del countdown |
| `.ed-interest-indicator` | Indicador de interés compuesto |
| `.ed-body` | Sección principal bajo el hero |
| `.sidebar-sticky` | Sidebar derecha (sticky) |
| `.ed-trust-row` | Items de confianza bajo el CTA |

---

## 9. LÓGICA DE CHECKOUT — ZONAS PROHIBIDAS

El flujo de compra toca múltiples archivos. **Nunca modificar sin análisis explícito:**

```
FrontEnd\CheckOutController@checkout2      ← POST /check-out2
FrontEnd\Event\BookingController           ← Booking creation
FrontEnd\PaymentGateway\*Controller        ← 19 gateways
```

**Campos del form que NUNCA se tocan:**
```
name="event_id"        name="pricing_type"    name="quantity"
name="quantity[]"      name="date_type"       name="event_date"
data-price             data-stock             data-ticket_id
data-purchase          data-p_qty             #total_price
#total                 @csrf                  route('check-out2')
```

**Función JS crítica:** `recalcTotal()` — recalcula el total visible cuando cambia cantidad.  
**Botones de cantidad:** `.quantity-up`, `.quantity-down`, `.quantity-down_variation`

---

## 10. FEATURES IMPLEMENTADOS EN DETALLE DE EVENTO

### Indicador de interés (`buildInterestIndicator`)
**Ubicación:** `EventController.php` línea ~514.  
**Lógica:** Usa `crc32()` con salt del evento para base determinística + crecimiento diario + señales reales (views, wishlist, bookings). Resultado: `$edInterestIndicator` (int) en la vista.

### Hero Nudge Rotator
**Blade:** `.ed-hero-nudge` con 5 frases, `aria-live="polite"`, `aria-atomic="true"`.  
**JS:** Rota cada 5s usando `opacity` transition. Respeta `prefers-reduced-motion`.

### Countdown
**Blade:** `.ed-countdown-wrap` > `.event-countdown` con `data-end_date` y `data-end_time`.  
**Plugin:** syotimer — solo aparece si `$content->countdown_status == 1` y `!$over`.

### Variables del controlador disponibles en la vista
```php
$content          // EventContent — datos del evento (con join a events)
$heroStatusLabel  // string: "En curso", "Próximo", etc.
$heroStatusClass  // CSS class del pill
$heroPriceLabel   // string: "Desde $X" | "Gratis"
$heroDateTimestamp
$ticketSummary    // array: min_price, max_price, has_price_range, total_stock, has_unlimited_stock
$edInterestIndicator // int: número de interesados
$over             // bool: evento pasado/venta cerrada
$images           // colección de imágenes de galería
$organizer        // modelo Organizer (o '' si es admin)
$related_events   // eventos relacionados (mismo organizador)
$relatedEventsMode // 'upcoming' | 'past' | null
$summaryOrganizer // string: nombre del organizador
$map_address      // string: dirección para mapa
$websiteInfo      // config global (timezone, etc.)
```

---

## 11. CONVENCIONES Y REGLAS DEL PROYECTO

### Código
- Lógica en Controllers o Helpers. Models livianos.
- `Helper.php` es enorme — siempre `rg`/`grep` antes de leer.
- Max 300 líneas por lectura. Buscar antes de leer.
- Un solo cambio por PR. Patches quirúrgicos.
- No explorar el repo completo sin instrucción explícita.

### Frontend
- Todo texto visible al cliente: **español**.
- No usar Tailwind, React ni dependencias nuevas sin aprobación.
- No tocar jQuery global ni los plugins existentes.
- CSS nuevo: agregar al **final** de `style.css` o en `@section('custom-style')`.
- Selectores CSS: usar `body.page-event-detail .clase` para scoping por página.

### Zonas de riesgo — requieren confirmación explícita
```
routes/          → no agregar rutas sin analizar conflictos
auth guards      → no modificar config/auth.php
gateways de pago → no tocar ningún PaymentGateway/*Controller
migraciones      → no crear/modificar en producción
.env             → nunca leer ni modificar
seeds            → no ejecutar en producción
Admin.php (model) → no modificar sin análisis
```

---

## 12. COMANDOS DE DESARROLLO

```bash
composer install
npm install
npm run dev          # compilar assets (dev)
npm run watch        # watch mode
npm run prod         # producción
php artisan migrate  # migraciones
php artisan serve    # servidor local
php artisan test     # tests

# Buscar antes de leer (SIEMPRE)
rg "nombre_funcion" app/
grep -rn "clase_o_variable" resources/views/
```

---

## 13. ARCHIVOS DE REFERENCIA DEL AGENTE

| Archivo | Contenido |
|---|---|
| `CLAUDE.md` | Reglas de ejecución del agente IA |
| `INFORME.md` | Este archivo — mapa técnico completo |
| `PENDIENTES.md` | Tareas activas y backlog |
| `.claude/PROJECT_MEMORY.md` | Memoria persistente de sesiones |

---

## 14. FOCO ACTUAL DEL PROYECTO

1. **Rediseño frontend "Modern SaaS"** — página de detalle de evento (`event-details.blade.php`) como página piloto.
2. **Traducción completa al español** del frontend público.
3. **Social Login para Organizadores** — paridad con el login de clientes (Google/Facebook).

---

*Generado automáticamente el 2026-04-26. Actualizar con cada cambio estructural significativo.*
