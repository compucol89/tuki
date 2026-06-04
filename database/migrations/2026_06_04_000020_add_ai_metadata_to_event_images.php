<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('event_ai_generations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_ai_generations', 'validation_ssim_score')) {
                $table->decimal('validation_ssim_score', 8, 6)->nullable()->after('cost_estimate');
            }
        });

        Schema::table('event_images', function (Blueprint $table) {
            if (!Schema::hasColumn('event_images', 'generation_method')) {
                $table->string('generation_method')->nullable()->after('format');
            }
            if (!Schema::hasColumn('event_images', 'source_image_hash')) {
                $table->string('source_image_hash', 64)->nullable()->after('generation_method');
            }
            if (!Schema::hasColumn('event_images', 'validation_ssim_score')) {
                $table->decimal('validation_ssim_score', 8, 6)->nullable()->after('source_image_hash');
            }
        });
    }

    public function down(): void
    {
        Schema::table('event_ai_generations', function (Blueprint $table) {
            if (Schema::hasColumn('event_ai_generations', 'validation_ssim_score')) {
                $table->dropColumn('validation_ssim_score');
            }
        });

        Schema::table('event_images', function (Blueprint $table) {
            if (Schema::hasColumn('event_images', 'validation_ssim_score')) {
                $table->dropColumn('validation_ssim_score');
            }
            if (Schema::hasColumn('event_images', 'source_image_hash')) {
                $table->dropColumn('source_image_hash');
            }
            if (Schema::hasColumn('event_images', 'generation_method')) {
                $table->dropColumn('generation_method');
            }
        });
    }
};
