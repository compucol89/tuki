<?php

namespace App\Console\Commands;

use App\Models\PaymentIncident;
use Illuminate\Console\Command;

class ReviewPaymentIncidents extends Command
{
  protected $signature = 'payments:incidents {--status=manual_review : Estado a listar} {--limit=50 : Máximo de filas}';

  protected $description = 'Lista incidentes de pago que requieren revisión o reembolso manual';

  public function handle(): int
  {
    $status = (string) $this->option('status');
    $limit = max(1, (int) $this->option('limit'));

    $incidents = PaymentIncident::query()
      ->when($status !== '', fn ($query) => $query->where('status', $status))
      ->orderByDesc('created_at')
      ->limit($limit)
      ->get();

    if ($incidents->isEmpty()) {
      $this->info('Sin incidentes para el estado indicado.');

      return self::SUCCESS;
    }

    $this->table(
      ['ID', 'Gateway', 'Payment', 'Booking', 'Monto', 'Motivo', 'Estado', 'Creado'],
      $incidents->map(fn (PaymentIncident $incident) => [
        $incident->id,
        $incident->gateway,
        $incident->payment_id,
        $incident->booking_id ?? '-',
        trim(($incident->currency ?? '') . ' ' . ($incident->amount ?? '')),
        $incident->reason,
        $incident->status,
        $incident->created_at?->format('Y-m-d H:i'),
      ])->all()
    );

    return self::SUCCESS;
  }
}
