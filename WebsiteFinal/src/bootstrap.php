<?php
declare(strict_types=1);

const WF_ROOT = __DIR__ . '/..';

function wf_config(): array
{
    static $config;
    return $config ??= require WF_ROOT . '/config/site.php';
}

function wf_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function wf_is_https(): bool
{
    if (($_SERVER['HTTPS'] ?? '') !== '' && strtolower((string)$_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    $forwarded = strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    return str_starts_with($forwarded, 'https');
}

function wf_send_security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()');
    header('Cross-Origin-Opener-Policy: same-origin');
    header_remove('X-Powered-By');
    if (wf_is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

function wf_base_path(): string
{
    static $base;
    if ($base !== null) {
        return $base;
    }
    $script = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $directory = rtrim(str_replace('\\', '/', dirname($script)), '/.');
    $base = $directory === '' || $directory === '/' ? '' : '/' . ltrim($directory, '/');
    return $base;
}

function wf_encode_path(string $path): string
{
    $segments = array_filter(explode('/', trim($path, '/')), static fn(string $segment): bool => $segment !== '');
    return implode('/', array_map('rawurlencode', $segments));
}

function wf_url(string $path = ''): string
{
    $encoded = wf_encode_path($path);
    return wf_base_path() . ($encoded === '' ? '/' : '/' . $encoded);
}

function wf_site_url(string $path = ''): string
{
    $base = wf_config()['site_url'];
    $encoded = wf_encode_path($path);
    return $base . ($encoded === '' ? '/' : '/' . $encoded);
}

function wf_asset(string $path): string
{
    $relative = ltrim($path, '/');
    $file = WF_ROOT . '/' . $relative;
    $version = is_file($file) ? (string)filemtime($file) : '1';
    return wf_url($relative) . '?v=' . rawurlencode($version);
}

function wf_route_map(): array
{
    return [
        'home' => '',
        'services' => 'leistungen',
        'about' => 'ueber-uns',
        'contact' => 'kontakt',
        'contact_submit' => 'kontakt/senden',
        'imprint' => 'impressum',
        'privacy' => 'datenschutz',
    ];
}

function wf_current_route(): string
{
    return (string)($GLOBALS['wf_route'] ?? 'home');
}

function wf_route_url(string $route, string $fragment = ''): string
{
    $map = wf_route_map();
    $url = wf_url($map[$route] ?? '');
    if ($fragment !== '') {
        $url .= '#' . rawurlencode($fragment);
    }
    return $url;
}

function rt_project_url(string $path = ''): string
{
    return wf_url($path);
}

function rt_image(string $file): string
{
    return wf_url('assets/images/' . $file);
}

function rt_image_size_attrs(string $file): string
{
    $path = WF_ROOT . '/assets/images/' . ltrim($file, '/');
    $size = is_file($path) ? @getimagesize($path) : false;
    if (!is_array($size) || empty($size[0]) || empty($size[1])) {
        return '';
    }
    return ' width="' . (int)$size[0] . '" height="' . (int)$size[1] . '"';
}

function rt_video(string $file): string
{
    return wf_url('assets/video/' . $file);
}

function rt_shared_content(): array
{
    return wf_config();
}

function rt_logo_lockup(string $variant = 'default', string $design = 'd2', bool $darkBackground = false): void
{
    $variant = preg_replace('/[^a-z0-9_-]/i', '', $variant) ?: 'default';
    ?>
<span class="rt-brand-lockup rt-brand-lockup--<?= wf_escape($variant) ?>" role="img" aria-label="RT Rail Time GmbH">
    <img class="rt-brand-lockup__mark" src="<?= wf_escape(wf_url('assets/logo/rt-logo.svg')) ?>" alt="" width="512" height="512" aria-hidden="true">
    <img class="rt-brand-lockup__wordmark" src="<?= wf_escape(wf_url('assets/images/logo-txt-darkbg.png')) ?>" alt="" width="760" height="133" aria-hidden="true">
</span>
<?php
}

function rt_video_poster(array $rt): string
{
    return rt_image($rt['assets']['hero_poster']);
}

function rt_video_attrs(array $rt, bool $autoplay = true, bool $loop = false, string $preload = 'metadata'): string
{
    $poster = rt_video_poster($rt);
    $preload = in_array($preload, ['none', 'metadata', 'auto'], true) ? $preload : 'metadata';
    $attrs = [
        'muted',
        'playsinline',
        'preload="' . $preload . '"',
        'poster="' . wf_escape($poster) . '"',
        'data-hero-video',
        'data-hero-poster="' . wf_escape($poster) . '"',
    ];
    if ($autoplay) {
        $attrs[] = 'autoplay';
    }
    if ($loop) {
        $attrs[] = 'loop';
    }
    return implode(' ', $attrs);
}

function wf_meta(string $route): array
{
    $seo = wf_config()['seo'];
    return $seo[$route] ?? $seo['not_found'];
}

function wf_legal_is_complete(string $route): bool
{
    $required = [
        'imprint' => ['managing_director', 'register_court', 'register_number', 'vat_id', 'liability_insurer', 'liability_insurer_address'],
        'privacy' => ['hosting_provider', 'server_log_retention'],
    ][$route] ?? [];
    if ($required === []) {
        return false;
    }
    $legal = wf_config()['legal'];
    foreach ($required as $key) {
        if (trim((string)($legal[$key] ?? '')) === '') {
            return false;
        }
    }
    return true;
}

function wf_json_ld(string $route, array $meta): array
{
    $config = wf_config();
    $siteUrl = $config['site_url'];
    $canonical = wf_site_url($meta['path']);
    $organizationId = $siteUrl . '/#organization';
    $websiteId = $siteUrl . '/#website';
    $pageId = $canonical . '#webpage';
    $address = $config['postal_address'];

    $graph = [
        [
            '@type' => 'Organization',
            '@id' => $organizationId,
            'name' => $config['company'],
            'url' => wf_site_url(),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => wf_site_url('assets/images/logo-horizontal-darkbg.png'),
            ],
            'email' => $config['email'],
            'telephone' => $config['phone_href'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $address['street'],
                'postalCode' => $address['postal_code'],
                'addressLocality' => $address['locality'],
                'addressCountry' => $address['country'],
            ],
            'areaServed' => ['@type' => 'Country', 'name' => 'Deutschland'],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => $config['phone_href'],
                'email' => $config['email'],
                'contactType' => 'Kundenservice und Notfalldienst',
                'areaServed' => 'DE',
                'availableLanguage' => ['de'],
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => $websiteId,
            'url' => wf_site_url(),
            'name' => $config['site_name'],
            'inLanguage' => $config['language'],
            'publisher' => ['@id' => $organizationId],
        ],
        [
            '@type' => $meta['schema_type'],
            '@id' => $pageId,
            'url' => $canonical,
            'name' => $meta['title'],
            'description' => $meta['description'],
            'inLanguage' => $config['language'],
            'isPartOf' => ['@id' => $websiteId],
            'about' => ['@id' => $organizationId],
            'primaryImageOfPage' => [
                '@type' => 'ImageObject',
                'url' => wf_site_url('assets/images/' . $meta['image']),
            ],
        ],
    ];

    if ($route !== 'home' && $route !== 'not_found') {
        $graph[] = [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Startseite', 'item' => wf_site_url()],
                ['@type' => 'ListItem', 'position' => 2, 'name' => preg_replace('/\s+\|.*$/u', '', $meta['title']), 'item' => $canonical],
            ],
        ];
    }

    if ($route === 'services') {
        $items = [];
        foreach ($config['services'] as $index => $service) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'item' => [
                    '@type' => 'Service',
                    '@id' => wf_site_url('leistungen') . '#' . $service['slug'],
                    'name' => $service['title'],
                    'description' => $service['copy'],
                    'provider' => ['@id' => $organizationId],
                    'areaServed' => ['@type' => 'Country', 'name' => 'Deutschland'],
                ],
            ];
        }
        $graph[] = [
            '@type' => 'ItemList',
            '@id' => $canonical . '#leistungen',
            'name' => 'Leistungen der RT Rail Time GmbH',
            'itemListElement' => $items,
        ];
    }

    return ['@context' => 'https://schema.org', '@graph' => $graph];
}

function wf_breadcrumb(string $route): void
{
    if ($route === 'home' || $route === 'not_found') {
        return;
    }
    $label = [
        'services' => 'Leistungen',
        'about' => 'Über uns',
        'contact' => 'Kontakt',
        'imprint' => 'Impressum',
        'privacy' => 'Datenschutz',
    ][$route] ?? '';
    ?>
<nav class="rt-breadcrumb" aria-label="Brotkrümelnavigation">
    <ol><li><a href="<?= wf_escape(wf_route_url('home')) ?>">Startseite</a></li><li aria-current="page"><?= wf_escape($label) ?></li></ol>
</nav>
<?php
}

function rt_document_start(string $legacyTitle, int $theme, bool $home = false): array
{
    $rt = wf_config();
    $route = wf_current_route();
    $meta = wf_meta($route);
    $canonical = wf_site_url($meta['path']);
    $robots = (string)($meta['robots'] ?? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1');
    if (in_array($route, ['imprint', 'privacy'], true) && wf_legal_is_complete($route)) {
        $robots = 'index,follow,max-image-preview:large,max-snippet:-1';
    }
    if (filter_var(getenv('RAILTIME_NOINDEX') ?: '0', FILTER_VALIDATE_BOOL)) {
        $robots = 'noindex,nofollow';
    }
    $ogImage = wf_site_url('assets/images/' . $meta['image']);
    ?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<title><?= wf_escape($meta['title']) ?></title>
<meta name="description" content="<?= wf_escape($meta['description']) ?>">
<meta name="robots" content="<?= wf_escape($robots) ?>">
<meta name="author" content="RT Rail Time GmbH">
<meta name="theme-color" content="#090c11">
<meta name="format-detection" content="telephone=yes">
<meta name="geo.region" content="DE-NI">
<meta name="geo.placename" content="Winsen (Luhe)">
<link rel="canonical" href="<?= wf_escape($canonical) ?>">
<link rel="alternate" hreflang="de-DE" href="<?= wf_escape($canonical) ?>">
<link rel="alternate" hreflang="x-default" href="<?= wf_escape($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="de_DE">
<meta property="og:site_name" content="RT Rail Time GmbH">
<meta property="og:url" content="<?= wf_escape($canonical) ?>">
<meta property="og:title" content="<?= wf_escape($meta['title']) ?>">
<meta property="og:description" content="<?= wf_escape($meta['description']) ?>">
<meta property="og:image" content="<?= wf_escape($ogImage) ?>">
<meta property="og:image:alt" content="<?= wf_escape($meta['image_alt']) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= wf_escape($meta['title']) ?>">
<meta name="twitter:description" content="<?= wf_escape($meta['description']) ?>">
<meta name="twitter:image" content="<?= wf_escape($ogImage) ?>">
<meta name="twitter:image:alt" content="<?= wf_escape($meta['image_alt']) ?>">
<link rel="icon" type="image/svg+xml" href="<?= wf_escape(wf_asset('assets/icons/favicon.svg')) ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= wf_escape(wf_asset('assets/icons/favicon-32x32.png')) ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= wf_escape(wf_asset('assets/icons/apple-touch-icon.png')) ?>">
<link rel="manifest" href="<?= wf_escape(wf_asset('site.webmanifest')) ?>">
<?php if ($home): ?>
<link rel="preload" href="<?= wf_escape(wf_url('assets/images/start3-first-frame.png')) ?>" as="image" fetchpriority="high">
<link rel="preload" href="<?= wf_escape(wf_url('assets/models/rt-logo.glb')) ?>" as="fetch" type="model/gltf-binary" crossorigin>
<?php endif ?>
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/design-system.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/layout-polish.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/layout.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/motion-stability.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/brand-lockup.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/mobile-navigation.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/logo-3d.css')) ?>">
<link rel="stylesheet" href="<?= wf_escape(wf_asset('assets/css/standalone.css')) ?>">
<script>
(function(){if(matchMedia('(prefers-reduced-motion: reduce)').matches)return;var r=document.documentElement;r.classList.add('rt-motion-enabled','rt-motion-pending');window.__rtMotionCloakTimer=setTimeout(function(){r.classList.remove('rt-motion-enabled','rt-motion-pending')},4000)})();
</script>
<script type="application/ld+json"><?= json_encode(wf_json_ld($route, $meta), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
</head>
<body class="rt-shell theme-3<?= $home ? ' is-home' : ' is-subpage' ?>" data-page="<?= wf_escape($route) ?>">
<a class="rt-skip-link" href="#main-content">Direkt zum Inhalt</a>
<?php
    rt_navigation($rt, $home);
    wf_breadcrumb($route);
    return $rt;
}

function rt_navigation(array $rt, bool $home): void
{
    $current = wf_current_route();
    ?>
<header class="rt-nav<?= $home ? '' : ' is-visible' ?>" data-site-navigation>
    <a class="rt-nav__brand" href="<?= wf_escape(wf_route_url('home')) ?>" aria-label="RT Rail Time GmbH – Startseite"><?php rt_logo_lockup('nav', 'd2', true); ?></a>
    <button class="rt-nav__toggle" type="button" aria-label="Menü öffnen" aria-controls="main-navigation" aria-expanded="false"><span></span><span></span></button>
    <nav id="main-navigation" aria-label="Hauptnavigation">
        <?php foreach ($rt['navigation'] as $item): ?>
        <a href="<?= wf_escape(wf_route_url($item['route'])) ?>"<?= $current === $item['route'] ? ' aria-current="page"' : '' ?>><?= wf_escape($item['label']) ?></a>
        <?php endforeach ?>
    </nav>
    <a class="rt-nav__phone" href="tel:<?= wf_escape($rt['phone_href']) ?>"><span>Notfall 24/7</span><strong><?= wf_escape($rt['phone']) ?></strong></a>
</header>
<?php
}

function rt_footer(array $rt): void
{
    ?>
<footer class="rt-footer">
    <div class="rt-footer__brand">
        <?php rt_logo_lockup('footer', 'd2', true); ?>
        <p>Ihr verlässlicher Partner im Eisenbahnbetrieb.<br>Sicher. Flexibel. Deutschlandweit im Einsatz.</p>
        <a class="rt-footer__hotline" href="tel:<?= wf_escape($rt['phone_href']) ?>"><span>Notfalldienst 24/7</span><strong><?= wf_escape($rt['phone']) ?></strong></a>
    </div>
    <nav aria-label="Leistungsübersicht"><b>Leistungen</b><?php foreach ($rt['services'] as $service): ?><a href="<?= wf_escape(wf_route_url('services', $service['slug'])) ?>"><?= wf_escape($service['title']) ?></a><?php endforeach ?></nav>
    <nav aria-label="Kontakt und Rechtliches"><b>Kontakt</b><a href="<?= wf_escape(wf_route_url('contact')) ?>">Kontakt</a><a href="mailto:<?= wf_escape($rt['email']) ?>"><?= wf_escape($rt['email']) ?></a><span><?= wf_escape($rt['address']) ?></span><a href="<?= wf_escape(wf_route_url('imprint')) ?>">Impressum</a><a href="<?= wf_escape(wf_route_url('privacy')) ?>">Datenschutz</a></nav>
    <small>Copyright © <?= date('Y') ?> RT Rail Time GmbH. Alle Rechte vorbehalten.</small>
</footer>
<?php
}

function rt_document_end(bool $home = false): void
{
    ?>
<script src="<?= wf_escape(wf_asset('assets/vendor/ScrollMagic.min.js')) ?>" defer></script>
<script src="<?= wf_escape(wf_asset('assets/js/scroll-motion.js')) ?>" defer></script>
<script src="<?= wf_escape(wf_asset('assets/js/mobile-navigation.js')) ?>" defer></script>
<script src="<?= wf_escape(wf_asset('assets/js/motion.js')) ?>" defer></script>
<?php if ($home): ?>
<script type="module" src="<?= wf_escape(wf_asset('assets/js/logo-3d.js')) ?>"></script>
<script src="<?= wf_escape(wf_asset('assets/js/scroll-video-engine.js')) ?>" defer></script>
<script src="<?= wf_escape(wf_asset('assets/js/home-intro.js')) ?>" defer></script>
<?php else: ?>
<script src="<?= wf_escape(wf_asset('assets/js/subpages.js')) ?>" defer></script>
<?php endif ?>
</body>
</html>
<?php
}

function wf_legal_value(string $key): string
{
    $value = trim((string)(wf_config()['legal'][$key] ?? ''));
    if ($value !== '') {
        return wf_escape($value);
    }
    return '<mark class="rt-legal-placeholder">Vor Veröffentlichung in config/site.php ergänzen</mark>';
}

function wf_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    session_name('railtime_website');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => wf_base_path() . '/',
        'secure' => wf_is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function wf_csrf_token(): string
{
    wf_start_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return (string)$_SESSION['csrf_token'];
}

function wf_form_state(): array
{
    wf_start_session();
    $state = $_SESSION['contact_form'] ?? ['errors' => [], 'old' => [], 'status' => ''];
    unset($_SESSION['contact_form']);
    return is_array($state) ? $state : ['errors' => [], 'old' => [], 'status' => ''];
}

function wf_not_found(): void
{
    $rt = rt_document_start('Seite nicht gefunden', 3, false);
    ?>
<main id="main-content" class="ig-subpage">
    <section class="ig-legal-hero rt-error-hero"><span class="ig-code">ERROR / 404</span><p class="ig-kicker">Seite nicht gefunden</p><h1>Diese Verbindung führt ins Leere.</h1><p>Die angeforderte Seite existiert nicht oder wurde verschoben.</p><a class="ig-button" href="<?= wf_escape(wf_route_url('home')) ?>">Zur Startseite <b class="rt-action-arrow" aria-hidden="true"></b></a></section>
</main>
<?php
    rt_footer($rt);
    rt_document_end();
}
