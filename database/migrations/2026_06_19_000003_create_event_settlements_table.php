<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('event_settlements', function (Blueprint $table) {
      $table->id();
      $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
      $table->foreignId('organizer_id')->constrained('organizers')->cascadeOnDelete();
      $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
      $table->string('amount_option', 32);
      $table->decimal('paid_amount', 15, 2)->default(0);
      $table->decimal('covered_organizer_amount', 15, 2)->default(0);
      $table->decimal('balance_debited_amount', 15, 2)->default(0);
      $table->decimal('charged_amount_snapshot', 15, 2)->default(0);
      $table->decimal('organizer_net_amount_snapshot', 15, 2)->default(0);
      $table->decimal('platform_amount_snapshot', 15, 2)->default(0);
      $table->unsignedInteger('paid_bookings_count')->default(0);
      $table->date('paid_at')->nullable();
      $table->string('reference', 160)->nullable();
      $table->text('note')->nullable();
      $table->timestamps();

      $table->index(['event_id', 'organizer_id']);
      $table->index(['organizer_id', 'paid_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_settlements');
  }
};
