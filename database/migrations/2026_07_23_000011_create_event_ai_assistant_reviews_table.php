<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('event_ai_assistant_reviews', function (Blueprint $table) {
      $table->id();
      $table->foreignId('run_id')->constrained('event_ai_assistant_runs')->cascadeOnDelete();
      $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
      $table->foreignId('organizer_id')->constrained('organizers')->cascadeOnDelete();
      $table->json('canonical_event_facts')->nullable();
      $table->json('accepted_fields')->nullable();
      $table->json('ignored_fields')->nullable();
      $table->json('audience_payload')->nullable();
      $table->string('tone', 60)->default('cercano_rioplatense');
      $table->string('intensity', 30)->default('equilibrado');
      $table->string('status', 30)->default('pending');
      $table->timestamp('reviewed_at')->nullable();
      $table->timestamps();

      $table->index(['event_id', 'status'], 'idx_event_ai_reviews_event_status');
      $table->index(['organizer_id', 'created_at'], 'idx_event_ai_reviews_org_created');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_ai_assistant_reviews');
  }
};
