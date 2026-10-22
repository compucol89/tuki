<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('ticket_scans', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('booking_id');
      $table->unsignedBigInteger('event_id')->nullable();
      $table->unsignedBigInteger('organizer_id')->nullable();
      $table->string('unique_id', 64);
      $table->string('scanner_type', 20);
      $table->unsignedBigInteger('scanner_id')->nullable();
      $table->string('ip', 45)->nullable();
      $table->string('user_agent', 255)->nullable();
      $table->timestamp('scanned_at');
      $table->timestamps();

      $table->unique(['booking_id', 'unique_id']);
      $table->index(['event_id', 'scanned_at']);
      $table->index(['organizer_id', 'scanned_at']);
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('ticket_scans');
  }
};
