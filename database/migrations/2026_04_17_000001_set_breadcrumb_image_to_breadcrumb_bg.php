<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Use the new hero/breadcrumb background shipped at public/assets/admin/img/breadcrumb_bg.jpg
     */
    public function up(): void
    {
        if (! Schema::hasTable('basic_settings') || ! Schema::hasColumn('basic_settings', 'breadcrumb')) {
            return;
        }

        DB::table('basic_settings')->update(['breadcrumb' => 'breadcrumb_bg.jpg']);
    }

    public function down(): void
    {
        // Previous filename is environment-specific; no safe automatic restore.
    }
};
