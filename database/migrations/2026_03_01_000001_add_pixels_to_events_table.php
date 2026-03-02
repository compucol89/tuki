<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPixelsToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('meta_pixel_id')->nullable()->after('ticket_logo');
            $table->string('google_analytics_id')->nullable()->after('meta_pixel_id');
            $table->string('tiktok_pixel_id')->nullable()->after('google_analytics_id');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['meta_pixel_id', 'google_analytics_id', 'tiktok_pixel_id']);
        });
    }
}
