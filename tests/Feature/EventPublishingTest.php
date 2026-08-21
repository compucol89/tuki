<?php

namespace Tests\Feature;

use App\Models\Event\EventContent;
use App\Support\DemoEventExclusion;
use App\Support\EventPublicWindow;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * F-006 — ventana de venta pública de eventos: status=1, no demo,
 * end_date_time >= now. Contratos del EventController@index.
 */
class EventPublishingTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();
    $this->buildMinimalSchema();
  }

  public function test_live_event_is_visible(): void
  {
    $this->insertEvent(1, 1, now()->addDays(3));
    $this->insertContent(1, 1, 8);

    $ids = EventPublicWindow::apply($this->baseQuery(), Carbon::now())->pluck('event_contents.event_id')->all();

    $this->assertSame([1], $ids);
  }

  public function test_expired_event_is_hidden(): void
  {
    $this->insertEvent(1, 1, now()->subDay());
    $this->insertContent(1, 1, 8);

    $this->assertEmpty(EventPublicWindow::apply($this->baseQuery(), Carbon::now())->pluck('event_contents.event_id')->all());
  }

  public function test_inactive_event_is_hidden(): void
  {
    $this->insertEvent(1, 0, now()->addDays(3));
    $this->insertContent(1, 1, 8);

    $this->assertEmpty(EventPublicWindow::apply($this->baseQuery(), Carbon::now())->pluck('event_contents.event_id')->all());
  }

  public function test_demo_event_is_hidden(): void
  {
    $demoId = DemoEventExclusion::EVENT_IDS[0];
    $this->insertEvent($demoId, 1, now()->addDays(3));
    $this->insertContent($demoId, $demoId, 8);

    $this->assertEmpty(EventPublicWindow::apply($this->baseQuery(), Carbon::now())->pluck('event_contents.event_id')->all());
  }

  private function baseQuery()
  {
    return EventContent::query()
      ->join('events', 'events.id', '=', 'event_contents.event_id');
  }

  private function insertEvent(int $id, int $status, Carbon $end): void
  {
    DB::table('events')->insert([
      'id' => $id, 'status' => $status, 'end_date_time' => $end,
    ]);
  }

  private function insertContent(int $id, int $eventId, int $languageId): void
  {
    DB::table('event_contents')->insert([
      'id' => $id, 'event_id' => $eventId, 'language_id' => $languageId,
    ]);
  }

  private function buildMinimalSchema(): void
  {
    foreach (['events', 'event_contents'] as $table) {
      Schema::dropIfExists($table);
    }

    Schema::create('events', function ($t) {
      $t->id();
      $t->string('status')->default('1');
      $t->dateTime('end_date_time')->nullable();
      $t->timestamps();
    });
    Schema::create('event_contents', function ($t) {
      $t->id();
      $t->integer('event_id')->nullable();
      $t->integer('language_id')->nullable();
    });
  }
}
