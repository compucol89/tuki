<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ARCA / AFIP Integration
    |--------------------------------------------------------------------------
    |
    | Configuración para la integración con ARCA (ex AFIP) para emisión de
    | comprobantes electrónicos.
    |
    | Modelo fiscal en revisión: Tayrona Group SAS emite preview al cliente
    | comprador. Comisión vs total cobrado y tipo de comprobante quedan
    | pendientes de validación contable antes de habilitar emisión real.
    |
    */

    /*
    | Feature Flags
    */
    'enable_issuing' => env('ARCA_ENABLE_ISSUING', false),
    'enable_preview' => env('ARCA_ENABLE_PREVIEW', true),

    /*
    | Environment: 'homologation' (testing) | 'production'
    */
    'environment' => env('ARCA_ENVIRONMENT', 'homologation'),

    /*
    | Credenciales de acceso
    */
    'cuit' => env('ARCA_CUIT', ''),
    'certificate' => env('ARCA_CERT_PATH', ''),
    'private_key' => env('ARCA_KEY_PATH', ''),
    'passphrase' => env('ARCA_CERT_PASSPHRASE', ''),

    /*
    | Endpoints WSAA (Web Service de Autorización)
    */
    'wsaa' => [
        'homologation' => 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms',
        'production'   => 'https://wsaa.afip.gov.ar/ws/services/LoginCms',
    ],

    /*
    | Endpoints WSFEv1 (Web Service de Factura Electrónica)
    */
    'wsfe' => [
        'homologation' => 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx',
        'production'   => 'https://servicios1.afip.gov.ar/wsfev1/service.asmx',
    ],

    /*
    | Punto de venta y tipo de comprobante
    |
    | punto_venta: número asignado por ARCA/AFIP (ej: 0001)
    | tipo_comprobante: pendiente de validación contable para Tayrona RI.
    | El valor actual se conserva bloqueado por ARCA_ENABLE_ISSUING=false.
    | tipo_documento: 96 = DNI, 80 = CUIT
    | moneda: PESO = Moneda local
    */
    'punto_venta' => env('ARCA_PUNTO_VENTA', 1),
    'tipo_comprobante' => 6,
    'tipo_documento' => 96,
    'moneda' => 'PES',
    'moneda_ctz' => 1,

    /*
    | Conceptos para WSFEv1
    | 1 = Productos, 2 = Servicios, 3 = Productos y Servicios
    */
    'concepto' => 2,

    /*
    | IVA por defecto para la comisión
    | 5 = 21% (responsable inscripto)
    */
    'iva_id' => 5,
    'default_commission_rate' => env('ARCA_DEFAULT_COMMISSION_RATE', 0.10),
    'default_vat_rate' => env('ARCA_DEFAULT_VAT_RATE', 0),
    'invoice_model' => env('ARCA_INVOICE_MODEL', 'customer_service_fee_invoice'),
    'issuer_name' => env('ARCA_ISSUER_NAME', ''),

    /*
    | Directorio para almacenar tickets de acceso (TA)
    */
    'ta_storage' => storage_path('framework/arca/ta'),
];
