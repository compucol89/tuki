<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Instrucciones de acceso predeterminadas
    |--------------------------------------------------------------------------
    |
    | Estas instrucciones se muestran al comprador en la página post-compra
    | y en el detalle de la reserva. Son fijas para todos los eventos porque
    | el proceso de acceso es siempre el mismo:
    |
    | 1. El cliente presenta el código QR de su entrada.
    | 2. El operador / agente de logística / taquilla lo escanea.
    | 3. Se valida el acceso y el cliente ingresa al evento.
    |
    */

    'access_instructions' => 'Presentá el código QR de tu entrada en la entrada del evento. El personal del organizador lo escaneará y validará tu acceso. ¡Listo!',

    /*
    |--------------------------------------------------------------------------
    | Identidad corporativa (fallbacks centralizados)
    |--------------------------------------------------------------------------
    |
    | ÚNICA fuente de fallback para los datos de identidad de la empresa.
    | La fuente de verdad operativa es la tabla `billing_settings` (DB, editable
    | desde Admin) y `basic_settings` para email/teléfono/dirección. Estos
    | valores SOLO se usan cuando la fila de DB no existe o está vacía.
    |
    | HARDCODE-ALLOW: literales de identidad legal obligatoria (AGENTS.md)
    | reason: textos legales exigidos por normativa argentina; no existen
    |         como datos de negocio dinámicos por organizador/evento
    | owner: plataforma Tukipass
    | scope: solo fallbacks — nunca reemplazan el valor de DB
    |
    */

    'fiscal' => [
        'issuer_name' => 'TAYRONA GROUP SAS',
        'issuer_cuit' => '30-71885087-4',
        'issuer_cuit_compact' => '30718850874',
        'issuer_iva_condition' => 'Responsable Inscripto',
        'issuer_address' => 'Pueyrredón Av. 1357, Ciudad Autónoma de Buenos Aires, Argentina',
        'support_email' => 'soporte@tukipass.com',
        'contact_email' => 'info@tukipass.com',
    ],

    'currency' => [
        'text' => 'ARS',
        'symbol' => '$',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dominios (www redirect)
    |--------------------------------------------------------------------------
    |
    | Fuente única para el middleware RedirectToWww. Override por env cuando
    | se desplega en otro dominio (staging, test, multi-tenant).
    |
    */

    'redirect_www' => [
        'bare_domain' => env('APP_BARE_DOMAIN', 'tukipass.com'),
        'www_domain' => env('APP_WWW_DOMAIN', 'www.tukipass.com'),
    ],


];
