<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('payment_incidents', function (Blueprint $table) {
      $table->id();
      $table->string('gateway', 40);
      $table->string('payment_id', 120);
      $table->unsignedBigInteger('booking_id')->nullable();
      $table->unsignedBigInteger('event_id')->nullable();
      $table->decimal('amount', 12, 2)->nullable();
      $table->string('currency', 10)->nullable();
      $table->string('reason', 80);
      $table->string('status', 30)->default('manual_review');
      $table->json('context')->nullable();
      $table->timestamps();

      $table->index(['gateway', 'payment_id']);
      $table->index(['status', 'created_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('payment_incidents');
  }
};
