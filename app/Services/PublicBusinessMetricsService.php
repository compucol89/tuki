<?php

namespace App\Services;

use App\Models\Event\Booking;
use App\Models\Event\EventContent;
use App\Models\Organizer;
use App\Support\DemoEventExclusion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Métricas públicas del frontend (/sobre-nosotros). Reemplaza a config('about_metrics')
 * (valores placeholder) — F-002 / F-011.
 *
 * Definiciones documentadas (content-integrity-policy):
 *
 * METRIC events_published_live
 *   source: event_contents + events
 *   definition: eventos con status = 1, end_date_time >= ahora,
 *     excluidos DemoEventExclusion::EVENT_IDS, idioma activo si se indica
 *   owner: Business / Product · freshness: cache 3600 s
 *
 * METRIC tickets_sold_last_12_months
 *   source: bookings
 *   definition: SUM(quantity) con paymentStatus IN ('completed','free')
 *     (completed = pago; free = evento gratuito cerrado) y created_at dentro
 *     de los últimos 12 meses. Excluye emails de prueba (@test., @example.,
 *     user+test@). Ventana móvil, hora del servidor.
 *
 * METRIC organizers_active
 *   source: organizers + organizer_infos
 *   definition: MISMA política que el directorio público listable()
 *     (email verificado + foto + portada + red social + perfil completo).
 *
 * METRIC weekend_events_avg
 *   source: events
 *   definition: eventos status=1 (no demo) con start_date en sábado o domingo
 *     dentro de las últimas 52 semanas / 52 (promedio semanal de eventos de
 *     fin de semana). 0 si no hay historial en la ventana.
 */
class PublicBusinessMetricsService
{
  public const CACHE_KEY = 'public_business_metrics';

  public const CACHE_TTL_SECONDS = 3600;

  public const TICKET_PAYMENT_STATUSES = ['completed', 'free'];

  public function forAboutPage(?int $languageId = null): array
  {
    return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, function () use ($languageId) {
      return [
        'enabled' => true,
        'stats' => [
          $this->stat('about_metrics_label_events_bsas', $this->eventsPublishedLive($languageId)),
          $this->stat('about_metrics_label_tickets_year', $this->ticketsSoldLast12Months()),
          $this->stat('about_metrics_label_organizers', $this->organizersActive()),
          $this->stat('about_metrics_label_weekend_avg', $this->weekendEventsAvg($languageId)),
        ],
      ];
    });
  }

  public function eventsPublishedLive(?int $languageId = null): int
  {
    return (int) EventContent::query()
      ->join('events', 'events.id', '=', 'event_contents.event_id')
      ->where('events.status', 1)
      ->where('events.end_date_time', '>=', Carbon::now())
      ->whereNotIn(DemoEventExclusion::eventIdColumn(), DemoEventExclusion::EVENT_IDS)
      ->when($languageId, fn ($q) => $q->where('event_contents.language_id', $languageId))
      ->distinct()
      ->count('event_contents.event_id');
  }

  public function ticketsSoldLast12Months(): int
  {
    return (int) Booking::query()
      ->whereIn('paymentStatus', self::TICKET_PAYMENT_STATUSES)
      ->where('created_at', '>=', Carbon::now()->subMonths(12))
      ->where(function ($q) {
        $q->whereNull('email')
          ->orWhere(function ($email) {
            $email->where('email', 'not like', '%@test.%')
              ->where('email', 'not like', '%@example.%')
              ->where('email', 'not like', '%test%@%');
          });
      })
      ->sum(DB::raw('CAST(quantity AS UNSIGNED)'));
  }

  public function organizersActive(): int
  {
    return Organizer::query()->listable()->count();
  }

  public function weekendEventsAvg(?int $languageId = null): int
  {
    $windowStart = Carbon::now()->subWeeks(52)->startOfDay();

    $weekendEvents = EventContent::query()
      ->join('events', 'events.id', '=', 'event_contents.event_id')
      ->where('events.status', 1)
      ->whereNotIn(DemoEventExclusion::eventIdColumn(), DemoEventExclusion::EVENT_IDS)
      ->where('events.start_date', '>=', $windowStart->toDateString())
      ->where('events.start_date', '<=', Carbon::now()->toDateString())
      ->when($languageId, fn ($q) => $q->where('event_contents.language_id', $languageId))
      ->select('events.id as event_id', 'events.start_date')
      ->distinct()
      ->get()
      ->filter(fn ($row) => in_array(Carbon::parse($row->start_date)->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]))
      ->count();

    return (int) round($weekendEvents / 52);
  }

  public static function forgetCache(): void
  {
    Cache::forget(self::CACHE_KEY);
  }

  private function stat(string $labelKey, int $value): array
  {
    return [
      'value' => number_format($value, 0, ',', '.'),
      'label_key' => $labelKey,
    ];
  }
}
