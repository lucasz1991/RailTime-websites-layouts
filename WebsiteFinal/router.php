<?php
declare(strict_types=1);

$path = rawurldecode((string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/'));
$path = preg_replace('~/+~', '/', str_replace('\\', '/', $path)) ?? '/';
if (str_contains($path, "\0") || preg_match('~(?:^|/)\.\.(?:/|$)|/(?:config|src)(?:/|$)|/(?:\.(?!well-known(?:/|$)))~i', $path)) {
    http_response_code(403);
    exit('Zugriff verweigert.');
}

$root = realpath(__DIR__);
$file = realpath(__DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $path));
if ($path !== '/' && $root !== false && $file !== false && str_starts_with($file, $root . DIRECTORY_SEPARATOR) && is_file($file)) {
    return false;
}

require __DIR__ . '/index.php';
