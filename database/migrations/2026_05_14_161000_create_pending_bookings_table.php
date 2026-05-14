<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_bookings', function (Blueprint $table): void {
            $table->id();
            $table->string('token', 64)->unique();
            $table->unsignedBigInteger('event_id');
            $table->json('data');
            $table->decimal('amount', 10, 2);
            $table->string('status', 20)->default('pending');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_bookings');
    }
};