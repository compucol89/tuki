<?php

namespace Tests\Unit;

use App\Http\Controllers\FrontEnd\PaymentGateway\MercadoPagoController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;

class MercadoPagoEventBookingPreferenceItemTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::create('events', function (Blueprint $table) {
      $table->id();
      $table->string('date_type')->nullable();
      $table->string('start_date')->nullable();
      $table->string('start_time')->nullable();
      $table->timestamps();
    });

    Schema::create('event_contents', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->unsignedBigInteger('language_id')->nullable();
      $table->string('title')->nullable();
      $table->timestamps();
    });

    Schema::create('languages', function (Blueprint $table) {
      $table->id();
      $table->string('code')->nullable();
      $table->boolean('is_default')->default(false);
      $table->timestamps();
    });
  }

  protected function tearDown(): void
  {
    Schema::dropIfExists('languages');
    Schema::dropIfExists('event_contents');
    Schema::dropIfExists('events');

    parent::tearDown();
  }

  public function test_preference_item_uses_event_title_and_start_date_without_session_event(): void
  {
    DB::table('languages')->insert([
      'id' => 1,
      'code' => 'es',
      'is_default' => true,
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    DB::table('events')->insert([
      'id' => 120,
      'date_type' => 'single',
      'start_date' => '2026-06-27',
      'start_time' => '18:30',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    DB::table('event_contents')->insert([
      'event_id' => 120,
      'language_id' => 1,
      'title' => 'Colombia vs Portugal: Rumba y Fiesta Fan Fest Mundial 2026 en Palermo',
      'created_at' => now(),
      'updated_at' => now(),
    ]);

    $controller = (new ReflectionClass(MercadoPagoController::class))->newInstanceWithoutConstructor();
    $method = new ReflectionMethod(MercadoPagoController::class, 'buildEventBookingPreferenceItem');
    $method->setAccessible(true);

    $item = $method->invoke($controller, 120, 1, 46000.0, 'ARS');

    $this->assertStringContainsString('Colombia vs Portugal', $item['title']);
    $this->assertStringContainsString('27/06/2026 18:30', $item['description']);
    $this->assertStringNotContainsString('Event Booking', $item['title']);
    $this->assertSame(46000.0, $item['unit_price']);
  }
}
