<?php

$publicPath = realpath(__DIR__ . '/public');
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$requestedFile = realpath($publicPath . rawurldecode($requestPath));

if ($requestedFile !== false
    && str_starts_with($requestedFile, $publicPath . DIRECTORY_SEPARATOR)
    && is_file($requestedFile)
) {
    $extension = strtolower(pathinfo($requestedFile, PATHINFO_EXTENSION));
    $cacheableExtensions = [
        'avif', 'css', 'eot', 'gif', 'ico', 'jpg', 'jpeg', 'js', 'otf', 'png',
        'svg', 'ttf', 'webp', 'woff', 'woff2',
    ];
    $mimeTypes = [
        'avif' => 'image/avif',
        'css' => 'text/css; charset=UTF-8',
        'eot' => 'application/vnd.ms-fontobject',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'js' => 'application/javascript; charset=UTF-8',
        'otf' => 'font/otf',
        'png' => 'image/png',
        'svg' => 'image/svg+xml',
        'ttf' => 'font/ttf',
        'webp' => 'image/webp',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
    ];

    if (in_array($extension, $cacheableExtensions, true)) {
        $lastModified = filemtime($requestedFile);
        $etag = '"' . md5($requestedFile . '|' . $lastModified . '|' . filesize($requestedFile)) . '"';

        header('Content-Type: ' . ($mimeTypes[$extension] ?? 'application/octet-stream'));
        header('Cache-Control: public, max-age=31536000, immutable');
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
        header('ETag: ' . $etag);

        $ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        $ifModifiedSince = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '') ?: 0;
        if ($ifNoneMatch === $etag || $ifModifiedSince >= $lastModified) {
            http_response_code(304);
            exit;
        }

        header('Content-Length: ' . filesize($requestedFile));

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
            readfile($requestedFile);
        }
        exit;
    }

    return false;
}

require __DIR__ . '/public/index.php';
