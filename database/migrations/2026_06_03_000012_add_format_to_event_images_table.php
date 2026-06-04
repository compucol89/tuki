<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_images', function (Blueprint $table) {
            $table->enum('format', ['square', 'gallery', 'og', 'hero'])->nullable()->after('image');
            $table->index('format');
        });
    }

    public function down(): void
    {
        Schema::table('event_images', function (Blueprint $table) {
            $table->dropIndex(['format']);
            $table->dropColumn('format');
        });
    }
};
