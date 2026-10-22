# Hardening de MCP — TukiPass

## Inventario actual

| MCP | Tipo | Riesgo | Acción |
|---|---|---|---|
| `chrome-devtools` | local (`npx`) | Bajo | Mantener; solo lectura de navegador |
| `playwright` | local (`npx`) | Bajo | Mantener; pin de versión |
| `easypanel` | remoto HTTP | Alto | HTTPS/VPN + token read-only + allowlist |
| `mcp-tayrona-easypanel` | remoto HTTP | Alto | Unificar con `easypanel` o eliminar |
| `mercadopago` | remoto | Alto | Solo docs/sandbox; prohibido crear pagos reales |
| `n8n` | remoto | Alto | Perfil separado; sin credenciales productivas |
| `ui-skills` | remoto | Bajo | Mantener; solo lectura |

## Reglas

1. **Lectura por defecto.** Ningún agente debe tener escritura sobre infraestructura o pagos sin aprobación explícita.
2. **Sin secretos en prompts.** No pegar tokens, `APP_KEY`, certificados ARCA ni credenciales de DB.
3. **HTTPS o VPN.** Los endpoints de EasyPanel no deben viajar por HTTP público.
4. **Tokens separados.** Un token read-only para diagnóstico; otro, con aprobación, para operaciones.
5. **Perfiles separados.** No usar `easypanel` y `n8n` en la misma sesión que un deploy.
6. **MercadoPago sandbox.** Nunca ejecutar cobros reales desde un agente.
7. **Registro de acciones.** Toda mutación de infraestructura debe quedar en un runbook con hora, autor y rollback.

## Cambios pendientes (fuera del repo)

Editar `~/.config/opencode/opencode.json`:

- Cambiar `http://148.230.72.12:3000/api/mcp` y `http://85.31.63.115:3000/api/mcp` por HTTPS o túnel VPN.
- Eliminar el duplicado `mcp-tayrona-easypanel`.
- Configurar el MCP de MercadoPago con credenciales de sandbox.
- Revisar si `n8n` necesita estar habilitado en sesiones de producción.

No se modificó la configuración global en esta auditoría para no romper flujos existentes sin las credenciales/endpoints correctos.
