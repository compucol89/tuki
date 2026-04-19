<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cliente final — login y flujos relacionados (Argentina, voseo)
    |--------------------------------------------------------------------------
    */
    'login' => [
        'page_heading' => 'Iniciar sesión',

        /*
         * SEO / compartir: el admin (Basic Settings → SEO) puede sobreescribir meta keywords y description.
         * Si están vacíos, se usan estos textos. robots: noindex evita indexar una URL de formulario fina;
         * follow conserva el rastreo de enlaces. Cambiá a "index, follow" si querés posicionar esta URL.
         */
        'seo' => [
            'meta_description_default' => 'Iniciá sesión en tu cuenta de Tukipass para comprar entradas, ver tus reservas y administrar tus eventos. Acceso seguro desde Argentina.',
            'meta_keywords_default' => 'iniciar sesión, Tukipass, entradas eventos, mi cuenta, reservas, Argentina',
            'robots' => 'noindex, follow',
            'og_image_alt' => 'Logo de :site',
        ],

        'visual_title' => 'Tu próximo evento, a un clic.',
        'visual_subtitle' => 'Comprá entradas, gestioná tus reservas y no te pierdas nada.',

        'fallback_event_title' => 'Estás a un paso de confirmar tu entrada',
        'visual_subtitle_checkout' => 'Ingresá o seguí como invitado para terminar tu compra sin perder tu selección.',

        'event_online' => 'Evento online',
        'event_on_tukipass' => 'Evento en Tukipass',
        'online_short' => 'Online',

        'form_eyebrow' => 'Acceso a tu cuenta',
        'form_title' => 'Bienvenido de vuelta',
        'form_subtitle' => 'Ingresá a tu cuenta para continuar.',

        'form_eyebrow_checkout' => 'Último paso',
        'form_title_checkout' => 'Terminá tu compra',
        'form_subtitle_checkout' => 'Elegí si querés seguir como invitado o entrar con tu cuenta.',

        'guest_reserve_no_account' => 'Reservar entrada sin cuenta',
        'guest_buy_no_account' => 'Comprar entrada sin cuenta',
        'guest_shop_checkout' => 'Comprar como invitado — sin registrarme',

        'divider_continue_account' => 'o seguí con tu cuenta',
        'divider_email_login' => 'o ingresá con tu cuenta',

        'continue_facebook' => 'Continuar con Facebook',
        'continue_google' => 'Continuar con Google',

        'username_label' => 'Usuario',
        'username_placeholder' => 'Tu nombre de usuario',
        'password_label' => 'Contraseña',
        'password_placeholder' => 'Tu contraseña',
        'forgot_password' => '¿Olvidaste tu contraseña?',

        'submit_checkout' => 'Continuar con mi cuenta',
        'submit_login' => 'Ingresar',

        'no_account' => '¿No tenés cuenta?',
        'register_free' => 'Registrate gratis',

        'loading' => 'Por favor esperá…',
    ],

    /*
    |--------------------------------------------------------------------------
    | Registro de cliente (Argentina, voseo — público que compra entradas a eventos)
    |--------------------------------------------------------------------------
    */
    'signup' => [
        'page_heading' => 'Crear tu cuenta gratis',

        'seo' => [
            'meta_description_default' => 'Creá tu cuenta gratis en Tukipass: comprá entradas a eventos en Argentina, guardá tus tickets y gestioná tus reservas en un solo lugar.',
            'meta_keywords_default' => 'registrarse Tukipass, crear cuenta, entradas eventos Argentina, tickets, comprar entradas',
            'robots' => 'noindex, follow',
            'og_image_alt' => 'Logo de :site',
        ],

        /* Panel visual: promesa + beneficio concreto (no slogans vacíos) */
        'visual_title_line1' => 'Tu cuenta para ir a eventos',
        'visual_title_line2' => 'sin perder tiempo.',
        'visual_subtitle' => 'Registrate gratis y tené tus entradas, reservas y comprobantes juntos. Ideal si salís a shows, festivales o eventos en todo el país.',

        /* Chips de confianza: específicos y honestos */
        'stats' => [
            ['num' => 'Sin costo', 'label' => 'Registrarte'],
            ['num' => 'Un solo lugar', 'label' => 'Entradas y reservas'],
            ['num' => 'Desde el celu', 'label' => 'O la compu'],
        ],

        'logo_alt' => 'Logo de :site',

        'form_title' => 'Crear tu cuenta gratis',
        'form_subtitle' => 'Completá tus datos y empezá a comprar entradas o reservar en minutos. Sin tarjeta para registrarte.',

        'continue_facebook' => 'Continuar con Facebook',
        'continue_google' => 'Continuar con Google',
        'divider_social' => 'o registrate con tu email',

        'field_fname_label' => 'Nombre',
        'field_fname_placeholder' => 'Como figura en tu DNI',

        'field_lname_label' => 'Apellido',
        'field_lname_placeholder' => 'Tu apellido',

        'field_username_label' => 'Usuario',
        'field_username_placeholder' => 'Elegí un nombre de usuario',

        'field_email_label' => 'Email',
        'field_email_placeholder' => 'El que usás para recibir las entradas',

        'field_password_label' => 'Contraseña',
        'field_password_placeholder' => 'Al menos 6 caracteres',

        'field_password_confirm_label' => 'Repetir contraseña',
        'field_password_confirm_placeholder' => 'Volvé a escribir la contraseña',

        'password_mismatch' => 'Las contraseñas no coinciden.',

        'password_strength' => [
            'very_weak' => 'Muy débil',
            'weak' => 'Débil',
            'good' => 'Buena',
            'strong' => 'Muy fuerte',
        ],

        'submit' => 'Crear mi cuenta gratis',

        'footer_has_account' => '¿Ya tenés cuenta?',
        'footer_login' => 'Ingresá acá',
    ],
];
