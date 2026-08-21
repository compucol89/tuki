<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Reemplaza las meta descriptions placeholder ("Home Description", etc.)
 * de la tabla `seos` por descripciones reales en español (es-AR).
 * Fuente: auditoría Google Search — GS-P0-04.
 */
return new class extends Migration
{
    public function up(): void
    {
        $replacements = [
            'meta_description_home' => 'Entradas y tickets online para eventos en Argentina: fiestas, conciertos, fan fests y más. Reservá tu lugar en minutos con Tukipass.',
            'meta_description_event' => 'Descubrí los próximos eventos en Argentina: conciertos, fiestas, fan fests y experiencias. Reservá tus entradas online con Tukipass.',
            'meta_description_organizer' => 'Explorá los productores y organizadores de eventos en Tukipass: perfiles, próximas fechas y agenda pública.',
            'meta_description_shop' => 'Tienda de Tukipass: productos y accesorios para tus eventos. Comprá online y recibí tu pedido.',
            'meta_description_blog' => 'Novedades y guías de Tukipass: cómo comprar entradas, tips para productores y lo último del mundo de los eventos.',
            'meta_description_faq' => 'Respuestas a las preguntas frecuentes sobre compra de entradas, pagos, reembolsos y uso de Tukipass.',
            'meta_description_contact' => 'Contactate con Tukipass: soporte, consultas y prensa. Respondemos por email y WhatsApp.',
            'meta_description_about' => 'Conocé Tukipass, la plataforma argentina de entradas y gestión de eventos de TAYRONA GROUP SAS.',
            'meta_description_customer_login' => 'Ingresá a tu cuenta de Tukipass para ver tus entradas y reservas.',
            'meta_description_customer_signup' => 'Creá tu cuenta gratis en Tukipass y comprá entradas de forma rápida y segura.',
            'meta_description_organizer_login' => 'Ingresá al panel de productores de Tukipass para gestionar tus eventos y ventas.',
            'meta_description_organizer_signup' => 'Creá tu cuenta de productor en Tukipass y empezá a vender entradas online.',
            'meta_description_customer_forget_password' => 'Recuperá tu contraseña de Tukipass.',
            'meta_description_organizer_forget_password' => 'Recuperá la contraseña de tu cuenta de productor en Tukipass.',
        ];

        foreach ($replacements as $column => $value) {
            DB::table('seos')
                ->where($column, 'like', '%Description%')
                ->orWhere($column, 'like', '%description%')
                ->update([$column => $value]);
        }

        // Invalidar caché de SEO por idioma (HomeController cachea 'home_seo_{id}' 24h).
        foreach (DB::table('languages')->pluck('id') as $languageId) {
            \Illuminate\Support\Facades\Cache::forget('home_seo_' . $languageId);
        }
    }

    public function down(): void
    {
        // No revierte: los valores anteriores eran placeholders sin utilidad.
    }
};
