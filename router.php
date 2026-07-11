<?php
declare(strict_types=1);

require __DIR__ . '/Shared/config/layout-registry.php';

$layoutId = filter_input(INPUT_GET, 'layout', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
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
$_SERVER['RT_PUBLIC_LAYOUT_BASE'] = '/RailTime/' . $layout['public_path'];
$_SERVER['RT_LAYOUT_ASSET_BASE'] = '/RailTime/' . str_replace('\\', '/', substr(dirname($target), strlen(__DIR__) + 1));

require $target;
