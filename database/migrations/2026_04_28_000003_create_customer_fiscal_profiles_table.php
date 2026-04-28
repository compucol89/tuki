<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_fiscal_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->nullable()->unique()->constrained('customers')->nullOnDelete();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->string('full_name');
            $table->string('document_type')->default('DNI');
            $table->string('document_number', 20);
            $table->string('iva_condition')->default('consumidor_final');
            $table->string('fiscal_address')->nullable();
            $table->string('fiscal_email')->nullable();
            $table->timestamps();

            $table->index(['document_type', 'document_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_fiscal_profiles');
    }
};
