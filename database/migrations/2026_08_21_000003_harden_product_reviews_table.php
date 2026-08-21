<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Hardening de la tabla legacy `product_reviews` (correcciones 3 de la
 * auditoría docs/auditorias/product-review-schema/):
 * - user_id/product_id → bigint unsigned (tipos de las FKs padre)
 * - FK product_id → products.id ON DELETE CASCADE
 * - FK user_id → customers.id ON DELETE SET NULL (review queda anónima)
 * - CHECK: review NULL o entre 1 y 5 (escala real de la UI)
 * - utf8mb3 → utf8mb4 (emojis/acentos en comments)
 * - Huérfanos existentes → NULL (sin borrado masivo)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->unsignedBigInteger('product_id')->nullable()->change();
        });

        // Huérfanos: apuntar a NULL en vez de borrar (integridad sin pérdida).
        DB::statement('UPDATE product_reviews pr LEFT JOIN products p ON p.id = pr.product_id SET pr.product_id = NULL WHERE pr.product_id IS NOT NULL AND p.id IS NULL');
        DB::statement('UPDATE product_reviews pr LEFT JOIN customers c ON c.id = pr.user_id SET pr.user_id = NULL WHERE pr.user_id IS NOT NULL AND c.id IS NULL');

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('customers')->onDelete('set null');
        });

        // CHECK con SQL nativo (Blueprint no expone ->check()).
        DB::statement('ALTER TABLE product_reviews ADD CONSTRAINT product_reviews_review_check CHECK (review IS NULL OR (review >= 1 AND review <= 5))');

        DB::statement('ALTER TABLE product_reviews CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['user_id']);
        });
        DB::statement('ALTER TABLE product_reviews DROP CHECK product_reviews_review_check');
    }
};
