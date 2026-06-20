<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('organizer_telegram_accounts', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('organizer_id')->index();
      $table->string('telegram_user_id')->unique();
      $table->string('telegram_chat_id')->nullable()->index();
      $table->string('username')->nullable();
      $table->string('first_name')->nullable();
      $table->string('last_name')->nullable();
      $table->boolean('is_active')->default(true);
      $table->timestamp('linked_at')->nullable();
      $table->timestamps();

      $table->foreign('organizer_id')->references('id')->on('organizers')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('organizer_telegram_accounts');
  }
};
