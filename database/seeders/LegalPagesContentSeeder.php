<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegalPagesContentSeeder extends Seeder
{
  public function run()
  {
    $operator = 'TAYRONA - GROUP S.A.S.';
    $cuit = '30-71885087-4';
    $address = 'Pueyrredón Av. 1357, Ciudad Autónoma de Buenos Aires, Argentina';
    $domain = 'https://www.tukipass.com';
    $supportEmail = 'soporte@tukipass.com';
    $privacyEmail = 'info@tukipass.com';
    $updatedAt = '15/04/2026';
    $operatorNotice = "TukiPass es una marca, nombre comercial y/o plataforma operada por {$operator}, CUIT {$cuit}, con domicilio legal/fiscal en {$address}.";

    $pages = [
      'politica-de-privacidad' => [
        'title' => 'Política de privacidad',
        'meta_keywords' => 'TukiPass, política de privacidad, datos personales, Facebook Login, Google Login, entradas, eventos, TAYRONA',
        'meta_description' => 'Conocé cómo TukiPass, operada por TAYRONA - GROUP S.A.S., recopila, usa y protege tus datos personales.',
        'content' => <<<HTML
<h2>Política de privacidad</h2>
<p><strong>Última actualización:</strong> {$updatedAt}</p>
<p>{$operatorNotice}</p>
<p>Esta Política de privacidad explica cómo {$operator}, responsable legal, fiscal y operativo de TukiPass, recopila, usa, conserva y protege los datos personales de las personas que usan {$domain}.</p>
<p>Para consultas de privacidad y datos personales podés escribir a {$privacyEmail}. Para soporte general, reclamos o ayuda operativa podés escribir a {$supportEmail}.</p>

<h3>1. Datos que recopilamos</h3>
<p>Podemos recopilar datos que nos das al crear una cuenta, comprar entradas, contactar soporte, publicar eventos como organizador o usar funciones de la plataforma.</p>
<ul>
  <li>Datos de identificación y cuenta: nombre, apellido, email, teléfono, nombre de usuario y contraseña cifrada cuando corresponda.</li>
  <li>Datos de compra: evento, cantidad y tipo de entradas, reservas, comprobantes, estado de pago, fecha de compra y datos necesarios para emitir o validar tickets.</li>
  <li>Datos de compradores y asistentes: datos necesarios para entregar entradas, validar accesos, gestionar reclamos, enviar comunicaciones operativas o cumplir condiciones del organizador.</li>
  <li>Datos de organizadores: información de cuenta, datos de contacto, eventos publicados, configuración de ventas, reportes y datos necesarios para gestionar cobros o comunicaciones.</li>
  <li>Datos técnicos: dirección IP, dispositivo, navegador, sesiones, registros de seguridad, cookies y tecnologías similares.</li>
  <li>Mensajes o solicitudes enviados por formularios de contacto, soporte o canales de atención.</li>
</ul>

<h3>2. Facebook Login y Google Login</h3>
<p>TukiPass puede permitir el acceso mediante Facebook Login y Google Login para clientes. Estos proveedores pueden compartir con TukiPass datos básicos de perfil, como nombre, email, identificador del proveedor y foto de perfil si está disponible.</p>
<p>Usamos esos datos para crear o acceder a tu cuenta, evitar cuentas duplicadas, mejorar la seguridad del inicio de sesión y facilitar el uso de la plataforma. TukiPass no publica contenido en tus redes sociales, no accede a tus publicaciones privadas y no usa Pages API.</p>

<h3>3. Para qué usamos los datos</h3>
<ul>
  <li>Crear, autenticar y administrar cuentas.</li>
  <li>Procesar compras, reservas, tickets, pagos y comprobantes.</li>
  <li>Permitir que organizadores gestionen eventos, ventas, asistentes y validación de accesos.</li>
  <li>Enviar confirmaciones, entradas, avisos operativos, comunicaciones de soporte y novedades relacionadas con el servicio.</li>
  <li>Prevenir fraude, abuso, accesos no autorizados y usos indebidos.</li>
  <li>Cumplir obligaciones legales, contables, fiscales o regulatorias.</li>
  <li>Analizar el funcionamiento de la plataforma y mejorar la experiencia.</li>
</ul>

<h3>4. Pagos y proveedores externos</h3>
<p>Los pagos pueden procesarse mediante proveedores externos, como Mercado Pago, Stripe, PayPal u otros gateways habilitados. TukiPass no almacena los datos completos de tarjetas de crédito o débito cuando el procesamiento ocurre dentro de esos proveedores.</p>
<p>También podemos usar proveedores técnicos de hosting, correo, analítica, seguridad, almacenamiento, automatización, atención al cliente y operación de la plataforma.</p>

<h3>5. Organizadores y asistentes</h3>
<p>Podemos compartir datos con organizadores cuando sea necesario para gestionar entradas, validar acceso al evento, resolver consultas, emitir listados de asistentes o enviar comunicaciones operativas vinculadas al evento adquirido.</p>
<p>Los organizadores son responsables por el uso que hagan de la información recibida para la operación de sus eventos y por cumplir la normativa aplicable a su actividad.</p>

<h3>6. Meta Pixel y herramientas de medición</h3>
<p>Los organizadores pueden configurar herramientas de medición o marketing, incluyendo Meta Pixel, en páginas de sus eventos. En esos casos, el organizador es responsable de informar a sus asistentes y obtener los consentimientos que correspondan según la normativa aplicable.</p>

<h3>7. Cookies y tecnologías similares</h3>
<p>Usamos cookies necesarias para iniciar sesión, mantener sesiones, recordar preferencias, proteger formularios y operar la plataforma. También pueden usarse cookies analíticas o de marketing según la configuración del sitio o de los organizadores. Para más información, consultá la Política de cookies.</p>

<h3>8. Con quién compartimos datos</h3>
<p>Podemos compartir datos con organizadores, proveedores de pago, proveedores técnicos, autoridades competentes cuando corresponda y terceros necesarios para prestar el servicio, procesar operaciones, cumplir obligaciones legales o proteger derechos.</p>

<h3>9. Conservación de datos</h3>
<p>Conservamos los datos mientras sean necesarios para prestar el servicio, cumplir obligaciones legales, resolver reclamos, prevenir fraude, mantener registros contables o proteger nuestros derechos. Algunos datos pueden conservarse de forma anonimizada o agregada para estadísticas.</p>

<h3>10. Derechos del usuario</h3>
<p>Podés solicitar acceso, actualización, rectificación o eliminación de tus datos personales escribiendo a {$privacyEmail}. Es posible que solicitemos información adicional para verificar tu identidad antes de procesar una solicitud.</p>

<h3>11. Eliminación de datos</h3>
<p>Para solicitar la eliminación de tus datos, enviá un email a {$privacyEmail} indicando el email asociado a tu cuenta, tu nombre, si usaste Facebook Login o Google Login y una solicitud expresa de eliminación de datos. La página de Eliminación de datos contiene instrucciones detalladas.</p>

<h3>12. AAIP y bases de datos personales</h3>
<p>El isologotipo de la AAIP identifica a las bases de datos personales debidamente inscriptas en el Registro Nacional de Bases de Datos Personales. TukiPass podrá exhibir dicha identificación cuando corresponda y cuente con la inscripción aplicable.</p>

<h3>13. Seguridad</h3>
<p>Aplicamos medidas técnicas y organizativas razonables para proteger los datos personales contra accesos no autorizados, pérdida, uso indebido o alteración. Ningún sistema es completamente infalible, por lo que también es importante que protejas tus credenciales.</p>

<h3>14. Cambios en esta política</h3>
<p>Podemos actualizar esta política para reflejar cambios operativos, legales o técnicos. Si incorporamos Pages API, Social Plugins SDK u otras integraciones sociales nuevas, actualizaremos esta política según corresponda.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'terminos-y-condiciones' => [
        'title' => 'Términos y condiciones',
        'meta_keywords' => 'TukiPass, términos y condiciones, entradas, eventos, organizadores, pagos, tickets, TAYRONA',
        'meta_description' => 'Conocé los términos de uso de TukiPass, operada por TAYRONA - GROUP S.A.S., para compradores y organizadores.',
        'content' => <<<HTML
<h2>Términos y condiciones</h2>
<p><strong>Última actualización:</strong> {$updatedAt}</p>
<p>{$operatorNotice}</p>
<p>Estos Términos y condiciones regulan el uso de TukiPass, disponible en {$domain}. Al usar la plataforma, crear una cuenta, publicar eventos o comprar entradas, aceptás estas condiciones.</p>
<p>Toda referencia a TukiPass, la plataforma, nosotros o nuestro debe entenderse como referencia a {$operator}, salvo que se indique otra cosa. Contacto de soporte: {$supportEmail}. Contacto legal, privacidad y datos personales: {$privacyEmail}.</p>

<h3>1. Rol de TukiPass</h3>
<p>TukiPass actúa como plataforma tecnológica e intermediario para la publicación de eventos, gestión de ventas y emisión de entradas. Salvo que se indique expresamente lo contrario, TukiPass no es organizador, productor ni responsable directo de la realización de los eventos publicados por terceros.</p>

<h3>2. Responsabilidad de los organizadores</h3>
<p>Los organizadores son responsables por la información del evento, precios, cupos, disponibilidad, condiciones de ingreso, permisos, autorizaciones, seguridad, cambios de fecha, cancelaciones, reprogramaciones, atención a asistentes y cumplimiento de la normativa aplicable.</p>
<p>El organizador debe publicar información veraz, completa y actualizada, y responder por reclamos vinculados con la realización del evento.</p>

<h3>3. Responsabilidad de compradores y asistentes</h3>
<p>Las personas que compran entradas son responsables por ingresar datos correctos, revisar la información del evento antes de comprar, verificar fecha, cantidad, precio, cargos de servicio, condiciones de ingreso y restricciones aplicables.</p>
<p>La admisión al evento puede estar sujeta a validación de identidad, edad mínima, cupo, normas del lugar, medidas de seguridad o condiciones definidas por el organizador.</p>

<h3>4. Cuentas y acceso</h3>
<p>Para usar algunas funciones puede ser necesario crear una cuenta. TukiPass puede permitir acceso mediante email y contraseña, Facebook Login o Google Login. El usuario es responsable por la confidencialidad de sus credenciales y por la actividad realizada desde su cuenta.</p>
<p>TukiPass no publica contenido en redes sociales del usuario mediante Social Login.</p>

<h3>5. Compra y uso de entradas</h3>
<p>Las entradas son emitidas para el evento, fecha, sector, categoría o modalidad indicados al momento de compra. Pueden ser personales, transferibles o sujetas a validaciones, según lo defina el organizador.</p>
<p>La reventa, duplicación, manipulación o uso fraudulento de entradas puede derivar en la cancelación de la compra, bloqueo de cuenta o rechazo de ingreso.</p>

<h3>6. Pagos, cobros y facturación</h3>
<p>Los pagos pueden procesarse mediante Mercado Pago, Stripe, PayPal u otros proveedores externos. Cada proveedor puede aplicar sus propias condiciones, medidas de seguridad, plazos de acreditación, comisiones o validaciones.</p>
<p>Los cobros, cargos, comisiones, reintegros, facturación o gestiones de pago vinculadas al uso de la plataforma podrán ser realizados por {$operator} directamente o mediante proveedores externos. El cargo puede verse identificado como {$operator}, TukiPass, Mercado Pago u otra denominación asociada al procesador utilizado.</p>
<p>TukiPass no garantiza la aprobación de pagos por parte de terceros y puede rechazar operaciones sospechosas, incompletas o contrarias a estas condiciones.</p>

<h3>7. Reembolsos, cancelaciones, reprogramaciones y arrepentimiento</h3>
<p>Los reembolsos se rigen por la Política de reembolsos, por la política definida por cada organizador y por la normativa aplicable. Algunas comisiones de servicio o costos de procesamiento pueden tener tratamiento diferenciado si fueron informados y resultan compatibles con la normativa aplicable.</p>
<p>Cuando corresponda legalmente, el usuario consumidor podrá ejercer el derecho de revocación o arrepentimiento por los canales habilitados. TukiPass podrá poner a disposición un Botón de Arrepentimiento o mecanismo equivalente conforme la normativa aplicable, sin que esta mención implique prometer una funcionalidad específica si todavía no se encuentra implementada como flujo operativo.</p>

<h3>8. Uso indebido</h3>
<p>Está prohibido usar TukiPass para fraude, suplantación de identidad, publicación de eventos falsos, manipulación de tickets, ataques técnicos, scraping no autorizado, spam, abuso de promociones o cualquier actividad ilegal o dañina.</p>
<p>TukiPass puede suspender, limitar o cancelar cuentas y operaciones ante indicios de abuso, fraude, incumplimiento o riesgo para la plataforma.</p>

<h3>9. Propiedad intelectual</h3>
<p>La marca TukiPass, el diseño, software, interfaces, textos, bases de datos y demás elementos de la plataforma pertenecen a sus titulares o licenciantes. Los organizadores conservan responsabilidad sobre los contenidos que publican y declaran contar con derechos para usarlos.</p>

<h3>10. Limitación de responsabilidad</h3>
<p>TukiPass no será responsable por incumplimientos atribuibles a organizadores, asistentes, proveedores de pago, terceros técnicos, fuerza mayor, fallas externas, cambios de programación o condiciones del evento ajenas a la plataforma.</p>

<h3>11. Ley aplicable</h3>
<p>Estos términos se interpretan de acuerdo con la normativa aplicable de la República Argentina, sin perjuicio de los derechos irrenunciables que pudieran corresponder a usuarios consumidores.</p>

<h3>12. Soporte</h3>
<p>Para consultas operativas, reclamos o reembolsos podés escribir a {$supportEmail}. Para consultas sobre privacidad o datos personales, escribí a {$privacyEmail}.</p>

<h3>13. Cambios en los términos</h3>
<p>Podemos actualizar estos términos para reflejar cambios del servicio, requisitos legales o mejoras operativas. La versión vigente será la publicada en {$domain}.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'eliminacion-de-datos' => [
        'title' => 'Eliminación de datos',
        'meta_keywords' => 'TukiPass, eliminación de datos, borrar cuenta, Facebook Login, Google Login, privacidad, TAYRONA',
        'meta_description' => 'Instrucciones para solicitar la eliminación de datos personales en TukiPass, incluyendo Facebook Login y Google Login.',
        'content' => <<<HTML
<h2>Eliminación de datos</h2>
<p><strong>Última actualización:</strong> {$updatedAt}</p>
<p>{$operatorNotice}</p>
<p>Esta página explica cómo solicitar la eliminación de datos personales asociados a una cuenta de TukiPass. También sirve como URL pública de instrucciones para usuarios que accedieron mediante Facebook Login o Google Login.</p>

<h3>1. Cómo pedir la eliminación</h3>
<p>Para solicitar la eliminación de tus datos, enviá un email a {$privacyEmail} con el asunto "Solicitud de eliminación de datos".</p>
<p>Incluí la siguiente información:</p>
<ol>
  <li>Nombre y apellido.</li>
  <li>Email asociado a tu cuenta de TukiPass.</li>
  <li>Si accediste con Facebook Login, Google Login o email y contraseña.</li>
  <li>Una solicitud expresa de eliminación de tus datos personales.</li>
  <li>Cualquier dato adicional que nos ayude a identificar tu cuenta o tus compras.</li>
</ol>

<h3>2. Verificación de identidad</h3>
<p>Podemos pedir información adicional para verificar que la solicitud pertenece a la persona titular de la cuenta. Esto ayuda a evitar eliminaciones no autorizadas.</p>

<h3>3. Qué datos se eliminan</h3>
<p>Cuando corresponda, eliminaremos o anonimizaremos datos de cuenta, perfil, identificadores de Social Login, datos de contacto, historial operativo no necesario, comunicaciones de soporte y otros datos personales asociados a la cuenta.</p>

<h3>4. Qué datos pueden conservarse</h3>
<p>Algunos datos pueden conservarse por motivos de facturación, obligaciones legales, registros contables, prevención de fraude, seguridad, defensa de derechos, resolución de reclamos o estadísticas anonimizadas. En esos casos, limitaremos su uso a esas finalidades.</p>

<h3>5. Plazo de respuesta</h3>
<p>Procesaremos la solicitud dentro de los plazos legales aplicables. Si necesitamos información adicional para verificar tu identidad o precisar el alcance de la solicitud, podremos requerirla por el mismo medio de contacto.</p>

<h3>6. Facebook Login y Google Login</h3>
<p>Si usaste Facebook Login o Google Login, la eliminación en TukiPass alcanza los datos que TukiPass recibió y conserva, como nombre, email, identificador del proveedor y foto de perfil si aplica. Para eliminar datos mantenidos por Facebook o Google, deberás gestionar la solicitud directamente desde esas plataformas.</p>

<h3>7. Sin callback automático</h3>
<p>Esta página contiene instrucciones públicas para solicitar eliminación de datos. TukiPass no informa en esta página un callback automático de Meta ni un botón de borrado dentro del dashboard.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'politica-de-reembolsos' => [
        'title' => 'Política de reembolsos, cancelaciones y arrepentimiento',
        'meta_keywords' => 'TukiPass, política de reembolsos, cancelaciones, reprogramaciones, arrepentimiento, entradas',
        'meta_description' => 'Conocé cómo se gestionan reembolsos, cancelaciones, reprogramaciones y arrepentimiento en TukiPass.',
        'content' => <<<HTML
<h2>Política de reembolsos, cancelaciones y arrepentimiento</h2>
<p><strong>Última actualización:</strong> {$updatedAt}</p>
<p>{$operatorNotice}</p>
<p>Esta Política de reembolsos explica criterios generales aplicables a compras realizadas mediante TukiPass. Las condiciones específicas pueden variar según el evento, el organizador, el medio de pago y la normativa aplicable.</p>

<h3>1. Revisión previa de la compra</h3>
<p>Antes de confirmar una compra, la persona usuaria debe revisar evento, fecha, cantidad, precio, cargos de servicio, condiciones de ingreso, restricciones, ubicación y cualquier otra información publicada por el organizador.</p>

<h3>2. Rol de TukiPass</h3>
<p>TukiPass funciona como plataforma tecnológica e intermediario para la venta y gestión de entradas. En eventos publicados por terceros, el organizador es responsable principal por la realización del evento y por definir la política comercial aplicable.</p>

<h3>3. Cancelaciones</h3>
<p>Si un evento es cancelado definitivamente, el organizador deberá informar la cancelación y las condiciones aplicables. TukiPass podrá colaborar en la gestión del reembolso según los fondos disponibles, el gateway de pago utilizado y las condiciones informadas para el evento.</p>

<h3>4. Reprogramaciones</h3>
<p>Si un evento es reprogramado, las entradas pueden seguir siendo válidas para la nueva fecha. Si el organizador habilita devoluciones por reprogramación, se informará el plazo y mecanismo para solicitarlas.</p>

<h3>5. Derecho de arrepentimiento</h3>
<p>Cuando corresponda conforme la normativa aplicable para compras a distancia o por medios electrónicos, el usuario consumidor podrá ejercer el derecho de revocación o arrepentimiento por los canales habilitados.</p>
<p>TukiPass podrá disponer un Botón de Arrepentimiento o formulario equivalente, sin perjuicio de otros canales de atención que se encuentren disponibles.</p>

<h3>6. Política del organizador</h3>
<p>Antes de comprar, la persona compradora debe revisar la descripción del evento, condiciones de ingreso, política del organizador y restricciones aplicables. Algunas entradas pueden no admitir devolución salvo cancelación, reprogramación, arrepentimiento legalmente aplicable u obligación legal.</p>

<h3>7. Comisiones y costos</h3>
<p>Las comisiones de servicio, costos de procesamiento, cargos administrativos o costos cobrados por gateways externos pueden tener tratamiento diferenciado si fue informado y resulta compatible con la normativa aplicable.</p>

<h3>8. Medio de devolución</h3>
<p>Cuando corresponda un reintegro, la devolución se realizará preferentemente por el mismo medio de pago utilizado, salvo imposibilidad técnica, reglas del procesador, validaciones antifraude o acuerdo permitido entre las partes.</p>

<h3>9. Cómo solicitar un reembolso</h3>
<p>Para solicitar un reembolso, escribí a {$supportEmail} indicando nombre, email de compra, evento, número de orden o comprobante, motivo de la solicitud y cualquier documentación relevante.</p>

<h3>10. Plazos</h3>
<p>Los plazos de análisis, aprobación y acreditación pueden variar según el organizador, el medio de pago, el banco emisor, el gateway utilizado y las validaciones antifraude aplicables. TukiPass no controla los tiempos internos de acreditación de terceros.</p>

<h3>11. Casos donde puede no aplicar reembolso</h3>
<p>Puede no corresponder reembolso en casos de inasistencia, error en la compra imputable al usuario, incumplimiento de condiciones de ingreso, entradas usadas o validadas, compras fuera de plazo de reclamo, fraude o supuestos expresamente informados como no reembolsables.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'politica-de-cookies' => [
        'title' => 'Política de cookies',
        'meta_keywords' => 'TukiPass, política de cookies, Meta Pixel, cookies analíticas, cookies necesarias, TAYRONA',
        'meta_description' => 'Conocé cómo TukiPass usa cookies necesarias, analíticas y de marketing, incluyendo Meta Pixel de organizadores.',
        'content' => <<<HTML
<h2>Política de cookies</h2>
<p><strong>Última actualización:</strong> {$updatedAt}</p>
<p>{$operatorNotice}</p>
<p>Esta Política de cookies explica cómo TukiPass usa cookies y tecnologías similares en {$domain}.</p>

<h3>1. Qué son las cookies</h3>
<p>Las cookies son pequeños archivos que se guardan en tu navegador o dispositivo para recordar información, mantener sesiones, mejorar la seguridad, medir el uso del sitio o personalizar algunas funciones.</p>

<h3>2. Cookies necesarias</h3>
<p>Usamos cookies necesarias para que la plataforma funcione correctamente: inicio de sesión, carrito o reservas cuando corresponda, protección de formularios, seguridad, preferencias básicas y continuidad de sesión.</p>

<h3>3. Cookies analíticas</h3>
<p>Podemos usar herramientas analíticas para entender el uso de la plataforma, detectar errores, medir rendimiento y mejorar la experiencia. Estas mediciones pueden usar identificadores técnicos, datos de dispositivo, navegador, páginas visitadas y eventos de navegación.</p>

<h3>4. Cookies de marketing y tracking</h3>
<p>Algunas páginas pueden incluir tecnologías de marketing o medición publicitaria. En particular, los organizadores pueden configurar Meta Pixel en páginas de sus eventos para medir campañas, conversiones o audiencias.</p>
<p>Cada organizador es responsable por el uso de su propio pixel, por informar a sus asistentes y por obtener los consentimientos que correspondan según la normativa aplicable.</p>

<h3>5. Links para compartir</h3>
<p>TukiPass puede incluir links simples para compartir eventos en redes sociales. Esos links redirigen al sitio o aplicación correspondiente, pero TukiPass no carga actualmente Social Plugins SDK de Facebook.</p>

<h3>6. Pages API y Social Plugins</h3>
<p>TukiPass no usa Pages API ni Social Plugins SDK actualmente. Si en el futuro incorporamos esas integraciones, actualizaremos esta política y la información relacionada con privacidad.</p>

<h3>7. Cómo gestionar cookies</h3>
<p>Podés configurar tu navegador para bloquear, eliminar o limitar cookies. Si bloqueás cookies necesarias, algunas funciones de TukiPass pueden no funcionar correctamente.</p>

<h3>8. Cambios en esta política</h3>
<p>Podemos actualizar esta política para reflejar cambios técnicos, legales o funcionales. La versión vigente será la publicada en {$domain}.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'defensa-al-consumidor' => [
        'title' => 'Defensa al consumidor',
        'meta_keywords' => 'TukiPass, defensa al consumidor, reclamos, arrepentimiento, soporte, TAYRONA',
        'meta_description' => 'Información de defensa al consumidor, reclamos, arrepentimiento y canales de contacto de TukiPass.',
        'content' => <<<HTML
<h2>Defensa al consumidor</h2>
<p><strong>Última actualización:</strong> {$updatedAt}</p>
<p>{$operatorNotice}</p>
<p>Esta página reúne información de contacto y canales de atención para usuarios consumidores de TukiPass.</p>

<h3>1. Datos del operador</h3>
<ul>
  <li>Operador legal, fiscal y operativo: {$operator}</li>
  <li>CUIT: {$cuit}</li>
  <li>Domicilio legal/fiscal: {$address}</li>
  <li>Dominio: {$domain}</li>
  <li>Soporte, reclamos y reembolsos: {$supportEmail}</li>
  <li>Privacidad y datos personales: {$privacyEmail}</li>
</ul>

<h3>2. Canal de reclamos</h3>
<p>Para iniciar un reclamo relacionado con compras, entradas, pagos, reembolsos, cancelaciones, reprogramaciones o uso de la plataforma, escribí a {$supportEmail} indicando nombre, email de compra, evento, número de orden o comprobante y una descripción clara del reclamo.</p>

<h3>3. Solicitudes de arrepentimiento</h3>
<p>Cuando corresponda conforme la normativa aplicable para compras a distancia o por medios electrónicos, el usuario consumidor podrá solicitar la revocación o arrepentimiento por los canales habilitados. TukiPass podrá disponer un Botón de Arrepentimiento o mecanismo equivalente cuando resulte aplicable.</p>

<h3>4. Documentos relacionados</h3>
<p>Para conocer las condiciones aplicables, consultá los Términos y condiciones, la Política de privacidad y la Política de reembolsos, cancelaciones y arrepentimiento.</p>

<h3>5. Información fiscal</h3>
<p>La constancia fiscal o código QR Data Fiscal correspondiente a {$operator} podrá ser exhibido en esta sección cuando se encuentre disponible para consulta pública.</p>

<h3>6. Identificación AAIP</h3>
<p>La identificación de la AAIP podrá exhibirse únicamente cuando corresponda y exista inscripción aplicable de bases de datos personales. Esta página no incorpora imágenes, códigos QR ni isologotipos no confirmados.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
    ];

    DB::transaction(function () use ($pages) {
      $defaultLanguageId = DB::table('page_contents')
        ->whereRaw('BINARY slug = ?', ['politica-de-privacidad'])
        ->value('language_id')
        ?? DB::table('languages')->where('code', 'es')->value('id')
        ?? DB::table('languages')->where('is_default', 1)->value('id')
        ?? 8;

      foreach ($pages as $slug => $data) {
        $pageContent = DB::table('page_contents')
          ->whereRaw('BINARY slug = ?', [$slug])
          ->first();

        if (!$pageContent) {
          $pageId = DB::table('pages')->insertGetId([
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
          ]);

          DB::table('page_contents')->insert([
            'language_id' => $defaultLanguageId,
            'page_id' => $pageId,
            'title' => $data['title'],
            'slug' => $slug,
            'content' => $data['content'],
            'meta_keywords' => $data['meta_keywords'],
            'meta_description' => $data['meta_description'],
            'created_at' => now(),
            'updated_at' => now(),
          ]);

          continue;
        }

        DB::table('page_contents')
          ->where('id', $pageContent->id)
          ->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'meta_keywords' => $data['meta_keywords'],
            'meta_description' => $data['meta_description'],
            'updated_at' => now(),
          ]);
      }
    });
  }
}
