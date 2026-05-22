<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update reset_password template to branded HTML with corporate design.
     */
    public function up(): void
    {
        DB::table('mail_templates')
            ->where('id', 5)
            ->where('mail_type', 'reset_password')
            ->update([
                'mail_subject' => 'Restablecé tu contraseña',
                'mail_body' => '<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Restablecé tu contraseña</title>
  <style>
    body {
      font-family: \'Inter\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 20px;
      color: #333;
      -webkit-font-smoothing: antialiased;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .header {
      background: linear-gradient(135deg, #1e2532 0%, #2d3748 100%);
      color: #ffffff;
      padding: 32px 32px 24px;
      text-align: center;
    }
    .header h1 {
      margin: 0 0 8px;
      font-size: 22px;
      font-weight: 700;
    }
    .header p {
      margin: 0;
      font-size: 14px;
      opacity: 0.85;
    }
    .badge {
      display: inline-block;
      background: #F97316;
      color: #ffffff;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      padding: 5px 14px;
      border-radius: 20px;
      margin-top: 14px;
    }
    .body {
      padding: 32px;
    }
    .body p {
      font-size: 15px;
      line-height: 1.6;
      color: #555;
      margin: 0 0 20px;
    }
    .cta-button {
      display: inline-block;
      background: #F97316;
      color: #ffffff;
      text-decoration: none;
      font-size: 15px;
      font-weight: 600;
      padding: 14px 36px;
      border-radius: 8px;
      margin: 8px 0;
      text-align: center;
    }
    .fallback-link {
      background: #f9fafb;
      border-radius: 8px;
      padding: 14px 18px;
      margin: 16px 0;
      font-size: 13px;
      color: #666;
      word-break: break-all;
    }
    .fallback-link a {
      color: #F97316;
    }
    .divider {
      border: none;
      border-top: 1px solid #eee;
      margin: 0;
    }
    .footer {
      background: #f9fafb;
      padding: 28px 32px;
      text-align: center;
      font-size: 12px;
      color: #888;
      border-top: 1px solid #eee;
    }
    .footer .brand {
      font-size: 14px;
      font-weight: 700;
      color: #1e2532;
      margin-bottom: 4px;
    }
    .footer p {
      margin: 4px 0;
    }
    .disclaimer {
      background: #fff7ed;
      border-left: 4px solid #F97316;
      padding: 14px 18px;
      margin: 24px 0 0;
      font-size: 12px;
      color: #7c2d12;
      border-radius: 0 6px 6px 0;
      line-height: 1.5;
    }
    @media (max-width: 480px) {
      body { padding: 10px; }
      .header { padding: 24px 20px 20px; }
      .header h1 { font-size: 18px; }
      .body { padding: 20px; }
      .footer { padding: 20px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>{website_title}</h1>
      <p>Restablecé tu contraseña</p>
      <div class="badge">Recuperación de cuenta</div>
    </div>
    <div class="body">
      <p>Hola <strong>{customer_name}</strong>,</p>
      <p>
        Recibimos una solicitud para restablecer la contraseña de tu cuenta en <strong>{website_title}</strong>.
        Si no hiciste esta solicitud, podés ignorar este mensaje.
      </p>
      <p style="margin-bottom: 28px;">
        Para crear una nueva contraseña, hacé clic en el botón de acá abajo:
      </p>
      <div style="text-align: center; margin: 28px 0;">
        <a href="{password_reset_link}" class="cta-button">Restablecer contraseña</a>
      </div>
      <div class="fallback-link">
        <p style="margin: 0 0 6px; font-size: 13px; color: #888;">
          Si el botón no funciona, copiá y pegá este enlace en tu navegador:
        </p>
        <a href="{password_reset_link}" style="font-size: 13px; color: #F97316; word-break: break-all;">{password_reset_link}</a>
      </div>
      <div class="disclaimer">
        <strong>Importante</strong><br>
        Tukipass no organiza ni produce los eventos publicados, salvo indicación expresa. Tukipass presta un servicio tecnológico de publicación, gestión y venta online de entradas. Si no solicitaste este cambio, ignorá este mensaje.
      </div>
    </div>
    <hr class="divider">
    <div class="footer">
      <p class="brand">{website_title}</p>
      <p>Entradas y Tickets Online para Eventos en Argentina</p>
      <p style="margin-top: 8px; font-size: 11px; color: #666;">
        <strong>TAYRONA GROUP SAS</strong> · CUIT 30-71885087-4
      </p>
      <p style="margin-top: 8px; font-size: 11px; color: #aaa;">
        Operador comercial de la plataforma {website_title}.<br>
        Este email fue generado automáticamente. No respondas a esta dirección.
      </p>
    </div>
  </div>
</body>
</html>',
            ]);
    }

    /**
     * Rollback to the original plain English template.
     */
    public function down(): void
    {
        DB::table('mail_templates')
            ->where('id', 5)
            ->where('mail_type', 'reset_password')
            ->update([
                'mail_subject' => 'Reset Your Password',
                'mail_body' => '<p>Hi <b>{customer_name}</b>,</p><p>We received a request to reset your password. If you did not make this request, you can ignore this email.</p><p>To reset your password, {password_reset_link}.</p><p>Thank you.<br />{website_title}</p><p><br /></p>',
            ]);
    }
};
