<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('event_ai_assistant_runs', function (Blueprint $table) {
      $table->id();
      $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
      $table->foreignId('organizer_id')->constrained('organizers')->cascadeOnDelete();
      $table->string('type', 30)->default('analysis');
      $table->string('status', 30)->default('pending');
      $table->string('model', 80)->nullable();
      $table->string('prompt_version', 80)->nullable();
      $table->string('source_image_path')->nullable();
      $table->string('source_image_hash', 64)->nullable();
      $table->json('input_payload')->nullable();
      $table->json('output_payload')->nullable();
      $table->json('moderation_payload')->nullable();
      $table->json('audit_payload')->nullable();
      $table->unsignedInteger('duration_ms')->nullable();
      $table->text('error_message')->nullable();
      $table->timestamps();

      $table->index(['event_id', 'type', 'status'], 'idx_event_ai_runs_event_type_status');
      $table->index(['organizer_id', 'type', 'created_at'], 'idx_event_ai_runs_org_type_created');
      $table->index('source_image_hash', 'idx_event_ai_runs_source_hash');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_ai_assistant_runs');
  }
};
