<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update balance_subtract template to branded HTML with corporate design.
     */
    public function up(): void
    {
        DB::table('mail_templates')
            ->where('id', 16)
            ->where('mail_type', 'balance_subtract')
            ->update([
                'mail_subject' => 'Saldo debitado',
                'mail_body' => '<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Saldo debitado</title>
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
    .details-box {
      background: #f9fafb;
      border-radius: 8px;
      padding: 18px 20px;
      margin: 16px 0 24px;
    }
    .details-box p {
      margin: 0 0 8px;
      font-size: 14px;
      color: #444;
    }
    .details-box p:last-child {
      margin-bottom: 0;
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
      <p>Saldo debitado</p>
      <div class="badge">Movimiento de cuenta</div>
    </div>
    <div class="body">
      <p>Hola <strong>{username}</strong>,</p>
      <p>
        Te informamos que se debitó saldo de tu cuenta en <strong>{website_title}</strong>.
      </p>
      <div class="details-box">
        <p><strong>Monto debitado:</strong> {amount}</p>
        <p><strong>Saldo actual:</strong> {current_balance}</p>
        <p><strong>ID de transacción:</strong> {transaction_id}</p>
      </div>
      <p style="margin-bottom: 0;">
        Si tenés alguna duda sobre este movimiento, contactanos desde tu panel.
      </p>
      <div class="disclaimer">
        <strong>Importante</strong><br>
        Tukipass no organiza ni produce los eventos publicados, salvo indicación expresa. Tukipass presta un servicio tecnológico de publicación, gestión y venta online de entradas. La realización, calidad, accesos, horarios, cambios, cancelaciones, reembolsos y condiciones particulares del evento son responsabilidad exclusiva del organizador.
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
            ->where('id', 16)
            ->where('mail_type', 'balance_subtract')
            ->update([
                'mail_subject' => 'Balance Subtract',
                'mail_body' => '<p>Hi {username}</p><p>{amount} subtract from your account.</p><p>Your current balance is {current_balance}.</p><p>The transaction id is {transaction_id}.<br /></p><p><br /></p><p>Best Regards.<br />{website_title}</p>',
            ]);
    }
};
