<?php

namespace Tests\Feature;

use App\Http\Controllers\FrontEnd\SupportTicketController;
use App\Models\Customer;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SupportTicketIsolationTest extends TestCase
{
  protected function setUp(): void
  {
    parent::setUp();

    Schema::create('customers', function (Blueprint $table) {
      $table->id();
      $table->string('name')->nullable();
      $table->string('email')->nullable();
      $table->string('password')->nullable();
      $table->timestamps();
    });

    Schema::create('support_ticket_statuses', function (Blueprint $table) {
      $table->id();
      $table->string('support_ticket_status')->default('active');
      $table->timestamps();
    });

    Schema::create('support_tickets', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id');
      $table->string('user_type')->default('customer');
      $table->string('email')->nullable();
      $table->string('subject')->nullable();
      $table->text('description')->nullable();
      $table->string('attachment')->nullable();
      $table->string('status')->nullable();
      $table->timestamp('last_message')->nullable();
      $table->timestamps();
    });

    Schema::create('conversations', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->unsignedTinyInteger('type')->nullable();
      $table->unsignedBigInteger('support_ticket_id')->nullable();
      $table->text('reply')->nullable();
      $table->string('file')->nullable();
      $table->timestamps();
    });

    \DB::table('support_ticket_statuses')->insert([
      'support_ticket_status' => 'active',
      'created_at' => now(),
      'updated_at' => now(),
    ]);
  }

  protected function tearDown(): void
  {
    Auth::guard('customer')->logout();

    Schema::dropIfExists('conversations');
    Schema::dropIfExists('support_tickets');
    Schema::dropIfExists('support_ticket_statuses');
    Schema::dropIfExists('customers');

    parent::tearDown();
  }

  public function test_customer_cannot_read_another_customers_ticket(): void
  {
    $owner = $this->customer('owner@example.test');
    $intruder = $this->customer('intruder@example.test');
    $ticket = $this->ticket($owner->id);

    Auth::guard('customer')->login($intruder);

    $this->expectException(ModelNotFoundException::class);

    (new SupportTicketController())->message($ticket->id);
  }

  public function test_customer_cannot_reply_to_another_customers_ticket(): void
  {
    $owner = $this->customer('owner@example.test');
    $intruder = $this->customer('intruder@example.test');
    $ticket = $this->ticket($owner->id);

    Auth::guard('customer')->login($intruder);

    $this->expectException(ModelNotFoundException::class);

    (new SupportTicketController())->reply(
      Request::create('/usuario/support-ticket/reply/' . $ticket->id, 'POST', ['reply' => 'mensaje']),
      $ticket->id
    );
  }

  private function customer(string $email): Customer
  {
    return Customer::create([
      'name' => $email,
      'email' => $email,
      'password' => bcrypt('secret'),
    ]);
  }

  private function ticket(int $ownerId): SupportTicket
  {
    return SupportTicket::create([
      'user_id' => $ownerId,
      'user_type' => 'customer',
      'email' => 'owner@example.test',
      'subject' => 'Ayuda',
      'description' => 'Necesito ayuda',
      'status' => 'open',
    ]);
  }
}
