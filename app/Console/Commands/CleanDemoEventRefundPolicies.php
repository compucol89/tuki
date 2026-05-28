<?php

namespace App\Console\Commands;

use App\Models\Event\EventContent;
use App\Support\DemoEventExclusion;
use App\Support\EventRefundPolicy;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CleanDemoEventRefundPolicies extends Command
{
  protected $signature = 'events:clean-demo-refund-policies {--dry-run : Solo listar filas afectadas}';

  protected $description = 'Vacía políticas de reembolso demo/placeholder en event_contents (eventos demo y texto inválido)';

  public function handle(): int
  {
    $query = EventContent::query()
      ->where(function ($builder) {
        $builder
          ->whereIn('event_id', DemoEventExclusion::EVENT_IDS)
          ->orWhereIn('slug', DemoEventExclusion::EVENT_SLUGS);
      })
      ->whereNotNull('refund_policy')
      ->where('refund_policy', '!=', '');

    $invalidQuery = EventContent::query()
      ->whereNotNull('refund_policy')
      ->where('refund_policy', '!=', '')
      ->whereNotIn('event_id', DemoEventExclusion::EVENT_IDS);

    $invalidIds = $invalidQuery
      ->get(['id', 'event_id', 'slug', 'refund_policy'])
      ->filter(fn ($row) => !EventRefundPolicy::isValid($row->refund_policy))
      ->pluck('id');

    $demoRows = $query->get(['id', 'event_id', 'slug', 'refund_policy']);
    $invalidRows = EventContent::query()
      ->whereIn('id', $invalidIds)
      ->get(['id', 'event_id', 'slug', 'refund_policy']);

    $all = $demoRows->merge($invalidRows)->unique('id');

    if ($all->isEmpty()) {
      $this->info('No hay políticas de reembolso demo para limpiar.');

      return self::SUCCESS;
    }

    $this->table(
      ['id', 'event_id', 'slug', 'preview'],
      $all->map(fn ($row) => [
        $row->id,
        $row->event_id,
        $row->slug,
        Str::limit($row->refund_policy, 60),
      ])->all()
    );

    if ($this->option('dry-run')) {
      $this->info('Dry-run: no se modificó la base de datos.');

      return self::SUCCESS;
    }

    $canonical = EventRefundPolicy::canonicalPlainText();

    $updated = EventContent::query()
      ->whereIn('id', $all->pluck('id'))
      ->update(['refund_policy' => $canonical]);

    $this->info("Políticas de reembolso reemplazadas por texto canónico: {$updated}");

    return self::SUCCESS;
  }
}
