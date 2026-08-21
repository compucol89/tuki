<?php

namespace Tests\Unit\Services;

use App\Services\PublicBusinessMetricsService;
use App\Support\DemoEventExclusion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PublicBusinessMetricsServiceTest extends TestCase
{
  private PublicBusinessMetricsService $service;

  protected function setUp(): void
  {
    parent::setUp();
    $this->service = new PublicBusinessMetricsService();
    $this->buildMinimalSchema();
    Cache::flush();

    $pdo = DB::connection()->getPdo();
    $pdo->sqliteCreateFunction('CHAR_LENGTH', fn ($value) => mb_strlen((string) $value), 1);
  }

  public function test_events_published_live_counts_only_active_future_events(): void
  {
    $this->insert('event_contents', ['id' => 1, 'event_id' => 1, 'language_id' => 8]);
    $this->insert('event_contents', ['id' => 2, 'event_id' => 2, 'language_id' => 8]);
    $this->insert('events', ['id' => 1, 'status' => 1, 'end_date_time' => now()->addDays(5)]);
    $this->insert('events', ['id' => 2, 'status' => 1, 'end_date_time' => now()->subDay()]);
    $this->insert('events', ['id' => 3, 'status' => 0, 'end_date_time' => now()->addDays(5)]);

    $this->assertSame(1, $this->service->eventsPublishedLive(8));
    $this->assertSame(1, $this->service->eventsPublishedLive(null));
  }

  public function test_events_published_live_excludes_demo_event_ids(): void
  {
    $demoId = DemoEventExclusion::EVENT_IDS[0];
    $this->insert('event_contents', ['id' => 10, 'event_id' => $demoId, 'language_id' => 8]);
    $this->insert('events', ['id' => $demoId, 'status' => 1, 'end_date_time' => now()->addDays(5)]);

    $this->assertSame(0, $this->service->eventsPublishedLive(8));
  }

  public function test_tickets_sold_last_12_months_sums_quantity_of_completed_or_free_bookings(): void
  {
    $this->insert('bookings', ['id' => 1, 'paymentStatus' => 'completed', 'quantity' => '2', 'email' => 'a@x.com', 'created_at' => now()->subMonths(2)]);
    $this->insert('bookings', ['id' => 2, 'paymentStatus' => 'free', 'quantity' => '3', 'email' => 'b@x.com', 'created_at' => now()->subMonths(2)]);
    $this->insert('bookings', ['id' => 3, 'paymentStatus' => 'pending', 'quantity' => '9', 'email' => 'c@x.com', 'created_at' => now()->subMonths(2)]);
    $this->insert('bookings', ['id' => 4, 'paymentStatus' => 'completed', 'quantity' => '5', 'email' => 'd@x.com', 'created_at' => now()->subMonths(13)]);

    $this->assertSame(5, $this->service->ticketsSoldLast12Months());
  }

  public function test_tickets_sold_last_12_months_excludes_test_emails(): void
  {
    $this->insert('bookings', ['id' => 10, 'paymentStatus' => 'completed', 'quantity' => '4', 'email' => 'qa@test.com', 'created_at' => now()->subMonths(2)]);
    $this->insert('bookings', ['id' => 11, 'paymentStatus' => 'completed', 'quantity' => '4', 'email' => 'user@example.com', 'created_at' => now()->subMonths(2)]);

    $this->assertSame(0, $this->service->ticketsSoldLast12Months());
  }

  public function test_organizers_active_uses_relaxed_listable_policy(): void
  {
    $this->insert('organizers', [
      'id' => 1, 'username' => 'organizador-real', 'email' => 'real@example.com', 'password' => 'x',
      'photo' => 'foto.jpg', 'cover_photo' => 'portada.jpg',
      'email_verified_at' => now(), 'website' => 'https://ejemplo.com',
    ]);
    $this->insert('organizer_infos', [
      'id' => 1, 'organizer_id' => 1, 'language_id' => 8,
      'name' => 'Organizador Real', 'country' => 'Argentina', 'city' => 'CABA',
      'details' => str_repeat('Detalle del perfil del organizador real. ', 5),
    ]);

    $this->insert('organizers', [
      'id' => 2, 'username' => 'sin-email', 'email' => 'x@example.com', 'password' => 'x',
      'photo' => 'foto.jpg', 'cover_photo' => 'portada.jpg', 'website' => 'https://ejemplo.com',
    ]);
    $this->insert('organizer_infos', [
      'id' => 2, 'organizer_id' => 2, 'language_id' => 8,
      'name' => 'Sin Email Verificado', 'country' => 'Argentina',
      'details' => str_repeat('Detalle del perfil. ', 10),
    ]);

    $this->insert('events', [
      'id' => 30, 'organizer_id' => 1, 'status' => 1,
      'start_date' => now()->subDays(60)->toDateString(),
      'end_date' => now()->subDays(30)->toDateString(),
      'end_date_time' => now()->subDays(30),
    ]);

    $this->assertSame(1, $this->service->organizersActive());
  }

  public function test_weekend_events_avg_counts_saturday_sunday_events_over_52_weeks(): void
  {
    $lastSaturday = Carbon::now()->startOfWeek()->subDays(2);
    $lastWednesday = Carbon::now()->startOfWeek()->subDays(5);

    foreach (range(1, 104) as $i) {
      $this->insert('event_contents', ['id' => 1000 + $i, 'event_id' => 1000 + $i, 'language_id' => 8]);
      $this->insert('events', ['id' => 1000 + $i, 'status' => 1, 'start_date' => $lastSaturday->toDateString(), 'end_date_time' => now()->addDays(5)]);
    }
    $this->insert('event_contents', ['id' => 2000, 'event_id' => 2000, 'language_id' => 8]);
    $this->insert('events', ['id' => 2000, 'status' => 1, 'start_date' => $lastWednesday->toDateString(), 'end_date_time' => now()->addDays(5)]);

    $this->assertSame(2, $this->service->weekendEventsAvg(8));
  }

  public function test_for_about_page_returns_four_stats_and_uses_cache(): void
  {
    $this->insert('bookings', ['id' => 30, 'paymentStatus' => 'completed', 'quantity' => '7', 'email' => 'c@x.com', 'created_at' => now()->subMonths(2)]);

    $result = $this->service->forAboutPage(8);
    $this->assertTrue($result['enabled']);
    $this->assertCount(4, $result['stats']);
    $this->assertSame('7', $result['stats'][1]['value']);
    $this->assertTrue(Cache::has(PublicBusinessMetricsService::CACHE_KEY));
  }

  private function insert(string $table, array $data): void
  {
    DB::table($table)->insert($data);
  }

  private function buildMinimalSchema(): void
  {
    Schema::dropIfExists('events');
    Schema::dropIfExists('event_contents');
    Schema::dropIfExists('bookings');
    Schema::dropIfExists('organizers');
    Schema::dropIfExists('organizer_infos');

    Schema::create('events', function ($t) {
      $t->id();
      $t->integer('organizer_id')->nullable();
      $t->string('status')->default('1');
      $t->date('start_date')->nullable();
      $t->date('end_date')->nullable();
      $t->dateTime('end_date_time')->nullable();
      $t->timestamps();
    });
    Schema::create('event_contents', function ($t) {
      $t->id();
      $t->integer('event_id')->nullable();
      $t->integer('language_id')->nullable();
    });
    Schema::create('bookings', function ($t) {
      $t->id();
      $t->string('paymentStatus')->nullable();
      $t->string('quantity')->nullable();
      $t->string('email')->nullable();
      $t->timestamps();
    });
    Schema::create('organizers', function ($t) {
      $t->id();
      $t->string('username');
      $t->string('email');
      $t->string('password');
      $t->string('photo')->nullable();
      $t->string('cover_photo')->nullable();
      $t->timestamp('email_verified_at')->nullable();
      $t->string('website')->nullable();
      $t->string('instagram')->nullable();
      $t->string('tiktok')->nullable();
      $t->string('facebook')->nullable();
      $t->string('twitter')->nullable();
      $t->string('linkedin')->nullable();
      $t->timestamps();
    });
    Schema::create('organizer_infos', function ($t) {
      $t->id();
      $t->integer('organizer_id')->nullable();
      $t->integer('language_id')->nullable();
      $t->string('name')->nullable();
      $t->string('country')->nullable();
      $t->string('city')->nullable();
      $t->string('state')->nullable();
      $t->text('address')->nullable();
      $t->longText('details')->nullable();
      $t->timestamps();
    });
  }
}
