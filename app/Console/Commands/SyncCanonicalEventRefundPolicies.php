<?php

namespace App\Console\Commands;

use App\Models\Event\EventContent;
use App\Support\EventRefundPolicy;
use Illuminate\Console\Command;

class SyncCanonicalEventRefundPolicies extends Command
{
  protected $signature = 'events:sync-canonical-refund-policies {--dry-run : Solo contar filas a actualizar}';

  protected $description = 'Reemplaza refund_policy en todos los eventos por el texto legal fijo de Tukipass';

  public function handle(): int
  {
    $canonical = EventRefundPolicy::canonicalPlainText();

    $query = EventContent::query()
      ->where(function ($builder) use ($canonical) {
        $builder
          ->whereNull('refund_policy')
          ->orWhere('refund_policy', '!=', $canonical);
      });

    $count = $query->count();

    if ($count === 0) {
      $this->info('Todos los eventos ya tienen la política canónica.');

      return self::SUCCESS;
    }

    $this->info("Filas a actualizar: {$count}");

    if ($this->option('dry-run')) {
      return self::SUCCESS;
    }

    $updated = $query->update(['refund_policy' => $canonical]);

    $this->info("Políticas actualizadas: {$updated}");

    return self::SUCCESS;
  }
}
