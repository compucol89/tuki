<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('event_ai_content_drafts', function (Blueprint $table) {
      $table->id();
      $table->foreignId('review_id')->constrained('event_ai_assistant_reviews')->cascadeOnDelete();
      $table->foreignId('run_id')->nullable()->constrained('event_ai_assistant_runs')->nullOnDelete();
      $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
      $table->foreignId('organizer_id')->constrained('organizers')->cascadeOnDelete();
      $table->string('status', 30)->default('pending');
      $table->json('generated_payload')->nullable();
      $table->json('audit_payload')->nullable();
      $table->string('audit_status', 30)->nullable();
      $table->boolean('needs_human_review')->default(false);
      $table->timestamp('applied_at')->nullable();
      $table->timestamps();

      $table->index(['event_id', 'status'], 'idx_event_ai_drafts_event_status');
      $table->index(['organizer_id', 'created_at'], 'idx_event_ai_drafts_org_created');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_ai_content_drafts');
  }
};
