<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_settings', function (Blueprint $table) {
            $table->boolean('send_arca_invoice_email')->default(true)->after('enabled');
        });
    }

    public function down(): void
    {
        Schema::table('billing_settings', function (Blueprint $table) {
            $table->dropColumn('send_arca_invoice_email');
        });
    }
};
