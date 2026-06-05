<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'base_url' => 'https://api.openai.com/v1',
    'model' => env('OPENAI_IMAGE_MODEL', 'gpt-image-2'),
    'timeout' => (int) env('OPENAI_TIMEOUT', 150),
    'max_retries' => 3,
    'queue' => 'ai-images',
    'smart_crop_mode' => env('AI_IMAGES_SMART_CROP_MODE', true),
    'hybrid_mode' => env('AI_IMAGES_USE_HYBRID_MODE', false),
    'use_alpha_mask' => env('AI_IMAGES_USE_ALPHA_MASK', true),
    'ssim_threshold' => (float) env('AI_IMAGES_SSIM_THRESHOLD', 0.99),

    'formats' => [
        'square' => [
            'size' => '1024x1024',
            'slot' => 'thumbnail',
            'output_filename' => 'square.png',
        ],
        'gallery' => [
            'size' => '1536x1024',
            'slot' => 'event_images',
            'output_filename' => 'gallery.png',
        ],
        'og' => [
            'size' => '1536x1024',
            'slot' => 'og_image',
            'output_filename' => 'og.png',
        ],
    ],

    'prompts' => [
        'square' => 'Mantén EXACTAMENTE el estilo visual, paleta de colores, tipografía, '
                  . 'composición y mood de la imagen de referencia. Adapta el contenido '
                  . 'a formato cuadrado 1:1 (1024x1024) optimizado para tarjeta de home '
                  . 'de plataforma de eventos. NO agregues texto nuevo. NO recortes '
                  . 'elementos importantes.',
        'gallery' => 'Mantén EXACTAMENTE el estilo visual, paleta, composición y mood '
                   . 'de la imagen de referencia. Reformatea a landscape 3:2 (1536x1024) '
                   . 'optimizado para galería y hero de evento. Conserva el flyer '
                   . 'completo sin recortar. NO agregues texto nuevo.',
        'og' => 'Mantén EXACTAMENTE el estilo visual, paleta y mood de la imagen de '
              . 'referencia. Reformatea a landscape 3:2 (1536x1024) optimizado para '
              . 'Open Graph / preview en redes sociales (Facebook, Twitter, LinkedIn, '
              . 'WhatsApp). NO agregues texto nuevo. El sujeto principal debe estar '
              . 'centrado horizontalmente porque las redes croppean a 1.91:1 (1200x630) '
              . 'al mostrar el preview.',
    ],

    'reference' => [
        'min_dimension' => 512,
        'max_dimension' => 4096,
        'max_size_kb' => 10240,
        'allowed_mimes' => ['image/png', 'image/jpeg', 'image/webp'],
    ],
];
