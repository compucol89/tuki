<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('basic_settings') || Schema::hasColumn('basic_settings', 'how_ticket_will_be_send')) {
            return;
        }

        Schema::table('basic_settings', function (Blueprint $table) {
            $table->string('how_ticket_will_be_send')->default('instant')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('basic_settings') || ! Schema::hasColumn('basic_settings', 'how_ticket_will_be_send')) {
            return;
        }

        Schema::table('basic_settings', function (Blueprint $table) {
            $table->dropColumn('how_ticket_will_be_send');
        });
    }
};
