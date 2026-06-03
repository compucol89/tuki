<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('event_addon_sections', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_id')->index();
      $table->unsignedBigInteger('organizer_id')->nullable()->index();
      $table->string('title');
      $table->string('slug')->nullable();
      $table->text('description')->nullable();
      $table->integer('sort_order')->default(0);
      $table->boolean('is_active')->default(true);
      $table->timestamps();

      $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
      $table->foreign('organizer_id')->references('id')->on('organizers')->onDelete('set null');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_addon_sections');
  }
};
