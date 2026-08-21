<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * F-003 — la tabla testimonials existía solo por dump externo (sin migración
 * en el repo). Documenta el schema real y agrega moderación: published,
 * verified, verified_at, verified_by (+ source/consent/original_text).
 */
return new class extends Migration
{
  public function up(): void
  {
    if (Schema::hasTable('testimonials')) {
      Schema::table('testimonials', function (Blueprint $table) {
        $this->addModerationColumns($table);
      });

      return;
    }

    Schema::create('testimonials', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('language_id')->nullable();
      $table->string('image')->nullable();
      $table->string('name')->nullable();
      $table->string('occupation')->nullable();
      $table->text('comment')->nullable();
      $table->unsignedInteger('serial_number')->nullable();
      $table->unsignedTinyInteger('rating')->nullable();
      $table->boolean('published')->default(false);
      $table->boolean('verified')->default(false);
      $table->timestamp('verified_at')->nullable();
      $table->string('verified_by')->nullable();
      $table->string('source')->nullable();
      $table->boolean('consent')->default(false);
      $table->text('original_text')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('testimonials');
  }

  private function addModerationColumns(Blueprint $table): void
  {
    foreach (['published', 'verified', 'consent'] as $column) {
      if (!Schema::hasColumn('testimonials', $column)) {
        $table->boolean($column)->default(false);
      }
    }

    foreach (['verified_at'] as $column) {
      if (!Schema::hasColumn('testimonials', $column)) {
        $table->timestamp($column)->nullable();
      }
    }

    foreach (['verified_by', 'source'] as $column) {
      if (!Schema::hasColumn('testimonials', $column)) {
        $table->string($column)->nullable();
      }
    }

    if (!Schema::hasColumn('testimonials', 'original_text')) {
      $table->text('original_text')->nullable();
    }
  }
};
