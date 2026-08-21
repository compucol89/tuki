<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

/**
 * F-006 — corrige eventos con fechas corruptas (end_date_time anterior a
 * start_date, p.ej. el evento 123 del entorno de prueba).
 *
 * Uso:
 *   php artisan events:fix-corrupted-dates --dry-run
 *   php artisan events:fix-corrupted-dates
 */
class FixCorruptedEventDates extends Command
{
  protected $signature = 'events:fix-corrupted-dates {--dry-run : Reportar sin modificar}';

  protected $description = 'Corrige eventos cuyo end_date_time es anterior a start_date';

  public function handle(): int
  {
    $events = Event::whereNotNull('start_date')
      ->whereNotNull('end_date_time')
      ->whereColumn('events.end_date_time', '<', 'events.start_date')
      ->get();

    if ($events->isEmpty()) {
      $this->info('No hay eventos con fechas corruptas.');

      return self::SUCCESS;
    }

    $rows = $events->map(fn ($e) => [
      $e->id,
      (string) $e->start_date,
      (string) $e->end_date_time,
    ]);

    $this->table(['id', 'start_date', 'end_date_time (corrupto)'], $rows);

    if ($this->option('dry-run')) {
      $this->warn('Modo dry-run: no se modificó nada.');

      return self::SUCCESS;
    }

    $count = 0;

    foreach ($events as $event) {
      $fixed = Carbon::parse($event->start_date)->endOfDay();
      $event->end_date_time = $fixed;
      $event->save();
      $count++;
    }

    $this->info(sprintf('Corregidos %d eventos: end_date_time = fin del día de start_date.', $count));

    return self::SUCCESS;
  }
}
