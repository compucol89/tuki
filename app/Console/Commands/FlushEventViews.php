<?php

namespace App\Console\Commands;

use App\Services\EventViewTracker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FlushEventViews extends Command
{
    protected $signature = 'events:flush-views';
    protected $description = 'Vuelca al contador de eventos las visitas bufferizadas';

    public function handle()
    {
        $tmp = EventViewTracker::take();

        if ($tmp === null) {
            return self::SUCCESS;
        }

        $counts = EventViewTracker::aggregate($tmp);

        foreach ($counts as $eventId => $views) {
            DB::table('events')
                ->where('id', $eventId)
                ->update([
                    'views_count' => DB::raw('views_count + ' . (int) $views),
                    'views_last_24h' => DB::raw('views_last_24h + ' . (int) $views),
                ]);
        }

        @unlink($tmp);

        $this->info(count($counts) . ' eventos actualizados (' . array_sum($counts) . ' visitas).');

        return self::SUCCESS;
    }
}
