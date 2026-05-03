<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('basic_settings')) {
            return;
        }

        Schema::create('basic_settings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('uniqid')->default(12345)->unique();

            $table->string('website_title')->nullable();
            $table->string('favicon')->nullable();
            $table->string('logo')->nullable();
            $table->string('footer_logo')->nullable();
            $table->string('preloader')->nullable();
            $table->string('email_address')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('timezone')->nullable();

            $table->unsignedTinyInteger('theme_version')->default(3);
            $table->unsignedTinyInteger('admin_theme_version')->default(1);

            $table->string('base_currency_symbol')->nullable();
            $table->string('base_currency_symbol_position')->nullable();
            $table->string('base_currency_text')->nullable();
            $table->string('base_currency_text_position')->nullable();
            $table->decimal('base_currency_rate', 15, 4)->nullable();

            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->string('breadcrumb_overlay_color')->nullable();
            $table->decimal('breadcrumb_overlay_opacity', 4, 3)->nullable();
            $table->string('breadcrumb')->nullable();

            $table->string('smtp_status')->nullable();
            $table->string('smtp_host')->nullable();
            $table->unsignedInteger('smtp_port')->nullable();
            $table->string('encryption')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('from_mail')->nullable();
            $table->string('from_name')->nullable();
            $table->string('to_mail')->nullable();

            $table->string('disqus_status')->nullable();
            $table->string('disqus_short_name')->nullable();
            $table->string('google_recaptcha_status')->nullable();
            $table->string('google_recaptcha_site_key')->nullable();
            $table->string('google_recaptcha_secret_key')->nullable();

            $table->string('whatsapp_status')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('whatsapp_header_title')->nullable();
            $table->string('whatsapp_popup_status')->nullable();
            $table->text('whatsapp_popup_message')->nullable();

            $table->unsignedTinyInteger('facebook_login_status')->default(0);
            $table->string('facebook_app_id')->nullable();
            $table->string('facebook_app_secret')->nullable();
            $table->unsignedTinyInteger('google_login_status')->default(0);
            $table->string('google_client_id')->nullable();
            $table->string('google_client_secret')->nullable();

            $table->string('maintenance_img')->nullable();
            $table->unsignedTinyInteger('maintenance_status')->default(0);
            $table->text('maintenance_msg')->nullable();
            $table->string('bypass_token')->nullable();

            $table->string('features_section_image')->nullable();
            $table->string('testimonials_section_image')->nullable();
            $table->string('course_categories_section_image')->nullable();
            $table->string('notification_image')->nullable();

            $table->string('google_adsense_publisher_id')->nullable();

            $table->string('shop_status')->nullable();
            $table->string('catalog_mode')->nullable();
            $table->unsignedTinyInteger('is_shop_rating')->default(0);
            $table->string('shop_guest_checkout')->nullable();
            $table->string('shop_tax')->nullable();

            $table->unsignedTinyInteger('event_guest_checkout_status')->default(0);
            $table->string('how_ticket_will_be_send')->default('instant')->nullable();

            $table->decimal('tax', 10, 2)->nullable();
            $table->decimal('commission', 10, 2)->nullable();

            $table->unsignedTinyInteger('organizer_email_verification')->default(0);
            $table->unsignedTinyInteger('organizer_admin_approval')->default(0);
            $table->text('admin_approval_notice')->nullable();

            $table->timestamps();
        });

        DB::table('basic_settings')->insert([
            'uniqid' => 12345,
            'website_title' => 'TukiPass',
            'theme_version' => 3,
            'admin_theme_version' => 1,
            'primary_color' => '#F97316',
            'timezone' => 'America/Argentina/Buenos_Aires',
            'event_guest_checkout_status' => 1,
            'how_ticket_will_be_send' => 'instant',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('basic_settings');
    }
};
