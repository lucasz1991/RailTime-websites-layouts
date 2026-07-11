<?php
declare(strict_types=1);

require __DIR__ . '/Shared/config/layout-registry.php';

// Apache supplies these values through .htaccess. This fallback also lets the
// file act as the router of PHP's built-in development server.
$requestPath = rawurldecode((string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
if (PHP_SAPI === 'cli-server') {
    $localFile = __DIR__ . '/' . ltrim($requestPath, '/');
    if ($requestPath !== '/' && is_file($localFile)) {
        return false;
    }
    if ($requestPath === '/') {
        require __DIR__ . '/index.php';
        return true;
    }
}

if (!isset($_GET['layout'])) {
    if (preg_match('~/layouts/([1-7])(?:/(leistungen|ueber-uns|kontakt|impressum|datenschutz|index)(?:\.(?:html|php))?)?/?$~i', $requestPath, $match)) {
        $_GET['layout'] = $match[1];
        if (!empty($match[2]) && strtolower($match[2]) !== 'index') {
            $_GET['page'] = strtolower($match[2]);
        }
    }
}

$layoutId = filter_var($_GET['layout'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 7]]);
$page = (string)($_GET['page'] ?? 'home');
$pageMap = [
    'home' => 'index.php',
    'leistungen' => 'leistungen.php',
    'ueber-uns' => 'ueber-uns.php',
    'kontakt' => 'kontakt.php',
    'impressum' => 'impressum.php',
    'datenschutz' => 'datenschutz.php',
];

$layout = $layoutId ? rt_layout_public_by_id($layoutId) : null;
$target = ($layout && isset($pageMap[$page])) ? $layout['dir'] . '/' . $pageMap[$page] : null;

if (!$target || !is_file($target)) {
    http_response_code(404);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="de"><meta charset="utf-8"><title>Layout nicht gefunden</title><body style="font-family:Arial,sans-serif;padding:40px;background:#090c11;color:#fff"><h1>Layout nicht gefunden</h1><p>Die angeforderte Vorschau existiert nicht.</p></body></html>';
    exit;
}

$_SERVER['RT_PUBLIC_LAYOUT_ID'] = (string)$layoutId;
$_SERVER['RT_PROJECT_BASE'] = rt_project_base_url();
$_SERVER['RT_PUBLIC_LAYOUT_BASE'] = rt_project_url($layout['public_path']);
$_SERVER['RT_LAYOUT_ASSET_BASE'] = rt_project_url(str_replace('\\', '/', substr(dirname($target), strlen(__DIR__) + 1)));

require $target;
