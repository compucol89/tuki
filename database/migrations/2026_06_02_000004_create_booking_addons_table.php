<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('booking_addons', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('booking_id')->index();
      $table->unsignedBigInteger('event_id')->index();
      $table->unsignedBigInteger('event_addon_id')->nullable()->index();
      $table->string('title');
      $table->text('description')->nullable();
      $table->decimal('unit_price', 10, 2);
      $table->unsignedInteger('quantity');
      $table->decimal('subtotal', 10, 2);
      $table->boolean('requires_age_verification')->default(false);
      $table->boolean('redeemable_only_at_event')->default(true);
      $table->boolean('non_refundable')->default(false);
      $table->boolean('redeemed')->default(false);
      $table->timestamp('redeemed_at')->nullable();
      $table->timestamps();

      $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
      $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
      $table->foreign('event_addon_id')->references('id')->on('event_addons')->onDelete('set null');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('booking_addons');
  }
};
