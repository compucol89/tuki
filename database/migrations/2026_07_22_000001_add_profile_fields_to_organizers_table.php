<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::table('organizers', function (Blueprint $table): void {
      if (!Schema::hasColumn('organizers', 'cover_photo')) {
        $table->string('cover_photo')->nullable()->after('photo');
      }

      if (!Schema::hasColumn('organizers', 'website')) {
        $table->string('website')->nullable()->after('linkedin');
      }

      if (!Schema::hasColumn('organizers', 'instagram')) {
        $table->string('instagram')->nullable()->after('website');
      }

      if (!Schema::hasColumn('organizers', 'tiktok')) {
        $table->string('tiktok')->nullable()->after('instagram');
      }

      if (!Schema::hasColumn('organizers', 'meta_pixel_id')) {
        $table->string('meta_pixel_id', 32)->nullable()->after('tiktok');
      }
    });
  }

  public function down(): void
  {
    Schema::table('organizers', function (Blueprint $table): void {
      foreach (['meta_pixel_id', 'tiktok', 'instagram', 'website', 'cover_photo'] as $column) {
        if (Schema::hasColumn('organizers', $column)) {
          $table->dropColumn($column);
        }
      }
    });
  }
};
