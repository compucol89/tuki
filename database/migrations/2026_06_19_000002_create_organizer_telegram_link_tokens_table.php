<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('organizer_telegram_link_tokens', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('organizer_id')->index();
      $table->string('token_hash', 64)->unique();
      $table->timestamp('expires_at');
      $table->timestamp('used_at')->nullable();
      $table->timestamps();

      $table->foreign('organizer_id')->references('id')->on('organizers')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('organizer_telegram_link_tokens');
  }
};
