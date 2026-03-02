<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMediaEmbedsToEventsTable extends Migration
{
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('spotify_url')->nullable()->after('tiktok_pixel_id');
            $table->string('youtube_url')->nullable()->after('spotify_url');
        });
    }

    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['spotify_url', 'youtube_url']);
        });
    }
}
