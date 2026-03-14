<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Event\Booking;

class EventBadgeService
{
    /**
     * Determina qué badge mostrar para un evento.
     * Devuelve solo UN badge (el de mayor prioridad), o null si no corresponde ninguno.
     *
     * Prioridad (de mayor a menor):
     * 1. Manual: imperdible / destacado (admin)
     * 2. Últimas entradas (stock ≤ 15%)
     * 3. Furor (≥ 20 ventas en últimas 48h)
     * 4. Últimas horas (evento en próximas 48h)
     * 5. Trending (≥ 100 visitas totales)
     * 6. Nuevo (creado hace menos de 72h)
     */
    public static function getBadge($event): ?array
    {
        // --- BADGES MANUALES (máxima prioridad) ---
        if ($event->manual_badge === 'imperdible') {
            return [
                'label' => 'Imperdible',
                'icon'  => '🎪',
                'class' => 'ev-badge--imperdible',
            ];
        }

        if ($event->manual_badge === 'destacado') {
            return [
                'label' => 'Destacado',
                'icon'  => '⭐',
                'class' => 'ev-badge--destacado',
            ];
        }

        // --- BADGES AUTOMÁTICOS ---

        // Últimas entradas: stock ≤ 15%
        $stockPercent = self::getStockPercent($event);
        if ($stockPercent !== null && $stockPercent <= 15 && $stockPercent > 0) {
            return [
                'label' => 'Últimas entradas',
                'icon'  => '⚡',
                'class' => 'ev-badge--agota',
            ];
        }

        // Furor: ≥ 20 ventas en las últimas 48h
        $recentSales = self::getRecentSales($event);
        if ($recentSales >= 20) {
            return [
                'label' => 'Furor',
                'icon'  => '🔥',
                'class' => 'ev-badge--furor',
            ];
        }

        // Últimas horas: evento en las próximas 48h
        $eventDate = self::getEventDate($event);
        if ($eventDate && $eventDate->isBetween(now(), now()->addHours(48))) {
            return [
                'label' => 'Últimas horas',
                'icon'  => '🎯',
                'class' => 'ev-badge--ultimas',
            ];
        }

        // Trending: ≥ 100 visitas totales
        $views = $event->views_last_24h ?? $event->views_count ?? 0;
        if ($views >= 100) {
            return [
                'label' => 'Trending',
                'icon'  => '📈',
                'class' => 'ev-badge--trending',
            ];
        }

        // Nuevo: creado hace menos de 72h
        if (!empty($event->created_at) && Carbon::parse($event->created_at)->isAfter(now()->subHours(72))) {
            return [
                'label' => 'Nuevo',
                'icon'  => '🆕',
                'class' => 'ev-badge--nuevo',
            ];
        }

        return null;
    }

    /**
     * Porcentaje de stock restante para tickets con cupo limitado.
     * Usa la tabla tickets: ticket_available_type = 'limited' → ticket_available = cupo total.
     * Compara contra bookings pagados.
     */
    private static function getStockPercent($event): ?float
    {
        $limitedTickets = \App\Models\Event\Ticket::where('event_id', $event->id)
            ->where('ticket_available_type', 'limited')
            ->get();

        if ($limitedTickets->isEmpty()) {
            return null;
        }

        $total = $limitedTickets->sum('ticket_available');
        if (!$total) {
            return null;
        }

        $sold = Booking::where('event_id', $event->id)
            ->where('paymentStatus', 'paid')
            ->sum(\DB::raw('CAST(quantity AS UNSIGNED)'));

        $remaining = max(0, $total - $sold);

        return ($remaining / $total) * 100;
    }

    /**
     * Ventas de entradas en las últimas 48h (bookings pagados).
     */
    private static function getRecentSales($event): int
    {
        return (int) Booking::where('event_id', $event->id)
            ->where('paymentStatus', 'paid')
            ->where('created_at', '>=', now()->subHours(48))
            ->sum(\DB::raw('CAST(quantity AS UNSIGNED)'));
    }

    /**
     * Fecha del evento como Carbon (usa start_date del evento o primer date de fechas múltiples).
     */
    private static function getEventDate($event): ?Carbon
    {
        $date = $event->start_date ?? null;

        if (!$date && isset($event->date_type) && $event->date_type === 'multiple') {
            $firstDate = \App\Models\Event\EventDates::where('event_id', $event->id)
                ->orderBy('start_date', 'asc')
                ->first();
            $date = $firstDate->start_date ?? null;
        }

        if (!$date) {
            return null;
        }

        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }
}
