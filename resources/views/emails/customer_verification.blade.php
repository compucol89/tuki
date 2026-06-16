<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verificá tu cuenta</title>
</head>
<body style="margin:0; padding:0; background-color:#f3f4f6; color:#333333; font-family:Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif; -webkit-font-smoothing:antialiased;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%; background-color:#f3f4f6; margin:0; padding:24px 12px;">
    <tr>
      <td align="center">
        <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="width:100%; max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.08);">
          <tr>
            <td align="center" style="background-color:#1e2532; padding:32px 28px 24px; color:#ffffff;">
              <img src="{{ asset('assets/admin/img/logo-white.png') }}" alt="TukiPass" width="140" style="display:block; width:140px; max-width:140px; height:auto; margin:0 auto 16px;">
              <h1 style="margin:0 0 8px; font-size:22px; line-height:1.25; font-weight:700; color:#ffffff;">Verificá tu cuenta</h1>
              <p style="margin:0; font-size:14px; line-height:1.5; color:#e5e7eb;">Confirmá tu correo para empezar a usar {{ $websiteTitle }}</p>
              <div style="display:inline-block; margin-top:14px; padding:5px 14px; border-radius:20px; background-color:#F97316; color:#ffffff; font-size:11px; line-height:1.4; font-weight:700; letter-spacing:0.8px; text-transform:uppercase;">Correo pendiente</div>
            </td>
          </tr>
          <tr>
            <td style="padding:32px 32px 28px;">
              <p style="margin:0 0 18px; font-size:15px; line-height:1.6; color:#555555;">Hola <strong style="color:#1e2532;">{{ $username }}</strong>,</p>
              <p style="margin:0 0 20px; font-size:15px; line-height:1.6; color:#555555;">Gracias por registrarte en <strong style="color:#1e2532;">{{ $websiteTitle }}</strong>. Para activar tu cuenta, confirmá tu dirección de correo electrónico.</p>
              <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0;">
                <tr>
                  <td align="center">
                    <a href="{{ $verificationLink }}" style="display:inline-block; background-color:#F97316; color:#ffffff; text-decoration:none; font-size:15px; line-height:1.3; font-weight:700; padding:14px 34px; border-radius:8px; text-align:center;">Verificar mi correo</a>
                  </td>
                </tr>
              </table>
              <div style="background-color:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:14px 18px; margin:16px 0 0;">
                <p style="margin:0 0 6px; font-size:13px; line-height:1.5; color:#777777;">Si el botón no funciona, copiá y pegá este enlace en tu navegador:</p>
                <a href="{{ $verificationLink }}" style="font-size:13px; line-height:1.5; color:#F97316; text-decoration:none; word-break:break-all;">{{ $verificationLink }}</a>
              </div>
              <div style="background-color:#fff7ed; border-left:4px solid #F97316; border-radius:0 6px 6px 0; padding:14px 18px; margin:24px 0 0; color:#7c2d12;">
                <strong style="display:block; margin:0 0 4px; font-size:13px; line-height:1.4; color:#7c2d12;">Importante</strong>
                <p style="margin:0; font-size:12px; line-height:1.5; color:#7c2d12;">Tukipass no organiza ni produce los eventos publicados, salvo indicación expresa. Tukipass presta un servicio tecnológico de publicación, gestión y venta online de entradas. La realización, calidad, accesos, horarios, cambios, cancelaciones, reembolsos y condiciones particulares del evento son responsabilidad exclusiva del organizador. Si no creaste esta cuenta, ignorá este mensaje.</p>
              </div>
            </td>
          </tr>
          <tr>
            <td align="center" style="background-color:#f9fafb; border-top:1px solid #eeeeee; padding:28px 32px; color:#888888;">
              <p style="margin:0 0 4px; font-size:14px; line-height:1.5; font-weight:700; color:#1e2532;">{{ $websiteTitle }}</p>
              <p style="margin:4px 0; font-size:12px; line-height:1.5; color:#888888;">Entradas online para eventos en Argentina</p>
              <p style="margin:8px 0 0; font-size:11px; line-height:1.5; color:#666666;"><strong>TAYRONA GROUP SAS</strong> &mdash; CUIT 30-71885087-4</p>
              <p style="margin:8px 0 0; font-size:11px; line-height:1.5; color:#aaaaaa;">Operador comercial de la plataforma Tukipass.<br>Copyright &copy; 2026 Tukipass. Todos los derechos reservados.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
