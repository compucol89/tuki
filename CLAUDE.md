# === TUKIPASS — AGENTE DE PRECISIÓN ===

Sos un agente de ejecución quirúrgica para una app Laravel en producción.  
Máxima precisión. Mínimo token. Cero exploración innecesaria.

---

## STACK

Laravel 12 · PHP 8.2+ · MySQL · Eloquent · Blade · Bootstrap 4 · jQuery  
CSS custom: `public/assets/front/css/style.css` (14.7k+ líneas)  
JS plugins: syotimer · slick · magnific-popup · DataTables · Lazy  
Auth: Guards custom (admin / organizer / customer) + Socialite (Google, Facebook)  
Pagos: MercadoPago · Stripe · PayPal + 16 gateways más  
Assets: Laravel Mix 6

---

## PROYECTO

**TukiPass** — SaaS de gestión de eventos y venta de entradas.  
Idioma frontend: **español (Argentina)** — TODO texto visible al cliente va en español.  
Diseño objetivo: Modern SaaS — Inter font, naranja `#F97316`, gris oscuro `#1e2532`.  
Referencia técnica completa: **`INFORME.md`** (leer si la tarea lo requiere).

---

## ESTRUCTURA CLAVE

```
app/Http/Controllers/FrontEnd/     ← Lógica pública (cliente)
app/Http/Controllers/BackEnd/      ← Admin + Organizer
app/Http/Helpers/Helper.php        ⚠️ ENORME — buscar función antes de leer
app/Models/                        ← Admin.php · Customer.php · Organizer.php (guards separados)
app/Models/Event/                  ← Event · EventContent · Ticket · Booking · Coupon
app/Models/ShopManagement/         ← Product · ProductOrder
routes/web.php                     ← Entry point, incluye sub-archivos
routes/frontend_*.php              ← auth · events · customer · payments · shop · pages
routes/admin.php                   ← Panel admin
routes/organizer*.php              ← Panel organizer
resources/views/frontend/          ← Vistas públicas
resources/views/organizer/         ← Panel organizer
resources/views/backend/           ← Panel admin (Atlantis theme)
public/assets/front/css/style.css  ← CSS principal frontend
```

---

## AUTH — TRES GUARDS (no hay User.php estándar)

| Guard | Modelo | Prefijo |
|---|---|---|
| `admin` | `App\Models\Admin` | `/admin/*` |
| `organizer` | `App\Models\Organizer` | `/organizer/*` |
| `customer` | `App\Models\Customer` | `/customer/*` |

Middlewares: `auth:admin` / `auth:organizer` / `auth:customer`  
RBAC admin: `permission:NombrePermiso` (ej: `permission:Event Management`)  
Socialite cliente: Google → `auth.google` · Facebook → `auth.facebook`

---

## CSS — CASCADA (detalle de evento)

```
style.css  <  styles.blade.php  <  @section('custom-style') del blade
```

- Feature nuevo reutilizable → agregar al **final** de `style.css`
- Override específico de evento → `@section('custom-style')` en `event-details.blade.php`
- Scoping: usar `body.page-event-detail .clase` para aislar estilos por página
- No usar Tailwind, React ni dependencias nuevas sin aprobación

---

## CHECKOUT — ZONA PROHIBIDA ⚠️

**Nunca modificar sin análisis explícito:**

```
FrontEnd\CheckOutController@checkout2      POST /check-out2
FrontEnd\Event\BookingController
FrontEnd\PaymentGateway\*Controller
```

**Campos HTML que NUNCA se tocan:**
```
name="event_id"   name="pricing_type"   name="quantity"   name="quantity[]"
name="date_type"  name="event_date"     data-price        data-stock
data-ticket_id    data-purchase         data-p_qty        #total_price
#total            @csrf                 route('check-out2')
```

**JS crítico:** `recalcTotal()` — nunca modificar.  
Botones de cantidad: `.quantity-up` · `.quantity-down` · `.quantity-down_variation`

---

## VARIABLES DISPONIBLES EN event-details.blade.php

```php
$content               // EventContent (con join a events)
$heroStatusLabel       // "En curso" | "Próximo" | etc.
$heroStatusClass       // CSS class del pill
$heroPriceLabel        // "Desde $X" | "Gratis"
$heroDateTimestamp
$ticketSummary         // array: min_price, max_price, has_price_range, total_stock
$edInterestIndicator   // int: interesados (crc32 + señales reales)
$over                  // bool: evento pasado / venta cerrada
$images                // colección galería
$organizer             // modelo Organizer ('' si es admin)
$related_events
$relatedEventsMode     // 'upcoming' | 'past' | null
$summaryOrganizer      // string nombre
$map_address
$websiteInfo           // timezone, config global
```

---

## CLASES CSS CLAVE — DETALLE DE EVENTO

| Clase | Función |
|---|---|
| `.ed-hero-event` | Section hero |
| `.ed-ev-kicker` | Chips categoría + estado |
| `.ed-ev-title` | H1 del evento |
| `.ed-ev-meta` | Fila metadatos (fecha, lugar, organizador) |
| `.ed-hero-nudge` | Rotador de frases (aria-live, opacity transition) |
| `.ed-event-quickfacts` | Strip datos rápidos |
| `.ed-ticket-card` | Card de compra sidebar |
| `.ed-ticket-card__head` | Header oscuro de la card |
| `.ed-head-pill` | Pill estado de venta |
| `.ed-buy-btn` | CTA "Reservar mi lugar" |
| `.ed-countdown-wrap` | Wrapper countdown syotimer |
| `.ed-interest-indicator` | Indicador interés compuesto |
| `.ed-body` | Sección principal bajo el hero |
| `.sidebar-sticky` | Sidebar derecha |
| `.ed-trust-row` | Items de confianza bajo CTA |

---

## REGLAS DE EJECUCIÓN

### Scope
- Trabajar SOLO en archivos mencionados explícitamente
- Nunca explorar el repo completo
- Nunca leer directorios sin instrucción

### Lectura de archivos
- Buscar primero con `rg` / `grep`
- Leer SOLO líneas necesarias
- Máx 2 archivos por tarea · Máx 300 líneas por lectura
- `Helper.php`: SIEMPRE buscar la función antes de abrir

### Edición
- Modificar SOLO lo solicitado
- No tocar código no relacionado
- No reformatear · No renombrar · No agregar abstracciones
- Preferir diff mínimo

### Output
- Código o patch mínimo
- Sin explicaciones salvo que se pidan (máx 3 líneas)
- Sin código completo del archivo salvo que se pida
- Una tarea = una respuesta

---

## PATHS PROHIBIDOS

```
vendor/   node_modules/   storage/   logs/
cache/    build/          dist/      .git/
.env      config/auth.php (sin análisis)
```

---

## ZONAS DE RIESGO — requieren confirmación explícita

```
routes/           → analizar conflictos antes de agregar
auth guards       → no modificar config/auth.php
gateways de pago  → no tocar PaymentGateway/*Controller
migraciones       → no crear/modificar en producción
seeds             → no ejecutar en producción
Admin.php (model) → no modificar sin análisis
```

---

## COMANDOS FRECUENTES

```bash
rg "nombre_funcion" app/                      # buscar antes de leer
grep -rn "clase" resources/views/             # buscar en vistas
npm run dev                                    # compilar assets
php artisan migrate                            # migraciones
```

---

## REFERENCIA

| Archivo | Contenido |
|---|---|
| `CLAUDE.md` | Este archivo — reglas del agente |
| `INFORME.md` | Mapa técnico completo del proyecto |
| `PENDIENTES.md` | Tareas activas y backlog |
| `.claude/PROJECT_MEMORY.md` | Memoria persistente de sesiones |

---

## SKILLS DISPONIBLES (leer SKILL.md antes de usarlas)

```
.claude/skills/laravel-patterns/SKILL.md
.claude/skills/laravel-specialist/SKILL.md
.claude/skills/php-pro/SKILL.md
.claude/skills/frontend-design/SKILL.md
.claude/skills/accessibility/SKILL.md
.claude/skills/seo/SKILL.md
.claude/skills/copywriting/SKILL.md
```

---

## FOCO ACTUAL

1. Rediseño frontend "Modern SaaS" — `event-details.blade.php` como página piloto
2. Traducción completa al español del frontend público
3. Social Login para Organizadores (paridad con login de clientes)

---

## OBJETIVO

Máxima precisión · Mínimo tokens · Cero exploración innecesaria

# userEmail
The user's email address is infocompucol@gmail.com.
# currentDate
Today's date is 2026-04-26.
