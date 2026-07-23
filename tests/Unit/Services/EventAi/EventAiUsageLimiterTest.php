<?php

namespace Tests\Unit\Services\EventAi;

use App\Services\EventAi\EventAiUsageLimiter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EventAiUsageLimiterTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::dropIfExists('event_ai_assistant_runs');
    Schema::create('event_ai_assistant_runs', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id');
      $table->unsignedBigInteger('organizer_id');
      $table->string('type', 30)->default('analysis');
      $table->string('status', 30)->default('completed');
      $table->timestamps();
    });

    config([
      'openai.event_assistant.limits.max_runs_per_event' => 2,
      'openai.event_assistant.limits.max_runs_per_organizer_day' => 10,
    ]);
  }

  public function test_it_allows_an_event_with_remaining_runs(): void
  {
    $result = app(EventAiUsageLimiter::class)->check(55, 7);

    $this->assertTrue($result['allowed']);
    $this->assertSame(2, $result['remaining_event_runs']);
    $this->assertSame(10, $result['remaining_daily_runs']);
  }

  public function test_it_blocks_when_event_limit_is_reached(): void
  {
    $this->insertRun(55, 7);
    $this->insertRun(55, 7);

    $result = app(EventAiUsageLimiter::class)->check(55, 7);

    $this->assertFalse($result['allowed']);
    $this->assertSame('event_limit_reached', $result['reason']);
    $this->assertSame(0, $result['remaining_event_runs']);
  }

  public function test_it_blocks_when_daily_organizer_limit_is_reached(): void
  {
    for ($i = 1; $i <= 10; $i++) {
      $this->insertRun($i, 7);
    }

    $result = app(EventAiUsageLimiter::class)->check(99, 7);

    $this->assertFalse($result['allowed']);
    $this->assertSame('organizer_daily_limit_reached', $result['reason']);
    $this->assertSame(0, $result['remaining_daily_runs']);
  }

  public function test_daily_limit_ignores_previous_days(): void
  {
    for ($i = 1; $i <= 10; $i++) {
      $this->insertRun($i, 7, now()->subDay());
    }

    $result = app(EventAiUsageLimiter::class)->check(99, 7);

    $this->assertTrue($result['allowed']);
    $this->assertSame(10, $result['remaining_daily_runs']);
  }

  private function insertRun(int $eventId, int $organizerId, $createdAt = null): void
  {
    DB::table('event_ai_assistant_runs')->insert([
      'event_id' => $eventId,
      'organizer_id' => $organizerId,
      'type' => 'analysis',
      'status' => 'completed',
      'created_at' => $createdAt ?? now(),
      'updated_at' => $createdAt ?? now(),
    ]);
  }
}
