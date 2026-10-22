<?php

namespace App\Services;

use App\Models\PaymentIncident;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Compensa pagos aprobados que no pudieron convertirse en reserva.
 *
 * Para MercadoPago intenta un reembolso total idempotente. Si el reembolso
 * falla o el gateway no lo soporta, deja un incidente en manual_review.
 */
class PaymentCompensationService
{
  public function compensateMercadoPago(string $paymentId, string $token, array $context = [], string $reason = 'booking_creation_failed'): PaymentIncident
  {
    $incident = [
      'gateway' => 'mercadopago',
      'payment_id' => $paymentId,
      'booking_id' => $context['booking_id'] ?? null,
      'event_id' => $context['event_id'] ?? null,
      'amount' => $context['amount'] ?? null,
      'currency' => $context['currency'] ?? null,
      'reason' => $reason,
      'status' => 'manual_review',
      'context' => $context,
    ];

    if ($token === '') {
      Log::critical('PaymentCompensation: MercadoPago sin token; revisión manual', [
        'payment_id' => $paymentId,
        'reason' => $reason,
      ]);

      return PaymentIncident::create($incident);
    }

    try {
      $response = Http::withToken($token)
        ->withHeaders(['X-Idempotency-Key' => 'tukipass-refund-' . $paymentId])
        ->timeout(20)
        ->post('https://api.mercadopago.com/v1/payments/' . rawurlencode($paymentId) . '/refunds', []);

      $incident['status'] = $response->successful() ? 'refunded' : 'refund_failed';
      $incident['context']['refund_http_status'] = $response->status();
      $incident['context']['refund_response'] = $response->json();

      if (!$response->successful()) {
        Log::critical('PaymentCompensation: reembolso MercadoPago falló', [
          'payment_id' => $paymentId,
          'http_status' => $response->status(),
          'body' => $response->body(),
        ]);
      }
    } catch (\Throwable $e) {
      $incident['status'] = 'refund_failed';
      $incident['context']['refund_error'] = $e->getMessage();

      Log::critical('PaymentCompensation: excepción reembolsando MercadoPago', [
        'payment_id' => $paymentId,
        'error' => $e->getMessage(),
      ]);
    }

    return PaymentIncident::create($incident);
  }

  public function recordManualReview(string $gateway, string $paymentId, array $context = [], string $reason = 'manual_review'): PaymentIncident
  {
    Log::critical('PaymentCompensation: revisión manual requerida', [
      'gateway' => $gateway,
      'payment_id' => $paymentId,
      'reason' => $reason,
    ]);

    return PaymentIncident::create([
      'gateway' => $gateway,
      'payment_id' => $paymentId,
      'booking_id' => $context['booking_id'] ?? null,
      'event_id' => $context['event_id'] ?? null,
      'amount' => $context['amount'] ?? null,
      'currency' => $context['currency'] ?? null,
      'reason' => $reason,
      'status' => 'manual_review',
      'context' => $context,
    ]);
  }
}
