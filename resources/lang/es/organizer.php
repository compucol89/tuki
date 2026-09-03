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
        'visual_title_line1' => 'Vendé entradas',
        'visual_title_line2' => 'sin perder el control.',
        'visual_subtitle' => 'Publicá eventos, cobrá online y seguí cada reserva desde un solo panel. Para productores, espacios y agencias en Argentina.',

        'stats' => [
            ['icon' => 'fas fa-ticket-alt', 'num' => 'Publicá el evento', 'label' => 'Configurá entradas y reservas online'],
            ['icon' => 'fas fa-credit-card', 'num' => 'Cobrá en pesos', 'label' => 'Pagos integrados al flujo de reserva'],
            ['icon' => 'fas fa-chart-line', 'num' => 'Controlá ventas', 'label' => 'Asistentes y reportes en un solo panel'],
        ],

        'stats_aria_label' => 'Qué podés gestionar desde el panel de organizadores',

        'logo_alt' => 'Logo de :site',

        'form_eyebrow' => 'Panel de organizador',
        'form_title' => 'Bienvenido de vuelta',
        'form_subtitle' => 'Entrá a tu panel para revisar reservas, ventas y asistentes. Si todavía no tenés cuenta, podés crearla gratis.',

        'username_label' => 'Usuario',
        'username_placeholder' => 'Nombre de usuario',
        'password_label' => 'Contraseña',
        'password_placeholder' => 'Contraseña del panel',
        'forgot_password' => '¿Olvidaste tu contraseña?',
        'validation' => [
            'username_required' => 'Ingresá tu usuario para entrar al panel.',
            'password_required' => 'Ingresá tu contraseña para continuar.',
        ],

        'submit' => 'Ingresar al panel',
        'loading' => 'Por favor esperá…',

        'footer_no_account' => '¿Primera vez como organizador?',
        'footer_signup' => 'Crear cuenta gratis',
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
            ['icon' => 'fas fa-calendar-check', 'num' => 'Alta gratis', 'label' => 'Sin costo de registro'],
            ['icon' => 'fas fa-credit-card', 'num' => 'Cobrá online', 'label' => 'Pagos integrados'],
            ['icon' => 'fas fa-chart-line', 'num' => 'Un solo panel', 'label' => 'Ventas y reportes'],
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
        'field_password_placeholder' => 'Mín. 6 caracteres',

        'field_password_confirm_label' => 'Repetir contraseña',
        'field_password_confirm_placeholder' => 'Volvé a escribirla',

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

    'flash' => [
        'verification_mail_sent' => 'Te mandamos un correo de verificación. Revisá tu bandeja de entrada y, por las dudas, también la carpeta de spam o correo no deseado.',
        'mail_not_sent' => 'No se pudo enviar el correo.',
        'signup_success' => 'Registro completado exitosamente. Por favor, iniciá sesión.',
        'mail_sent' => 'Se envió un correo a tu dirección de email.',
        'password_reset' => 'Tu contraseña fue restablecida.',
        'password_updated' => 'Contraseña actualizada correctamente.',
        'profile_updated' => 'Perfil actualizado correctamente.',
        'added_successfully' => 'Agregado correctamente.',
        'updated_successfully' => 'Actualizado correctamente.',
        'deleted_successfully' => 'Eliminado correctamente.',
        'email_verification_status_updated' => 'Estado de verificación de email actualizado correctamente.',
        'payment_status_updated_mail_sent' => 'Estado de pago actualizado y correo enviado correctamente.',
        'booking_deleted_successfully' => 'Reserva eliminada correctamente.',
        'ticket_updated_successfully' => 'Entrada del evento actualizada correctamente.',
        'message_sent_successfully' => 'Mensaje enviado correctamente.',
        'withdraw_request_sent' => 'Solicitud de retiro enviada correctamente.',
        'withdraw_request_deleted' => 'Solicitud de retiro eliminada correctamente.',

        'verify_email_alert' => 'Por favor, verificá tu dirección de email.',
        'login_error' => 'Ups, el usuario o la contraseña no coinciden.',
    ],

    'captcha' => [
        'required' => 'Por favor, verificá que no sos un robot.',
        'error' => 'Error de captcha. Intentá de nuevo más tarde o contactá al administrador del sitio.',
    ],

    'validation' => [
        'password_confirmation' => 'La confirmación de contraseña no coincide.',
        'confirm_new_password_required' => 'El campo de confirmación de contraseña es obligatorio.',
        'name_required_for_language' => 'El campo nombre es obligatorio para el idioma :language.',
    ],

    'qrcode' => [
        'verified' => 'Verificado',
        'already_scanned' => 'Ya escaneado',
        'payment_incomplete' => 'Pago incompleto',
        'payment_rejected' => 'Pago rechazado',
        'no_permission' => 'No tenés permiso',
        'unverified' => 'No verificado',
    ],
];
