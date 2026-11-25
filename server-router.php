<?php

/**
 * Router untuk PHP built-in server (pengganti `php artisan serve`).
 * Menambahkan header Cache-Control pada aset statis (/build, /fonts) agar
 * browser tidak mengunduh ulang font/CSS/JS di setiap kunjungan.
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$file = __DIR__.'/public'.$uri;

if ($uri !== '/' && is_file($file)) {
    $isBuild = str_starts_with($uri, '/build/');
    $isFont = str_starts_with($uri, '/fonts/');

    if (($isBuild || $isFont) && ! str_contains($uri, '..')) {
        $mimes = [
            'css' => 'text/css; charset=UTF-8',
            'js' => 'application/javascript; charset=UTF-8',
            'json' => 'application/json',
            'woff2' => 'font/woff2',
            'woff' => 'font/woff',
        ];
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (isset($mimes[$ext])) {
            header('Content-Type: '.$mimes[$ext]);
            header('Cache-Control: public, max-age='.($isBuild ? '31536000, immutable' : '2592000'));
            header('Content-Length: '.filesize($file));
            readfile($file);

            return true;
        }
    }

    return false; // file statis lain dilayani langsung oleh PHP
}

require_once __DIR__.'/public/index.php';
