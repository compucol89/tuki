<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('limit_free_tickets_per_person')->default(false)->after('event_addons_enabled');
            $table->unsignedTinyInteger('free_tickets_per_person_limit')->default(2)->after('limit_free_tickets_per_person');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['limit_free_tickets_per_person', 'free_tickets_per_person_limit']);
        });
    }
};
