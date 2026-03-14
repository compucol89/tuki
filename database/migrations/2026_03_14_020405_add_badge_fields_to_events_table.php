<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('manual_badge')->nullable()->after('status');
            $table->unsignedInteger('views_last_24h')->default(0)->after('views_count');
            $table->timestamp('views_last_reset')->nullable()->after('views_last_24h');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['manual_badge', 'views_last_24h', 'views_last_reset']);
        });
    }
};
