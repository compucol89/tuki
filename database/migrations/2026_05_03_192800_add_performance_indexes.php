<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. events: filtro más frecuente en Home y Eventos
        Schema::table('events', function (Blueprint $table) {
            $table->index(['status', 'end_date_time'], 'idx_events_status_end_date');
        });

        // 2. event_contents: JOIN + WHERE en TODAS las queries
        Schema::table('event_contents', function (Blueprint $table) {
            $table->index(['language_id', 'event_id'], 'idx_ec_lang_event');
        });

        // 3. tickets: foreign key usada en subqueries y badges
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('event_id', 'idx_tickets_event_id');
        });

        // 4. event_images: foreign key usada en galería y marquee
        Schema::table('event_images', function (Blueprint $table) {
            $table->index('event_id', 'idx_ei_event_id');
        });

        // 5. wishlists: foreign key + lookup en checkWishList
        Schema::table('wishlists', function (Blueprint $table) {
            $table->index(['customer_id', 'event_id'], 'idx_wishlists_customer_event');
        });

        // 6. event_dates: foreign key + filtro por fecha
        Schema::table('event_dates', function (Blueprint $table) {
            $table->index(['event_id', 'start_date_time'], 'idx_ed_event_start');
        });

        // 7. bookings: filtro en badges de ventas recientes
        Schema::table('bookings', function (Blueprint $table) {
            $table->index(['event_id', 'paymentStatus'], 'idx_bookings_event_status');
        });

        // 8. event_contents: lookup por slug en detalle
        Schema::table('event_contents', function (Blueprint $table) {
            $table->index('slug', 'idx_ec_slug');
        });

        // 9. tickets: filtro de precio en listado
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('f_price', 'idx_tickets_f_price');
        });

        // 10. tickets: filtro de pricing en listado
        Schema::table('tickets', function (Blueprint $table) {
            $table->index('pricing_type', 'idx_tickets_pricing_type');
        });

        // 11. event_categories: filtro en Home y Eventos
        Schema::table('event_categories', function (Blueprint $table) {
            $table->index(['language_id', 'status', 'is_featured'], 'idx_ecat_lang_status_featured');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('idx_events_status_end_date');
        });
        Schema::table('event_contents', function (Blueprint $table) {
            $table->dropIndex('idx_ec_lang_event');
            $table->dropIndex('idx_ec_slug');
        });
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('idx_tickets_event_id');
            $table->dropIndex('idx_tickets_f_price');
            $table->dropIndex('idx_tickets_pricing_type');
        });
        Schema::table('event_images', function (Blueprint $table) {
            $table->dropIndex('idx_ei_event_id');
        });
        Schema::table('wishlists', function (Blueprint $table) {
            $table->dropIndex('idx_wishlists_customer_event');
        });
        Schema::table('event_dates', function (Blueprint $table) {
            $table->dropIndex('idx_ed_event_start');
        });
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_event_status');
        });
        Schema::table('event_categories', function (Blueprint $table) {
            $table->dropIndex('idx_ecat_lang_status_featured');
        });
    }
};
