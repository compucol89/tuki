<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_settings', function (Blueprint $table): void {
            $table->string('invoice_item_description')->nullable();
            $table->boolean('invoice_item_include_event')->default(true);
            $table->boolean('invoice_item_include_booking')->default(true);
            $table->string('issuer_name')->nullable();
            $table->string('issuer_address')->nullable();
            $table->string('issuer_iva_condition_text')->nullable();
            $table->string('pdf_logo_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('billing_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'invoice_item_description',
                'invoice_item_include_event',
                'invoice_item_include_booking',
                'issuer_name',
                'issuer_address',
                'issuer_iva_condition_text',
                'pdf_logo_path',
            ]);
        });
    }
};
