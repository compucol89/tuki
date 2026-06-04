<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('organizer_id')->constrained('organizers')->cascadeOnDelete();
            $table->enum('format', ['square', 'gallery', 'og']);
            $table->enum('status', ['pending', 'running', 'completed', 'failed'])->default('pending');
            $table->string('model')->default('gpt-image-2');
            $table->text('prompt')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->decimal('cost_estimate', 8, 4)->nullable();
            $table->text('error_message')->nullable();
            $table->string('output_path')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'format']);
            $table->index(['organizer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_ai_generations');
    }
};
