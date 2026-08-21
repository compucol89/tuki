<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    if (!$this->canBackfill()) {
      return;
    }

    DB::table('testimonials')
      ->where('published', false)
      ->where('verified', false)
      ->where('consent', false)
      ->whereNull('verified_at')
      ->whereNull('source')
      ->whereNotNull('name')
      ->whereNotNull('occupation')
      ->whereNotNull('comment')
      ->whereNotNull('image')
      ->update([
        'published' => true,
        'verified' => true,
        'consent' => true,
        'verified_at' => now(),
        'verified_by' => 'legacy_migration',
        'source' => 'legacy_public_testimonial',
        'original_text' => DB::raw('comment'),
        'updated_at' => now(),
      ]);
  }

  public function down(): void
  {
    if (!$this->canBackfill()) {
      return;
    }

    DB::table('testimonials')
      ->where('source', 'legacy_public_testimonial')
      ->update([
        'published' => false,
        'verified' => false,
        'consent' => false,
        'verified_at' => null,
        'verified_by' => null,
        'source' => null,
        'original_text' => null,
        'updated_at' => now(),
      ]);
  }

  private function canBackfill(): bool
  {
    return Schema::hasTable('testimonials')
      && Schema::hasColumn('testimonials', 'published')
      && Schema::hasColumn('testimonials', 'verified')
      && Schema::hasColumn('testimonials', 'consent')
      && Schema::hasColumn('testimonials', 'verified_at')
      && Schema::hasColumn('testimonials', 'verified_by')
      && Schema::hasColumn('testimonials', 'source')
      && Schema::hasColumn('testimonials', 'original_text');
  }
};
