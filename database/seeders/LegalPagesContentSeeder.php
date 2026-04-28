<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LegalPagesContentSeeder extends Seeder
{
  public function run()
  {
    $pages = [
      'politica-de-privacidad' => [
        'title' => 'Política de privacidad',
        'meta_keywords' => 'TukiPass, política de privacidad, datos personales, Facebook Login, Google Login, entradas, eventos',
        'meta_description' => 'Conocé cómo TukiPass recopila, usa y protege tus datos personales, incluyendo Facebook Login, Google Login, compras de entradas y soporte.',
        'content' => <<<'HTML'
<h2>Política de privacidad</h2>
<p><strong>Última actualización:</strong> [FECHA_ACTUALIZACION]</p>
<p>Esta Política de privacidad explica cómo [RAZON_SOCIAL], responsable de TukiPass, recopila, usa, conserva y protege los datos personales de las personas que usan [DOMINIO].</p>
<p>Datos del responsable: [RAZON_SOCIAL], CUIT [CUIT], domicilio legal [DOMICILIO_LEGAL]. Para consultas de privacidad podés escribir a [EMAIL_PRIVACIDAD]. Para soporte general podés escribir a [EMAIL_SOPORTE].</p>

<h3>1. Datos que recopilamos</h3>
<p>Podemos recopilar datos que nos das al crear una cuenta, comprar entradas, contactar soporte, publicar eventos como organizador o usar funciones de la plataforma.</p>
<ul>
  <li>Datos de identificación y cuenta: nombre, apellido, email, teléfono, nombre de usuario y contraseña cifrada cuando corresponda.</li>
  <li>Datos de compra: evento, cantidad y tipo de entradas, reservas, comprobantes, estado de pago, fecha de compra y datos necesarios para emitir o validar tickets.</li>
  <li>Datos de organizadores: información de cuenta, datos de contacto, eventos publicados, configuración de ventas, reportes y datos necesarios para gestionar cobros o comunicaciones.</li>
  <li>Datos técnicos: dirección IP, dispositivo, navegador, sesiones, registros de seguridad, cookies y tecnologías similares.</li>
  <li>Mensajes o solicitudes enviadas por formularios de contacto, soporte o canales de atención.</li>
</ul>

<h3>2. Facebook Login y Google Login</h3>
<p>TukiPass puede permitir el acceso mediante Facebook Login y Google Login para clientes. Estos proveedores pueden compartir con TukiPass datos básicos de perfil, como nombre, email, identificador del proveedor y foto de perfil si está disponible.</p>
<p>Usamos esos datos para crear o acceder a tu cuenta, evitar cuentas duplicadas, mejorar la seguridad del inicio de sesión y facilitar el uso de la plataforma. TukiPass no publica contenido en tus redes sociales, no accede a tus publicaciones privadas y no usa Pages API.</p>

<h3>3. Para qué usamos los datos</h3>
<ul>
  <li>Crear, autenticar y administrar cuentas.</li>
  <li>Procesar compras, reservas, tickets, pagos y comprobantes.</li>
  <li>Permitir que organizadores gestionen eventos y ventas.</li>
  <li>Enviar confirmaciones, entradas, avisos operativos, comunicaciones de soporte y novedades relacionadas con el servicio.</li>
  <li>Prevenir fraude, abuso, accesos no autorizados y usos indebidos.</li>
  <li>Cumplir obligaciones legales, contables, fiscales o regulatorias.</li>
  <li>Analizar el funcionamiento de la plataforma y mejorar la experiencia.</li>
</ul>

<h3>4. Pagos y proveedores externos</h3>
<p>Los pagos pueden procesarse mediante proveedores externos, como MercadoPago, Stripe, PayPal u otros gateways habilitados. TukiPass no almacena los datos completos de tarjetas de crédito o débito cuando el procesamiento ocurre dentro de esos proveedores.</p>
<p>También podemos usar proveedores técnicos de hosting, correo, analítica, seguridad, almacenamiento, automatización, atención al cliente y operación de la plataforma.</p>

<h3>5. Organizadores y Meta Pixel</h3>
<p>Los organizadores pueden configurar herramientas de medición o marketing, incluyendo Meta Pixel, en páginas de sus eventos. En esos casos, el organizador es responsable de informar a sus asistentes y obtener los consentimientos que correspondan según la normativa aplicable.</p>

<h3>6. Cookies y tecnologías similares</h3>
<p>Usamos cookies necesarias para iniciar sesión, mantener sesiones, recordar preferencias, proteger formularios y operar la plataforma. También pueden usarse cookies analíticas o de marketing según la configuración del sitio o de los organizadores. Para más información, consultá la Política de cookies.</p>

<h3>7. Con quién compartimos datos</h3>
<p>Podemos compartir datos con organizadores cuando sea necesario para gestionar entradas, acceso al evento, soporte o comunicaciones operativas. También compartimos datos con proveedores de pago, proveedores técnicos, autoridades competentes cuando corresponda y terceros necesarios para prestar el servicio.</p>

<h3>8. Conservación de datos</h3>
<p>Conservamos los datos mientras sean necesarios para prestar el servicio, cumplir obligaciones legales, resolver reclamos, prevenir fraude, mantener registros contables o proteger nuestros derechos. Algunos datos pueden conservarse de forma anonimizada o agregada para estadísticas.</p>

<h3>9. Derechos del usuario</h3>
<p>Podés solicitar acceso, actualización, rectificación o eliminación de tus datos personales escribiendo a [EMAIL_PRIVACIDAD]. Es posible que solicitemos información adicional para verificar tu identidad antes de procesar una solicitud.</p>

<h3>10. Eliminación de datos</h3>
<p>Para solicitar la eliminación de tus datos, enviá un email a [EMAIL_PRIVACIDAD] indicando el email asociado a tu cuenta, tu nombre, si usaste Facebook Login o Google Login y una solicitud expresa de eliminación de datos. La página de Eliminación de datos contiene instrucciones detalladas.</p>

<h3>11. Seguridad</h3>
<p>Aplicamos medidas técnicas y organizativas razonables para proteger los datos personales contra accesos no autorizados, pérdida, uso indebido o alteración. Ningún sistema es completamente infalible, por lo que también es importante que protejas tus credenciales.</p>

<h3>12. Cambios en esta política</h3>
<p>Podemos actualizar esta política para reflejar cambios operativos, legales o técnicos. Si incorporamos Pages API, Social Plugins SDK u otras integraciones sociales nuevas, actualizaremos esta política según corresponda.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'terminos-y-condiciones' => [
        'title' => 'Términos y condiciones',
        'meta_keywords' => 'TukiPass, términos y condiciones, entradas, eventos, organizadores, pagos, tickets',
        'meta_description' => 'Conocé los términos de uso de TukiPass para compradores, organizadores, publicación de eventos, compra de entradas, pagos y uso de cuentas.',
        'content' => <<<'HTML'
<h2>Términos y condiciones</h2>
<p><strong>Última actualización:</strong> [FECHA_ACTUALIZACION]</p>
<p>Estos Términos y condiciones regulan el uso de TukiPass, disponible en [DOMINIO]. Al usar la plataforma, crear una cuenta, publicar eventos o comprar entradas, aceptás estas condiciones.</p>
<p>Responsable de la plataforma: [RAZON_SOCIAL], CUIT [CUIT], domicilio legal [DOMICILIO_LEGAL]. Contacto de soporte: [EMAIL_SOPORTE].</p>

<h3>1. Rol de TukiPass</h3>
<p>TukiPass actúa como plataforma tecnológica e intermediario para la publicación de eventos, gestión de ventas y emisión de entradas. Salvo que se indique expresamente lo contrario, TukiPass no es organizador, productor ni responsable directo de la realización de los eventos publicados por terceros.</p>

<h3>2. Responsabilidad de los organizadores</h3>
<p>Los organizadores son responsables por la información del evento, precios, cupos, disponibilidad, condiciones de ingreso, permisos, autorizaciones, cambios de fecha, cancelaciones, reprogramaciones, atención a asistentes y cumplimiento de la normativa aplicable.</p>
<p>El organizador debe publicar información veraz, completa y actualizada, y responder por reclamos vinculados con la realización del evento.</p>

<h3>3. Responsabilidad de compradores y asistentes</h3>
<p>Las personas que compran entradas son responsables por ingresar datos correctos, revisar la información del evento antes de comprar, conservar sus comprobantes y usar sus entradas conforme a las condiciones informadas.</p>
<p>La admisión al evento puede estar sujeta a validación de identidad, edad mínima, cupo, normas del lugar, medidas de seguridad o condiciones definidas por el organizador.</p>

<h3>4. Cuentas y acceso</h3>
<p>Para usar algunas funciones puede ser necesario crear una cuenta. TukiPass puede permitir acceso mediante email y contraseña, Facebook Login o Google Login. El usuario es responsable por la confidencialidad de sus credenciales y por la actividad realizada desde su cuenta.</p>
<p>TukiPass no publica contenido en redes sociales del usuario mediante Social Login.</p>

<h3>5. Compra y uso de entradas</h3>
<p>Las entradas son emitidas para el evento, fecha, sector, categoría o modalidad indicados al momento de compra. Pueden ser personales, transferibles o sujetas a validaciones, según lo defina el organizador.</p>
<p>La reventa, duplicación, manipulación o uso fraudulento de entradas puede derivar en la cancelación de la compra, bloqueo de cuenta o rechazo de ingreso.</p>

<h3>6. Pagos y gateways externos</h3>
<p>Los pagos pueden procesarse mediante MercadoPago, Stripe, PayPal u otros proveedores externos. Cada proveedor puede aplicar sus propias condiciones, medidas de seguridad, plazos de acreditación, comisiones o validaciones.</p>
<p>TukiPass no garantiza la aprobación de pagos por parte de terceros y puede rechazar operaciones sospechosas, incompletas o contrarias a estas condiciones.</p>

<h3>7. Reembolsos, cancelaciones y reprogramaciones</h3>
<p>Los reembolsos se rigen por la Política de reembolsos, por la política definida por cada organizador y por la normativa aplicable. Algunas comisiones de servicio o costos de procesamiento pueden no ser reembolsables.</p>

<h3>8. Uso indebido</h3>
<p>Está prohibido usar TukiPass para fraude, suplantación de identidad, publicación de eventos falsos, manipulación de tickets, ataques técnicos, scraping no autorizado, spam, abuso de promociones o cualquier actividad ilegal o dañina.</p>
<p>TukiPass puede suspender, limitar o cancelar cuentas y operaciones ante indicios de abuso, fraude, incumplimiento o riesgo para la plataforma.</p>

<h3>9. Propiedad intelectual</h3>
<p>La marca TukiPass, el diseño, software, interfaces, textos, bases de datos y demás elementos de la plataforma pertenecen a sus titulares o licenciantes. Los organizadores conservan responsabilidad sobre los contenidos que publican y declaran contar con derechos para usarlos.</p>

<h3>10. Limitación de responsabilidad</h3>
<p>TukiPass no será responsable por incumplimientos atribuibles a organizadores, asistentes, proveedores de pago, terceros técnicos, fuerza mayor, fallas externas, cambios de programación o condiciones del evento ajenas a la plataforma.</p>

<h3>11. Soporte</h3>
<p>Para consultas operativas podés escribir a [EMAIL_SOPORTE]. Para consultas sobre privacidad o datos personales, escribí a [EMAIL_PRIVACIDAD].</p>

<h3>12. Cambios en los términos</h3>
<p>Podemos actualizar estos términos para reflejar cambios del servicio, requisitos legales o mejoras operativas. La versión vigente será la publicada en [DOMINIO].</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'eliminacion-de-datos' => [
        'title' => 'Eliminación de datos',
        'meta_keywords' => 'TukiPass, eliminación de datos, borrar cuenta, Facebook Login, Google Login, privacidad',
        'meta_description' => 'Instrucciones para solicitar la eliminación de datos personales en TukiPass, incluyendo cuentas creadas con Facebook Login o Google Login.',
        'content' => <<<'HTML'
<h2>Eliminación de datos</h2>
<p><strong>Última actualización:</strong> [FECHA_ACTUALIZACION]</p>
<p>Esta página explica cómo solicitar la eliminación de datos personales asociados a una cuenta de TukiPass. También sirve como URL pública de instrucciones para usuarios que accedieron mediante Facebook Login o Google Login.</p>

<h3>1. Cómo pedir la eliminación</h3>
<p>Para solicitar la eliminación de tus datos, enviá un email a [EMAIL_PRIVACIDAD] con el asunto "Solicitud de eliminación de datos".</p>
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
<p>Procesaremos la solicitud en un plazo máximo de 120 días, salvo que la ley aplicable exija o permita un plazo distinto, o que necesitemos información adicional para verificar identidad o alcance de la solicitud.</p>

<h3>6. Facebook Login y Google Login</h3>
<p>Si usaste Facebook Login o Google Login, la eliminación en TukiPass alcanza los datos que TukiPass recibió y conserva, como nombre, email, identificador del proveedor y foto de perfil si aplica. Para eliminar datos mantenidos por Facebook o Google, deberás gestionar la solicitud directamente desde esas plataformas.</p>

<h3>7. Sin callback automático</h3>
<p>Esta página contiene instrucciones públicas para solicitar eliminación de datos. TukiPass no informa en esta página un callback automático de Meta ni un botón de borrado dentro del dashboard.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'politica-de-reembolsos' => [
        'title' => 'Política de reembolsos',
        'meta_keywords' => 'TukiPass, política de reembolsos, eventos cancelados, eventos reprogramados, entradas',
        'meta_description' => 'Conocé cómo se gestionan los reembolsos en TukiPass ante eventos cancelados, reprogramados, políticas del organizador y pagos externos.',
        'content' => <<<'HTML'
<h2>Política de reembolsos</h2>
<p><strong>Última actualización:</strong> [FECHA_ACTUALIZACION]</p>
<p>Esta Política de reembolsos explica criterios generales aplicables a compras realizadas mediante TukiPass. Las condiciones específicas pueden variar según el evento, el organizador, el medio de pago y la normativa aplicable.</p>

<h3>1. Rol de TukiPass</h3>
<p>TukiPass funciona como plataforma tecnológica e intermediario para la venta y gestión de entradas. En eventos publicados por terceros, el organizador es responsable principal por la realización del evento y por definir la política comercial aplicable.</p>

<h3>2. Cancelaciones</h3>
<p>Si un evento es cancelado definitivamente, el organizador deberá informar el procedimiento de devolución aplicable. TukiPass podrá colaborar en la gestión del reembolso según los fondos disponibles, el gateway de pago utilizado y las condiciones informadas para el evento.</p>

<h3>3. Reprogramaciones</h3>
<p>Si un evento es reprogramado, las entradas pueden seguir siendo válidas para la nueva fecha. Si el organizador habilita devoluciones por reprogramación, se informará el plazo y mecanismo para solicitarlas.</p>

<h3>4. Política del organizador</h3>
<p>Antes de comprar, la persona compradora debe revisar la descripción del evento, condiciones de ingreso, política del organizador y restricciones aplicables. Algunas entradas pueden no admitir devolución salvo cancelación, reprogramación u obligación legal.</p>

<h3>5. Comisiones y costos</h3>
<p>Las comisiones de servicio, costos de procesamiento, cargos administrativos o costos cobrados por gateways externos pueden no ser reembolsables, salvo que la normativa aplicable indique lo contrario o que el organizador asuma esos costos.</p>

<h3>6. Cómo solicitar un reembolso</h3>
<p>Para solicitar un reembolso, escribí a [EMAIL_SOPORTE] indicando nombre, email de compra, evento, número de orden o comprobante, motivo de la solicitud y cualquier documentación relevante.</p>

<h3>7. Plazos</h3>
<p>Los plazos de análisis, aprobación y acreditación pueden variar según el organizador, el medio de pago, el banco emisor y el gateway utilizado. TukiPass no controla los tiempos internos de acreditación de terceros.</p>

<h3>8. Casos donde puede no aplicar reembolso</h3>
<p>Puede no corresponder reembolso en casos de inasistencia, error en la compra imputable al usuario, incumplimiento de condiciones de ingreso, entradas usadas o validadas, compras fuera de plazo de reclamo, fraude o supuestos expresamente informados como no reembolsables.</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
      'politica-de-cookies' => [
        'title' => 'Política de cookies',
        'meta_keywords' => 'TukiPass, política de cookies, Meta Pixel, cookies analíticas, cookies necesarias',
        'meta_description' => 'Conocé cómo TukiPass usa cookies necesarias, analíticas y de marketing, incluyendo Meta Pixel configurado por organizadores.',
        'content' => <<<'HTML'
<h2>Política de cookies</h2>
<p><strong>Última actualización:</strong> [FECHA_ACTUALIZACION]</p>
<p>Esta Política de cookies explica cómo TukiPass usa cookies y tecnologías similares en [DOMINIO].</p>

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
<p>Podemos actualizar esta política para reflejar cambios técnicos, legales o funcionales. La versión vigente será la publicada en [DOMINIO].</p>

<p><strong>Este documento debe ser revisado por asesoría legal antes de su publicación definitiva.</strong></p>
HTML
      ],
    ];

    DB::transaction(function () use ($pages) {
      foreach ($pages as $slug => $data) {
        $updated = DB::table('page_contents')
          ->whereRaw('BINARY slug = ?', [$slug])
          ->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'meta_keywords' => $data['meta_keywords'],
            'meta_description' => $data['meta_description'],
            'updated_at' => now(),
          ]);

        if ($updated !== 1) {
          throw new RuntimeException("Expected to update one legal page for slug {$slug}, updated {$updated}.");
        }
      }
    });
  }
}
