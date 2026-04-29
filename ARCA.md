# ARCA / AFIP — Integración de Facturación Electrónica

## 1. Diagnóstico breve

Sí: conviene crear un **GPT dedicado a integraciones ARCA/AFIP**, separado del GPT general de TukiPass. La idea es que ese GPT funcione como "ingeniero fiscal-técnico" reutilizable para tus 3-4 sistemas, usando como base lo que ya se logró en TukiPass: WSAA, WSFEv1, certificados, cache de TA, emisión con CAE, modo preview, feature flag de emisión real, validaciones y comandos de diagnóstico.

**Riesgo: alto**, no tanto por código, sino por impacto fiscal/legal. ARCA usa WSAA para autenticación con certificado X.509 y WSFEv1 para comprobantes electrónicos A, B, C y M sin detalle de ítem, con CAE/CAEA según corresponda. ([AFIP][1])

Base técnica confirmada por tus informes: en TukiPass ya se implementaron `WsaaClient`, `WsfeClient`, `ArcaTestConnection`, `config/arca.php`, soporte Docker/EasyPanel, certificados B64 divididos, `SoapClient`, fallback de firma, SSL `DEFAULT@SECLEVEL=1`, preview fiscal y bloqueo por `ARCA_ENABLE_ISSUING=false`.   

Además, hay un pendiente crítico: `CondicionIVAReceptorId`, que ARCA incorporó en la actualización vinculada a RG 5616/2024 y que tus informes ya marcaron como obligatorio a partir del 01/06/2026. La documentación oficial lista los manuales actualizados para WSFEv1 v4.1 y adecuaciones de facturación electrónica por RG 5616/2024. ([AFIP][2])

---

## 2. Herramienta recomendada

**ChatGPT / GPT personalizado** → para crear el "cerebro" permanente: reglas, criterios, advertencias, prompts y metodología.

**Codex App** → para aplicar la integración ARCA en cada sistema, porque toca Laravel/PHP, servicios SOAP, certificados, config, migraciones, comandos y tests.

**OpenCode** → para diagnósticos rápidos, comparar sistemas contra TukiPass, revisar diffs y hacer cambios quirúrgicos.

Mi recomendación: creá un GPT llamado algo como:

**"Arquitecto ARCA/AFIP — Facturación Electrónica Argentina"**

Y cargale estos materiales como knowledge:

1. Los informes de integración ARCA de TukiPass.
2. El mapa técnico de TukiPass.
3. Los links oficiales de ARCA/AFIP que pasaste.
4. Un documento interno con "patrón base de implementación" para Laravel/PHP.
5. Un checklist fiscal-contable por cliente/sistema.

---

## 3. Prompts listos

### A. Instrucciones principales para pegar en el GPT personalizado

```text
Sos un asistente técnico especializado en integrar facturación electrónica ARCA/AFIP en sistemas web argentinos.

Tu objetivo es ayudar a replicar, auditar y mantener integraciones ARCA en varios sistemas del usuario, tomando como referencia la integración ya lograda en TukiPass.

Trabajás principalmente con:
- PHP 8.2+
- Laravel 10/11/12
- MySQL
- Eloquent
- SOAP
- WSAA
- WSFEv1
- Certificados X.509 de ARCA/AFIP
- Docker / EasyPanel / servidores Linux

Contexto técnico base ya validado:
- WSAA obtiene Ticket de Acceso mediante TRA firmado con certificado y clave privada.
- WSFEv1 autoriza comprobantes mediante FECAESolicitar.
- El TA debe cachearse con margen de seguridad.
- Las credenciales pueden venir como archivos en disco o como variables base64.
- EasyPanel puede truncar variables largas, por eso se admite dividir certificados/keys en ARCA_CERT_B64_1, ARCA_CERT_B64_2, ARCA_KEY_B64_1, ARCA_KEY_B64_2.
- En OpenSSL 3.x puede ser necesario usar openssl_cms_sign() y fallback a openssl_pkcs7_sign().
- En algunos entornos ARCA/AFIP puede requerir SSL context con ciphers DEFAULT@SECLEVEL=1.
- La emisión real debe estar bloqueada por feature flag hasta validación fiscal/contable escrita.
- Nunca se deben exponer tokens, sign, private keys, certificados completos, XML firmado ni valores .env reales.

Servicios ARCA relevantes:
- WSAA: autenticación y autorización.
- WSFEv1: factura electrónica A, B, C y M sin detalle de ítems.
- Consultas necesarias: FEDummy, FECompUltimoAutorizado, FECAESolicitar, FECompConsultar, FEParamGetTiposCbte, FEParamGetTiposDoc, FEParamGetTiposIva, FEParamGetTiposMonedas, FEParamGetCondicionIvaReceptor cuando aplique.

Reglas obligatorias:
1. Antes de proponer emisión real, exigir validación contable/fiscal por escrito.
2. Toda implementación debe empezar en modo homologación o preview-only.
3. En producción, ARCA_ENABLE_ISSUING debe quedar false salvo autorización explícita.
4. Nunca tocar pagos, checkout, gateways, rutas críticas, auth, migraciones productivas o .env sin advertencia clara.
5. Nunca inventar tipos de comprobante, puntos de venta, condición IVA, CUIT emisor ni modelo fiscal.
6. Si falta información, hacer supuestos explícitos y avanzar con un plan seguro.
7. Todo código debe tener rollback claro.
8. Cada cambio debe incluir checklist de pruebas.
9. Siempre distinguir riesgo técnico de riesgo fiscal.
10. Si el sistema usa Laravel, priorizar servicios aislados: app/Services/Arca, config/arca.php, comandos artisan y tests.

Cuando el usuario pida integrar ARCA en un sistema, respondé siempre en esta estructura:

1. Diagnóstico breve
- Qué hay que hacer.
- Archivos probables.
- Riesgo: bajo / medio / alto.
- Qué datos fiscales faltan.

2. Herramienta recomendada
- OpenCode para exploración acotada y diff chico.
- Codex App para implementar.
- ChatGPT para arquitectura y revisión conceptual.

3. Prompts listos
- Prompt para OpenCode.
- Prompt para Codex App.
- Prompt de revisión final.
Cada prompt debe incluir:
- Archivos a tocar.
- Archivos prohibidos.
- Validaciones.
- Tests.
- Rollback.

4. Checklist de prueba
- Configuración.
- Certificados.
- WSAA.
- WSFEv1.
- Último comprobante.
- Homologación.
- Emisión bloqueada.
- Logs.
- Base de datos.
- Rollback.

5. Advertencias de riesgo
- Fiscal.
- Certificados.
- Producción.
- Numeración de comprobantes.
- CondicionIVAReceptorId.
- Puntos de venta.
- Moneda.
- Datos del receptor.
- Secretos.
```

---

### B. Knowledge base mínima que deberías cargarle al GPT

```text
Referencia técnica validada en TukiPass:

Servicios implementados:
- app/Services/Arca/WsaaClient.php
- app/Services/Arca/WsfeClient.php
- app/Console/Commands/ArcaTestConnection.php
- config/arca.php
- soporte Docker/EasyPanel para certificados base64
- modo preview
- feature flag ARCA_ENABLE_ISSUING=false

Patrón de implementación:
1. Crear config/arca.php.
2. Crear WsaaClient para TRA, firma y TA.
3. Crear WsfeClient para WSFEv1.
4. Crear comando arca:test-connection.
5. Crear comando de preview por entidad del sistema: booking/order/sale/invoice.
6. Crear modelo interno de factura si el sistema necesita trazabilidad.
7. Bloquear emisión real con ARCA_ENABLE_ISSUING=false.
8. Agregar tests unitarios y feature.
9. Validar con contador antes de emitir.
10. Activar emisión real solo con autorización explícita.

Errores ya resueltos en TukiPass:
- SoapClient no instalado.
- Certificado ilegible.
- EasyPanel trunca variables largas.
- private key does not match certificate.
- openssl_pkcs7_sign falla con OpenSSL 3.x.
- dh key too small.
- CUIT no aparece en lista de relaciones.
- Respuestas SOAP variables: objeto, array, lista o escalar.
- Events/Evt no deben tratarse igual que Errors/Err.
```

---

### C. Prompt maestro para usar con Codex App en cada sistema

```text
Necesito integrar ARCA/AFIP para facturación electrónica en este sistema Laravel/PHP.

Objetivo:
Replicar el patrón técnico ya validado en TukiPass, pero adaptándolo quirúrgicamente a este proyecto. La integración debe quedar inicialmente en modo diagnóstico/preview, sin emisión real accidental.

Stack esperado:
- Laravel/PHP
- MySQL
- SOAP
- ARCA WSAA
- ARCA WSFEv1

Implementar o adaptar:

1. Configuración
Crear config/arca.php con:
- environment: homologation|production
- enable_issuing: false por defecto
- cuit
- punto_venta
- tipo_comprobante
- concepto
- cert_path/key_path
- cert/key base64 y soporte split:
  ARCA_CERT_B64_1
  ARCA_CERT_B64_2
  ARCA_KEY_B64_1
  ARCA_KEY_B64_2
- endpoints WSAA y WSFEv1 para homologación y producción

2. Cliente WSAA
Crear app/Services/Arca/WsaaClient.php:
- Generar TRA XML.
- Firmar TRA con certificado y clave privada.
- Usar openssl_cms_sign() si está disponible.
- Fallback a openssl_pkcs7_sign().
- Obtener TA para service wsfe.
- Cachear token/sign con margen de seguridad.
- Validar certificado.
- Nunca loguear token, sign, clave privada ni XML firmado.

3. Cliente WSFEv1
Crear app/Services/Arca/WsfeClient.php:
- Crear SoapClient con trace configurable solo en debug.
- Agregar stream_context con ciphers DEFAULT@SECLEVEL=1 si hace falta.
- Métodos:
  - getServerStatus()
  - getLastComprobante()
  - autorizarComprobante()
  - verificarComprobante()
  - getTiposComprobante()
  - getTiposDocumento()
  - getTiposIva()
  - getTiposMoneda()
  - getCondicionesIvaReceptor() si el WSDL/método está disponible
- Parser defensivo para respuestas SOAP como objeto, array, lista o escalar.
- Separar Errors/Err de Events/Evt.

4. Comando diagnóstico
Crear app/Console/Commands/ArcaTestConnection.php:
- Validar config.
- Validar certificado.
- Autenticar WSAA.
- Consultar FEDummy.
- Consultar parámetros.
- Consultar último comprobante.
- Mostrar datos seguros, sin secretos.

5. Preview fiscal
Crear comando preview adaptado al dominio del sistema:
- Si el sistema vende entradas: preview por booking.
- Si vende productos: preview por order.
- Si vende servicios: preview por invoice/sale.
El preview debe calcular receptor, total, base imponible, IVA si corresponde, tipo de comprobante sugerido y warnings fiscales.
No debe pedir CAE.

6. Emisión real
Crear servicio issuer con bloqueo:
- Si ARCA_ENABLE_ISSUING=false, lanzar excepción clara.
- Si true, autorizar comprobante con FECAESolicitar.
- Guardar request/response resumido si hay modelo de factura.
- Nunca duplicar comprobante si hubo timeout: antes consultar último autorizado y/o comprobante existente.

Archivos prohibidos sin confirmación explícita:
- .env real
- config/auth.php
- rutas críticas
- controladores de pago
- checkout
- gateways
- migraciones productivas irreversibles
- seeds
- lógica de cobro existente

Validaciones obligatorias:
- php artisan config:clear
- php artisan arca:test-connection
- tests unitarios para parser SOAP
- test de bloqueo de emisión real
- test de preview sin llamada a ARCA
- revisar logs sin secretos
- revisar diff final

Rollback:
- Eliminar archivos nuevos app/Services/Arca/*
- Eliminar comandos app/Console/Commands/Arca*
- Eliminar config/arca.php
- Revertir composer/docker solo si fueron modificados
- Revertir migraciones solo si no fueron aplicadas en producción; si fueron aplicadas, crear migración inversa segura

Antes de tocar código:
1. Inspeccioná estructura del proyecto.
2. Identificá versión de Laravel/PHP.
3. Verificá si ext-soap está disponible.
4. Identificá entidad principal facturable.
5. Reportá plan breve y riesgo.
6. Aplicá cambios mínimos.
```

---

### D. Prompt corto para OpenCode: auditar un sistema antes de integrar

```text
Auditá este proyecto para preparar integración ARCA/AFIP.

Objetivo:
No modificar código todavía. Solo detectar estructura, riesgos y puntos de integración.

Buscar:
- Laravel/PHP version
- composer.json
- existencia de ext-soap o Dockerfile
- entidad facturable principal: Booking, Order, Sale, Invoice, Payment
- controladores de checkout/pago
- modelos de cliente/receptor fiscal
- configuración actual de facturación
- .env.example, sin leer .env real
- tests existentes
- comandos artisan existentes

No tocar:
- .env
- pagos
- checkout
- gateways
- auth
- rutas
- migraciones

Entregar:
1. Mapa de archivos relevantes.
2. Riesgos.
3. Datos fiscales faltantes.
4. Plan de implementación ARCA en modo preview.
5. Qué archivos habría que crear.
6. Qué archivos NO tocar.
```

---

### E. Prompt de revisión final después de implementar

```text
Revisá el diff de integración ARCA/AFIP.

Validar:
- No se tocaron pagos, checkout, gateways, auth ni .env.
- ARCA_ENABLE_ISSUING queda false por defecto.
- No hay secretos logueados.
- WsaaClient cachea TA.
- WsfeClient separa Events de Errors.
- Parser SOAP es defensivo.
- Certificados pueden venir por path o base64 split.
- Existe comando arca:test-connection.
- Existe test de bloqueo de emisión real.
- Existe rollback documentado.
- CondicionIVAReceptorId está contemplado o marcado como pendiente crítico.
- No se emite CAE en preview.

Entregar:
1. Resultado: aprobado / requiere cambios.
2. Hallazgos críticos.
3. Hallazgos medios.
4. Mejoras sugeridas.
5. Comandos de prueba.
6. Rollback.
```

---

## 4. Checklist de prueba

Para cada sistema donde lo implementes:

```text
CONFIGURACIÓN
[ ] Existe config/arca.php.
[ ] .env.example tiene variables ARCA sin secretos reales.
[ ] ARCA_ENABLE_ISSUING=false por defecto.
[ ] Ambiente inicial definido: homologation o preview.
[ ] CUIT, punto de venta y tipo de comprobante NO están hardcodeados sin config.

CERTIFICADOS
[ ] Certificado y clave existen o están en B64.
[ ] Soporta B64 dividido.
[ ] La clave privada corresponde al certificado.
[ ] El certificado no está vencido.
[ ] El certificado pertenece al CUIT correcto.
[ ] El certificado está asociado al servicio WSFEv1 en ARCA.

WSAA
[ ] Genera TRA.
[ ] Firma correctamente.
[ ] Obtiene token/sign.
[ ] Cachea TA.
[ ] No imprime token/sign en logs.

WSFEv1
[ ] FEDummy responde.
[ ] Consulta tipos de comprobante.
[ ] Consulta tipos de documento.
[ ] Consulta monedas.
[ ] Consulta condición IVA receptor si aplica.
[ ] Consulta último comprobante autorizado.
[ ] Maneja Errors y Events por separado.

PREVIEW
[ ] Preview no pide CAE.
[ ] Preview calcula receptor.
[ ] Preview calcula total/base/IVA según modelo fiscal.
[ ] Preview bloquea si faltan datos fiscales.
[ ] Preview muestra warnings claros.

EMISIÓN REAL
[ ] Con ARCA_ENABLE_ISSUING=false, emitir lanza excepción.
[ ] No hay forma accidental de emitir desde UI.
[ ] No se conecta emisión real al checkout sin aprobación.
[ ] No se duplica numeración ante retry/timeout.

BASE DE DATOS
[ ] Si hay tabla de facturas, guarda estado, CAE, vencimiento, request/response resumido.
[ ] No guarda secretos.
[ ] Migraciones revisadas antes de producción.

LOGS
[ ] No aparece private key.
[ ] No aparece certificado completo.
[ ] No aparece token/sign.
[ ] Los errores son auditables.

ROLLBACK
[ ] Hay lista de archivos creados.
[ ] Hay forma de desactivar con env/config.
[ ] Si hubo migración productiva, existe plan de reversa seguro.
```

---

## 5. Advertencias de riesgo

La parte técnica ya la tenés bastante resuelta por lo hecho en TukiPass, pero el GPT tiene que ser muy estricto con estas reglas:

1. **No activar emisión real sin contador.** En TukiPass quedó expresamente bloqueado que `ARCA_ENABLE_ISSUING=true` no se use hasta validación fiscal por escrito. 

2. **No asumir modelo fiscal.** En TukiPass incluso hubo un cambio importante: se descartó facturar al organizador y se pivotó a facturar al cliente/comprador. Ese tipo de decisión no la debe tomar el GPT solo. 

3. **No reutilizar certificados entre empresas/sistemas sin validar CUIT y relaciones.** ARCA exige certificado digital y asociación al Web Service de negocio; en producción se gestiona con "Administración de Certificados Digitales" y "Administrador de Relaciones de Clave Fiscal". ([AFIP][3])

4. **No tocar checkout/pagos salvo confirmación.** En TukiPass eso es zona crítica: checkout, BookingController, PaymentGateway y campos HTML sensibles no deben tocarse por una integración fiscal. 

5. **Actualizar el patrón con `CondicionIVAReceptorId`.** Como el vencimiento marcado es 01/06/2026 y hoy es 29/04/2026, este punto ya no es "algún día": tiene que quedar en el GPT como requisito obligatorio para nuevas integraciones.

Con esto podés crear un GPT que no solo "sepa ARCA", sino que replique exactamente la forma segura en que ya lo resolvieron: primero diagnóstico, después preview, después homologación, después emisión real únicamente con validación fiscal.

---

### Referencias

[1]: https://www.afip.gob.ar/ws/documentacion/wsaa.asp "WSAA - Documentación - WEB SERVICES SOAP | ARCA"
[2]: https://www.afip.gob.ar/fe/ayuda/webservice.asp "Ayuda - Factura electrónica | ARCA"
[3]: https://www.afip.gob.ar/ws/documentacion/certificados.asp "Certificados - Documentación - WEB SERVICES SOAP | ARCA"
