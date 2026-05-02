<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_settings', function (Blueprint $table): void {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('issuer_cuit')->nullable();
            $table->string('issuer_iva_condition')->nullable();
            $table->unsignedInteger('point_of_sale')->nullable();
            $table->decimal('service_fee_percentage', 8, 4)->default(0);
            $table->string('service_fee_tax_mode')->default('no_vat_added');
            $table->decimal('vat_percentage', 8, 4)->default(0);
            $table->unsignedInteger('default_invoice_type')->nullable();
            $table->string('environment')->default('testing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_settings');
    }
};
