<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arca_invoices', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable()->index();
            $table->unsignedBigInteger('organizer_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('environment')->default('homologation');
            $table->string('status')->default('draft');
            $table->string('invoice_model')->default('commission_to_organizer');
            $table->string('currency')->default('ARS');
            $table->integer('point_of_sale')->nullable();
            $table->integer('cbte_tipo')->nullable();
            $table->bigInteger('cbte_nro')->nullable();
            $table->integer('concept')->nullable();
            $table->integer('doc_tipo')->nullable();
            $table->string('doc_nro')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_tax_condition')->nullable();
            $table->string('recipient_tax_id')->nullable();
            $table->string('recipient_address')->nullable();
            $table->date('service_from')->nullable();
            $table->date('service_to')->nullable();
            $table->date('due_date')->nullable();
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->decimal('vat_amount', 15, 2)->default(0);
            $table->decimal('exempt_amount', 15, 2)->default(0);
            $table->decimal('non_taxed_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('commission_rate', 8, 4)->nullable();
            $table->decimal('commission_base_amount', 15, 2)->nullable();
            $table->decimal('commission_amount', 15, 2)->nullable();
            $table->string('cae')->nullable();
            $table->date('cae_due_date')->nullable();
            $table->json('arca_request')->nullable();
            $table->json('arca_response')->nullable();
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->string('created_by_type')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arca_invoices');
    }
};
