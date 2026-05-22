<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update product_shipping template to branded HTML with corporate design.
     */
    public function up(): void
    {
        DB::table('mail_templates')
            ->where('id', 17)
            ->where('mail_type', 'product_shipping')
            ->update([
                'mail_subject' => 'Estado de envío actualizado',
                'mail_body' => '<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Estado de envío actualizado</title>
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
      <p>Estado de envío actualizado</p>
      <div class="badge">Seguimiento de pedido</div>
    </div>
    <div class="body">
      <p>Hola <strong>{customer_name}</strong>,</p>
      <p>
        Te informamos que el estado de envío de tu pedido en <strong>{website_title}</strong> fue actualizado.
      </p>
      <div class="details-box">
        <p><strong>Número de pedido:</strong> #{order_id}</p>
        <p><strong>Estado actual:</strong> {status}</p>
      </div>
      <p style="margin-bottom: 0;">
        Si tenés alguna duda sobre tu envío, contactanos desde tu panel de cliente.
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
            ->where('id', 17)
            ->where('mail_type', 'product_shipping')
            ->update([
                'mail_subject' => 'Product Shipping Status',
                'mail_body' => '<p>Hi <span style="font-weight:600;">{customer_name}</span>,</p><p>Your order shipping status is {status}.</p><p>Order Id: #{order_id}</p><p>Best regards.<br />{website_title}</p>',
            ]);
    }
};
