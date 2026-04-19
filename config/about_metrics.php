<?php

declare(strict_types=1);

/**
 * Indicadores mostrados en /sobre-nosotros (columna izquierda).
 * Valores placeholder: reemplazar por datos reales o por consultas a la BD cuando existan.
 *
 * label_key debe existir en resources/lang/{locale}.json
 */
return [
    'enabled' => true,

    /**
     * Visualizaciones SVG (solo estética / storytelling — no escalan literalmente a los números).
     * hero_bars: alturas 0–100 para 6 columnas (semestre / trimestre ilustrativo).
     * sparkline: 12 puntos 0–100 (tendencia indicativa).
     * meters: una barra de “intensidad relativa” por cada estadística secundaria (índices 0 = 1.er stat tras el héroe).
     */
    'visual' => [
        'hero_bars' => [38, 52, 71, 64, 88, 76],
        'sparkline' => [18, 22, 19, 28, 35, 32, 41, 48, 55, 62, 58, 70],
        'meters' => [88, 72, 65],
    ],

    'stats' => [
        [
            'value' => '3.200+',
            'label_key' => 'about_metrics_label_events_bsas',
        ],
        [
            'value' => '486.000+',
            'label_key' => 'about_metrics_label_tickets_year',
        ],
        [
            'value' => '1.050+',
            'label_key' => 'about_metrics_label_organizers',
        ],
        [
            'value' => '78',
            'label_key' => 'about_metrics_label_weekend_avg',
        ],
    ],
];
