<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Support\DemoEventExclusion;
use Illuminate\Console\Command;

class UnpublishDemoEvents extends Command
{
  protected $signature = 'events:unpublish-demo {--dry-run : Solo mostrar qué eventos se despublicarían}';

  protected $description = 'Despublica eventos demo/placeholder (status=0) por ID conocido';

  public function handle(): int
  {
    $events = Event::query()
      ->whereIn('id', DemoEventExclusion::EVENT_IDS)
      ->get(['id', 'status']);

    if ($events->isEmpty()) {
      $this->warn('No se encontraron eventos demo con los IDs configurados.');

      return self::SUCCESS;
    }

    $this->table(['id', 'status'], $events->map(fn ($e) => [$e->id, $e->status])->all());

    if ($this->option('dry-run')) {
      $this->info('Dry-run: no se modificó la base de datos.');

      return self::SUCCESS;
    }

    $updated = Event::query()
      ->whereIn('id', DemoEventExclusion::EVENT_IDS)
      ->where('status', 1)
      ->update(['status' => 0]);

    $this->info("Eventos despublicados: {$updated}");

    return self::SUCCESS;
  }
}
