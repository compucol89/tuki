# 05 · Moderation, Security & Abuse

## Moderación

- **NO EXISTE**: sin columna status/approved, sin pipeline de aprobación, sin UI admin, sin reason codes, sin audit trail.
- Consecuencia: reviews publicadas al instante; sin capacidad de excluir spam/abuso del aggregate salvo borrado manual en DB.

## Threat model (por prioridad)

| Riesgo | Estado | Evidencia |
|---|---|---|
| Rating manipulation (0/6/-5/99 vía POST directo) | 🔴 ALTO | Controller sin validate; DB sin CHECK |
| Review bombing / spam | 🔴 ALTO | Sin rate limit, sin moderación |
| Guest POST → 500 | 🟠 MEDIO | Ruta sin middleware auth; deref de null |
| Mass assignment (product_id sin existencia) | 🟠 MEDIO | `$data->create($input)` |
| Stored XSS en comment | ✅ MITIGADO | `{{ }}` + convertUtf8 |
| CSRF | ✅ OK | `@csrf` en el form |
| IDOR de user_id | ✅ MITIGADO | user_id forzado al autenticado |
| Inyección en futuro JSON-LD | ⚠️ GATE FUTURO | `json_encode(JSON_HEX_TAG|UNESCAPED_SLASHES|UNESCAPED_UNICODE)` ya usado en Product → seguro si se reutiliza |
| Privacidad (nombre completo) | 🟠 MEDIO | `fname . lname` público sin seudónimo |
| Cliente borrado rompe vista | 🟠 MEDIO | `$customer->photo` sobre null |

## Authorization matrix (real)

| Acción | Invitado | Cliente | Autor | Organizer | Admin |
|---|---|---|---|---|---|
| Crear | ❌ (500) | ✅ (cualquier producto) | ✅ | ❌ | ⚠️ NOT VERIFIED |
| Editar propia | ❌ | ✅ (upsert) | ✅ | ❌ | ⚠️ NOT VERIFIED |
| Editar ajena | ❌ | ❌ | ❌ | ❌ | ⚠️ NOT VERIFIED |
| Eliminar | ❌ | ❌ | ❌ | ❌ | ⚠️ NOT VERIFIED (solo DB) |
| Aprobar | ❌ | ❌ | ❌ | ❌ | ❌ (no existe concepto) |

## Observabilidad

- NINGUNA: sin logs de reviews, sin moderación logs, sin fraud alerts. Un eventual incidente de manipulación no sería investigable a posteriori (solo `updated_at`).
