<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    // La IA devuelve audit_status libres (ej. "listo_con_revision_de_promocion", 32 chars)
    // que desbordaban el string(30) original → SQLSTATE 22001 en GenerateEventContentDraftJob.
    Schema::table('event_ai_content_drafts', function (Blueprint $table) {
      $table->string('audit_status', 191)->nullable()->change();
    });
  }

  public function down(): void
  {
    Schema::table('event_ai_content_drafts', function (Blueprint $table) {
      $table->string('audit_status', 30)->nullable()->change();
    });
  }
};
