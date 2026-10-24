<?php

namespace App\Services;

/**
 * Buffer ligero de visitas de eventos.
 *
 * Escribir un contador en MySQL por cada vista toma un lock de fila (3-10 ms)
 * sobre events y serializa visitas concurrentes al mismo evento. En su lugar
 * se anota el ID en un log append-only y un comando programado agrega y vuelca
 * los contadores cada minuto.
 */
class EventViewTracker
{
    public static function record(int $eventId): void
    {
        if ($eventId < 1) {
            return;
        }

        $path = self::path();
        $dir = dirname($path);

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents($path, $eventId . "\n", FILE_APPEND | LOCK_EX);
    }

    /**
     * Toma el archivo actual de forma atómica y devuelve el path temporal.
     * Las visitas que lleguen después del rename caen en un archivo nuevo.
     */
    public static function take(): ?string
    {
        $path = self::path();

        if (!is_file($path) || filesize($path) === 0) {
            return null;
        }

        $tmp = $path . '.' . getmypid() . '.' . uniqid('', true);

        return @rename($path, $tmp) ? $tmp : null;
    }

    /**
     * @return array<int,int> event_id => visitas
     */
    public static function aggregate(string $path): array
    {
        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return [];
        }

        $counts = [];
        while (($line = fgets($handle)) !== false) {
            $id = (int) trim($line);
            if ($id > 0) {
                $counts[$id] = ($counts[$id] ?? 0) + 1;
            }
        }

        fclose($handle);

        return $counts;
    }

    public static function path(): string
    {
        return storage_path('framework/cache/event-views.log');
    }
}
