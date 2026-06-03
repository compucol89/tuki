<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('event_addons', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('event_addon_section_id')->index();
      $table->unsignedBigInteger('product_id')->nullable()->index();
      $table->unsignedBigInteger('event_id')->index();
      $table->string('title');
      $table->text('description')->nullable();
      $table->decimal('price', 8, 2);
      $table->decimal('previous_price', 8, 2)->nullable();
      $table->string('image')->nullable();
      $table->integer('stock')->nullable();
      $table->integer('max_per_order')->nullable();
      $table->boolean('is_active')->default(true);
      $table->boolean('requires_age_verification')->default(false);
      $table->boolean('redeemable_only_at_event')->default(true);
      $table->boolean('non_refundable')->default(false);
      $table->integer('sort_order')->default(0);
      $table->timestamps();

      $table->foreign('event_addon_section_id')->references('id')->on('event_addon_sections')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
      $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
    });
  }

  public function down(): void
  {
    Schema::dropIfExists('event_addons');
  }
};
