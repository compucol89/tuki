# Recomendaciones DNS / Email — TukiPass

> **Fecha:** 5 de junio de 2026
> **Origen:** Informe de auditoría Sabuezo + análisis READ-ONLY del Auditor.
> **Aplicar en:** Panel DNS de Hostinger para `tukipass.com` y panel de Postmark.
> **NO requiere cambios en código Laravel.**

---

## 1. Estado actual (verificado)

| Componente | Estado | Detalle |
|------------|--------|---------|
| DKIM Postmark | ✅ Configurado y verificado | Selector `20260425121452pm._domainkey` (RSA) |
| Return-Path Postmark | ✅ Verificado | `pm-bounces` CNAME → `pm.mtasv.net` |
| SPF | ⚠️ No incluye Postmark | Solo `include:_spf.mail.hostinger.com ~all` |
| DMARC | ❌ DOS registros duplicados | Ambos `p=none`, uno con `rua=mailto:infocompucol@gmail.com` |

---

## 2. Cambios requeridos

### 2.1. SPF (panel DNS Hostinger)

**Editar** el registro TXT `@` (SPF) existente:

| Campo | Valor actual | Valor nuevo |
|-------|--------------|-------------|
| Type  | `TXT` | `TXT` (sin cambio) |
| Name  | `@` | `@` (sin cambio) |
| Value | `v=spf1 include:_spf.mail.hostinger.com ~all` | `v=spf1 include:_spf.mail.hostinger.com include:spf.mtasv.net -all` |
| TTL   | (actual) | 3600 |

**Cambios concretos:**
- Agregar `include:spf.mtasv.net` (Postmark — registro oficial SPF).
- Cambiar `~all` (softfail) por `-all` (fail, alineamiento estricto).
- Conservar `include:_spf.mail.hostinger.com` por si se usa Hostinger mail en algún flujo.

### 2.2. DMARC (panel DNS Hostinger)

**Paso 1:** Eliminar los DOS registros TXT `_dmarc` actuales (están duplicados y violan RFC 7489).

**Paso 2:** Crear un único registro TXT `_dmarc`:

| Campo | Valor |
|-------|-------|
| Type  | `TXT` |
| Name  | `_dmarc` |
| Value | `v=DMARC1; p=quarantine; rua=mailto:dmarc@tukipass.com; ruf=mailto:dmarc@tukipass.com; pct=10; adkim=s; aspf=s; fo=1` |
| TTL   | 3600 |

**Pre-requisito:** crear buzón (o alias) `dmarc@tukipass.com` en Hostinger mail para recibir los reportes.

**Significado de los flags:**
- `p=quarantine` — política inicial conservadora (no rechaza, sugiere cuarentena en receptor).
- `rua` y `ruf` — destinatarios de reportes agregados y forenses.
- `pct=10` — solo el 10% de emails se evalúan al inicio (subir a 100% en 2-4 semanas).
- `adkim=s` y `aspf=s` — alineamiento estricto DKIM y SPF.
- `fo=1` — generar reporte forense ante cualquier fallo.

---

## 3. Cadencia recomendada

| Semana | Acción |
|--------|--------|
| 0 (ahora) | Aplicar SPF nuevo + DMARC `p=quarantine pct=10`. Crear buzón `dmarc@tukipass.com`. |
| 2-4 | Monitorear Postmark bounce log y reportes DMARC en `dmarc@tukipass.com`. Si no hay falsos positivos, subir DMARC a `pct=100`. |
| 4-6 | Si todo OK, migrar a `p=reject` (protección total anti-spoofing). |

---

## 4. Validación post-cambio (comandos DNS)

```bash
# SPF — debe mostrar la nueva cadena con spf.mtasv.net y -all
dig TXT tukipass.com

# DMARC — debe mostrar UN SOLO registro con la política nueva
dig TXT _dmarc.tukipass.com

# DKIM — debe seguir mostrando la clave RSA Postmark
dig TXT 20260425121452pm._domainkey.tukipass.com
```

**Salida esperada (resumida):**

- `dig TXT tukipass.com` → `"v=spf1 include:_spf.mail.hostinger.com include:spf.mtasv.net -all"`
- `dig TXT _dmarc.tukipass.com` → `"v=DMARC1; p=quarantine; rua=mailto:dmarc@tukipass.com; ruf=mailto:dmarc@tukipass.com; pct=10; adkim=s; aspf=s; fo=1"`
- `dig TXT 20260425121452pm._domainkey.tukipass.com` → clave RSA completa (sin cambios)

---

## 5. Rollback

Si los cambios SPF/DMARC rompen la deliverability de emails legítimos:

1. Restaurar SPF a `v=spf1 include:_spf.mail.hostinger.com ~all`.
2. Restaurar DMARC a `v=DMARC1; p=none` (un solo registro).
3. Investigar bounces en Postmark (https://postmarkapp.com → servidor transactional → Bounce Log).
4. Re-aplicar gradualmente: SPF con `~all` primero, DMARC con `pct=10` durante 1-2 semanas, monitorear, y luego endurecer.

---

## 6. Notas técnicas

- **No se requiere reinicio del servidor Laravel** — los registros DNS se consultan en cada envío de email.
- **TTL actual 3600** — la propagación DNS puede tardar hasta 1 hora en algunos resolvers. Esperar antes de validar.
- **Panel Postmark** — una vez aplicado el nuevo SPF, refrescar el panel Postmark; el estado "Inactive · Monitor email authentication" debería pasar a "Active".
- **CDN / cloud email security** — algunos filtros (Mimecast, Proofpoint) pueden interpretar `-all` diferente de `~all`. Si llega a un problema, documentar el bypass por IP (`ip4:80.72.32.0/24 ip4:85.154.138.0/24`) en lugar de `-all`.
- **Subdominio dedicado para From** (opcional, no bloqueante): considerar `noreply@tukipass.com` o `tickets@tukipass.com` para aislar reputación transaccional de emails administrativos. Esto requiere firmar DKIM para el subdominio.

---

## 7. Riesgos si NO se aplican estos cambios

1. **Phishing / spoofing** — cualquiera puede enviar emails en nombre de `@tukipass.com` y los receptores no los rechazan (DMARC `p=none` solo reporta, no previene).
2. **Deliverability degradada** — sin SPF incluyendo Postmark, los servidores receptores con DMARC estricto pueden marcar los emails como softfail y reducir puntaje.
3. **Panel Postmark inactivo** — la sección "Email Authentication" muestra "Inactive" hasta que DMARC esté correctamente configurado.
4. **Reportes a Gmail personal** — el `rua=mailto:infocompucol@gmail.com` filtra metadata de clientes a un buzón personal (riesgo de privacidad y dependencia individual).
