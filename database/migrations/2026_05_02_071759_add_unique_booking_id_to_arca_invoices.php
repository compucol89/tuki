<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arca_invoices', function (Blueprint $table): void {
            $table->unique('booking_id', 'arca_invoices_booking_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('arca_invoices', function (Blueprint $table): void {
            $table->dropUnique('arca_invoices_booking_id_unique');
        });
    }
};
