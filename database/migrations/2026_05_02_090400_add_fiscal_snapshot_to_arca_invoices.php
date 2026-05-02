<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arca_invoices', function (Blueprint $table): void {
            $table->decimal('service_fee_percentage_used', 8, 4)->nullable()->after('commission_amount');
            $table->string('service_fee_tax_mode_used')->nullable()->after('service_fee_percentage_used');
            $table->decimal('vat_percentage_used', 8, 4)->nullable()->after('service_fee_tax_mode_used');
            $table->string('issuer_cuit_used')->nullable()->after('vat_percentage_used');
            $table->unsignedInteger('invoice_type_used')->nullable()->after('issuer_cuit_used');
            $table->unsignedInteger('point_of_sale_used')->nullable()->after('invoice_type_used');
        });
    }

    public function down(): void
    {
        Schema::table('arca_invoices', function (Blueprint $table): void {
            $table->dropColumn([
                'service_fee_percentage_used',
                'service_fee_tax_mode_used',
                'vat_percentage_used',
                'issuer_cuit_used',
                'invoice_type_used',
                'point_of_sale_used',
            ]);
        });
    }
};
