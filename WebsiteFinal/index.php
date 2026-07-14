<?php
declare(strict_types=1);

require_once __DIR__ . '/src/bootstrap.php';
require_once __DIR__ . '/src/contact.php';
require_once __DIR__ . '/src/pages/home.php';
require_once __DIR__ . '/src/pages/subpages.php';

wf_send_security_headers();

function wf_request_path(): string
{
    $uriPath = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?? '/');
    $decoded = rawurldecode($uriPath);
    $base = wf_base_path();

    if ($base !== '' && ($decoded === $base || str_starts_with($decoded, $base . '/'))) {
        $decoded = substr($decoded, strlen($base));
    }

    $decoded = preg_replace('~/+~', '/', str_replace('\\', '/', $decoded)) ?? '/';
    return trim($decoded, '/');
}

function wf_redirect_to_route(string $route, int $status = 301): never
{
    header('Location: ' . wf_route_url($route), true, $status);
    exit;
}

function wf_render_robots(): never
{
    header('Content-Type: text/plain; charset=UTF-8');
    $noindex = filter_var(getenv('RAILTIME_NOINDEX') ?: '0', FILTER_VALIDATE_BOOL);
    if ($noindex) {
        echo "User-agent: *\nDisallow: /\n";
    } else {
        echo "User-agent: *\nAllow: /\nSitemap: " . wf_site_url('sitemap.xml') . "\n";
    }
    exit;
}

function wf_render_sitemap(): never
{
    header('Content-Type: application/xml; charset=UTF-8');
    $routes = ['home', 'services', 'about', 'contact'];
    foreach (['imprint', 'privacy'] as $legalRoute) {
        if (wf_legal_is_complete($legalRoute)) {
            $routes[] = $legalRoute;
        }
    }
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($routes as $route) {
        $meta = wf_meta($route);
        echo '  <url><loc>' . wf_escape(wf_site_url($meta['path'])) . "</loc></url>\n";
    }
    echo "</urlset>\n";
    exit;
}

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$path = wf_request_path();

if (str_contains($path, "\0") || preg_match('~(?:^|/)\.\.(?:/|$)~', $path)) {
    http_response_code(400);
    exit('Ungültige Anfrage.');
}

if (preg_match('~^(?:config|src)(?:/|$)~i', $path)) {
    http_response_code(403);
    exit('Zugriff verweigert.');
}

if ($method === 'GET' || $method === 'HEAD') {
    $legacyRoutes = [
        'index.php' => 'home',
        'index.html' => 'home',
        'home' => 'home',
        'leistungen.php' => 'services',
        'leistungen.html' => 'services',
        'ueber-uns.php' => 'about',
        'ueber-uns.html' => 'about',
        'kontakt.php' => 'contact',
        'kontakt.html' => 'contact',
        'impressum.php' => 'imprint',
        'impressum.html' => 'imprint',
        'datenschutz.php' => 'privacy',
        'datenschutz.html' => 'privacy',
    ];
    $lowerPath = strtolower($path);
    if (isset($legacyRoutes[$lowerPath])) {
        wf_redirect_to_route($legacyRoutes[$lowerPath]);
    }
    if ($path !== '' && str_ends_with((string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH) ?? ''), '/')) {
        $cleanRoutes = array_flip(array_filter(wf_route_map(), static fn(string $routePath): bool => $routePath !== '' && $routePath !== 'kontakt/senden'));
        if (isset($cleanRoutes[$lowerPath])) {
            wf_redirect_to_route($cleanRoutes[$lowerPath]);
        }
    }
    if ($path !== $lowerPath && in_array($lowerPath, wf_route_map(), true)) {
        $route = array_search($lowerPath, wf_route_map(), true);
        if (is_string($route)) {
            wf_redirect_to_route($route);
        }
    }
}

if ($path === 'robots.txt' && ($method === 'GET' || $method === 'HEAD')) {
    wf_render_robots();
}
if ($path === 'sitemap.xml' && ($method === 'GET' || $method === 'HEAD')) {
    wf_render_sitemap();
}
if ($path === 'kontakt/senden') {
    wf_handle_contact_submission();
}

$routes = [
    '' => ['home', 'wf_home'],
    'leistungen' => ['services', 'wf_services'],
    'ueber-uns' => ['about', 'wf_about'],
    'kontakt' => ['contact', 'wf_contact'],
    'impressum' => ['imprint', static fn() => wf_legal('imprint')],
    'datenschutz' => ['privacy', static fn() => wf_legal('privacy')],
];

if (($method === 'GET' || $method === 'HEAD') && isset($routes[$path])) {
    [$route, $renderer] = $routes[$path];
    $GLOBALS['wf_route'] = $route;
    $renderer();
    exit;
}

$GLOBALS['wf_route'] = 'not_found';
http_response_code(404);
wf_not_found();
