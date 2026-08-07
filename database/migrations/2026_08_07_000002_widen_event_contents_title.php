<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    // Títulos de evento legacy truncados (p.ej. "Buenos Aire" por columna corta).
    // Ampliar la columna no rompe nada y evita truncamientos futuros silenciosos.
    Schema::table('event_contents', function (Blueprint $table) {
      $table->string('title', 191)->nullable()->change();
    });
  }

  public function down(): void
  {
    Schema::table('event_contents', function (Blueprint $table) {
      $table->string('title', 191)->nullable()->change();
    });
  }
};
