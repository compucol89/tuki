<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;

class ResetDailyViews extends Command
{
    protected $signature = 'events:reset-daily-views';
    protected $description = 'Resetea el contador de visitas de las últimas 24h de todos los eventos';

    public function handle()
    {
        Event::query()->update([
            'views_last_24h'   => 0,
            'views_last_reset' => now(),
        ]);

        $this->info('Visitas diarias reseteadas.');
    }
}
