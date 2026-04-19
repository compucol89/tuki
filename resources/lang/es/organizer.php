<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Organizadores — login, registro (Argentina, voseo)
    |--------------------------------------------------------------------------
    */
    'login' => [
        'page_heading' => 'Ingreso de organizador',

        'seo' => [
            'meta_description_default' => 'Ingresá al panel de organizador de Tukipass: gestioná eventos, ventas de entradas y asistentes desde un solo lugar en Argentina.',
            'meta_keywords_default' => 'organizador Tukipass, panel productor, vender entradas, eventos Argentina, login organizador',
            'robots' => 'noindex, follow',
            'og_image_alt' => 'Logo de :site',
        ],

        /* Hero: problema → resultado (valor para quien produce eventos) */
        'visual_title_line1' => 'Vendé más entradas',
        'visual_title_line2' => 'con menos vueltas.',
        'visual_subtitle' => 'Publicá tu evento, cobrá online y llevá ventas y asistentes en un solo panel. Pensado para productores, espacios y agencias en Argentina.',

        'stats' => [
            ['num' => 'Ventas online', 'label' => 'Entradas y reservas'],
            ['num' => 'Cobrá en pesos', 'label' => 'Pagos integrados'],
            ['num' => 'Tu operación', 'label' => 'Reportes y control'],
        ],

        'logo_alt' => 'Logo de :site',

        'form_eyebrow' => 'Panel de organizador',
        'form_title' => 'Bienvenido de vuelta',
        'form_subtitle' => 'Ingresá para gestionar eventos, ventas y asistentes. ¿Primera vez? Podés crear tu cuenta gratis y empezar cuando quieras.',

        'username_label' => 'Usuario',
        'username_placeholder' => 'Tu usuario de organizador',
        'password_label' => 'Contraseña',
        'password_placeholder' => 'Tu contraseña',
        'forgot_password' => '¿Olvidaste tu contraseña?',

        'submit' => 'Ingresar al panel',
        'loading' => 'Por favor esperá…',

        'footer_no_account' => '¿Todavía no tenés cuenta de organizador?',
        'footer_signup' => 'Creala gratis y empezá a vender',
    ],

    /*
    |--------------------------------------------------------------------------
    | Registro de organizador (Argentina, voseo — product-led, outcome-first)
    |--------------------------------------------------------------------------
    */
    'signup' => [
        'page_heading' => 'Alta de organizador',

        'seo' => [
            'meta_description_default' => 'Creá tu cuenta de organizador en Tukipass: publicá eventos, vendé entradas online y llevá ventas y asistentes desde un solo panel en Argentina.',
            'meta_keywords_default' => 'registro organizador Tukipass, vender entradas, panel productor, eventos Argentina',
            'robots' => 'noindex, follow',
            'og_image_alt' => 'Logo de :site',
        ],

        'visual_title_line1' => 'Vendé con claridad',
        'visual_title_line2' => 'sin perder el control.',
        'visual_subtitle' => 'Publicá tu evento, cobrá en pesos y llevá ventas y asistentes en un solo lugar. Menos idas y vueltas, más tiempo para lo que importa: tu show.',

        'stats' => [
            ['num' => 'Alta gratis', 'label' => 'Sin costo de registro'],
            ['num' => 'Cobrá online', 'label' => 'Pagos integrados'],
            ['num' => 'Un solo panel', 'label' => 'Ventas y reportes'],
        ],

        'stats_aria_label' => 'Por qué registrarte como organizador',

        'aria_toggle_password' => 'Mostrar u ocultar contraseña',

        'logo_alt' => 'Logo de :site',

        'form_title' => 'Creá tu cuenta de organizador',
        'form_subtitle' => 'Completá tus datos y en minutos seguís al panel para cargar tu evento. Sin tarjeta para registrarte: solo lo necesario para operar.',

        'field_name_label' => 'Nombre completo',
        'field_name_placeholder' => 'Como figura en tu documento',

        'field_username_label' => 'Usuario',
        'field_username_placeholder' => 'Elegí un usuario único',

        'field_email_label' => 'Email',
        'field_email_placeholder' => 'El que usás para avisos y facturación',

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

        'submit' => 'Crear mi cuenta y continuar',
        'loading' => 'Por favor esperá…',

        'footer_has_account' => '¿Ya tenés cuenta de organizador?',
        'footer_login' => 'Ingresá acá',
    ],
];
