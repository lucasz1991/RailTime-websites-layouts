<?php
require_once __DIR__ . '/../config/layout-registry.php';

function rt_repair_utf8(mixed $value): mixed {
    if (is_array($value)) {
        foreach ($value as $key => $item) {
            $value[$key] = rt_repair_utf8($item);
        }
        return $value;
    }
    if (!is_string($value) || !preg_match('/(?:Ãƒ.|Ã‚.|Ã¢.)/u', $value)) {
        return $value;
    }
    return mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
}

function rt_shared_content(): array {
    static $content;
    return $content ??= rt_repair_utf8(require __DIR__ . '/../content/railtime-content.php');
}

function rt_image(string $file): string {
    return rt_project_url('Shared/assets/images/' . $file);
}

function rt_video(string $file): string {
    return rt_project_url('Shared/assets/video/' . $file);
}

function rt_layout_asset(string $file): string {
    if (!empty($_SERVER['RT_LAYOUT_ASSET_BASE'])) {
        return rtrim($_SERVER['RT_LAYOUT_ASSET_BASE'], '/') . '/' . implode('/', array_map('rawurlencode', explode('/', ltrim($file, '/'))));
    }
    return $file;
}

function rt_video_poster(array $rt): string {
    return rt_image($rt['assets']['hero_poster']);
}

function rt_video_attrs(array $rt, bool $autoplay = true): string {
    $poster = rt_video_poster($rt);
    $attrs = [
        'muted',
        'playsinline',
        'preload="metadata"',
        'poster="' . $poster . '"',
        'data-hero-video',
        'data-hero-poster="' . $poster . '"',
    ];
    if ($autoplay) {
        $attrs[] = 'autoplay';
    }
    return implode(' ', $attrs);
}

function rt_video_scroll_cue(): void {
    ?>
<div class="rt-video-scroll-cue" data-video-scroll-cue aria-hidden="true">
    <span><i></i></span>
    <small>Scrollen</small>
</div>
<?php
}

function rt_document_start(string $title, int $theme, bool $home = false): array {
    $rt = rt_shared_content();
    $publicAttrs = '';
    if (isset($_SERVER['RT_PUBLIC_LAYOUT_ID'], $_SERVER['RT_PUBLIC_LAYOUT_BASE'])) {
        $publicAttrs = ' data-public-layout="' . htmlspecialchars((string)$_SERVER['RT_PUBLIC_LAYOUT_ID'], ENT_QUOTES, 'UTF-8') . '"';
        $publicAttrs .= ' data-public-base="' . htmlspecialchars((string)$_SERVER['RT_PUBLIC_LAYOUT_BASE'], ENT_QUOTES, 'UTF-8') . '"';
    }
    ?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#090c11">
<meta name="description" content="Ihr verlässlicher Partner für professionelle Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr.">
<title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | RailTime</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= rt_project_url('Shared/vendor/tailwind.min.css') ?>?v=5">
<link rel="stylesheet" href="<?= rt_project_url('Shared/styles/design-system.css') ?>?v=5">
<link rel="stylesheet" href="<?= rt_project_url('Shared/styles/layout-polish.css') ?>?v=9">
<link rel="stylesheet" href="<?= htmlspecialchars(rt_layout_asset('assets/layout.css'), ENT_QUOTES, 'UTF-8') ?>?v=5">
<?php if ($theme === 1): ?>
<link rel='preconnect' href='https://esm.sh' crossorigin>
<link rel='preload' href='<?= rt_project_url('Codex/logo/d1/rt-logo.glb') ?>' as='fetch' type='model/gltf-binary' crossorigin>
<link rel='stylesheet' href='<?= rt_project_url('Shared/styles/logo-3d.css') ?>?v=1'>
<script type='module' src='<?= rt_project_url('Shared/scripts/logo-3d.js') ?>?v=1'></script>
<?php endif ?>
</head>
<body class="rt-shell theme-<?= $theme ?><?= $home ? ' is-home' : ' is-subpage' ?> antialiased"<?= $publicAttrs ?>>
<?php
    rt_navigation($rt, $home);
    return $rt;
}

function rt_navigation(array $rt, bool $home): void {
    ?>
<header class="rt-nav<?= $home ? '' : ' is-visible' ?>">
    <a class="rt-nav__brand" href="index.html"><img src="<?= rt_image($rt['assets']['logo_horizontal']) ?>" alt="RT Rail Time GmbH"></a>
    <button class="rt-nav__toggle" type="button" aria-label="Menü öffnen" aria-expanded="false"><span></span><span></span></button>
    <nav><?php foreach ($rt['navigation'] as $item): ?><a href="<?= $item['href'] ?>"><?= $item['label'] ?></a><?php endforeach ?></nav>
    <a class="rt-nav__phone" href="tel:<?= $rt['phone_href'] ?>"><span>Notfall 24/7</span><strong><?= $rt['phone'] ?></strong></a>
</header>
<?php
}

function rt_footer(array $rt): void {
    ?>
<footer class="rt-footer">
    <div class="rt-footer__brand">
        <img src="<?= rt_image($rt['assets']['logo_dark']) ?>" alt="RT Rail Time">
        <p>Ihr verlässlicher Partner im Eisenbahnbetrieb.<br>Sicher. Flexibel. Deutschlandweit im Einsatz.</p>
        <a class="rt-footer__hotline" href="tel:<?= $rt['phone_href'] ?>"><span>Notfalldienst 24/7</span><strong><?= $rt['phone'] ?></strong></a>
    </div>
    <div><b>Leistungen</b><?php foreach ($rt['services'] as $service): ?><a href="leistungen.html#<?= $service['slug'] ?>"><?= $service['title'] ?></a><?php endforeach ?></div>
    <div><b>Kontakt</b><a href="kontakt.html">Kontakt</a><a href="mailto:<?= $rt['email'] ?>"><?= $rt['email'] ?></a><span><?= $rt['address'] ?></span><a href="impressum.html">Impressum</a><a href="datenschutz.html">Datenschutz</a></div>
    <small>Copyright © <?= date('Y') ?> Rail Time GmbH. Alle Rechte vorbehalten.</small>
</footer>
<?php
}

function rt_document_end(bool $home = false): void {
    ?>
<script src="<?= rt_project_url('Shared/vendor/ScrollMagic.min.js') ?>?v=5"></script>
<script src="<?= rt_project_url('Shared/scripts/scroll-motion.js') ?>?v=6"></script>
<?php if ($home): ?>
<script src="<?= htmlspecialchars(rt_layout_asset('assets/motion.js'), ENT_QUOTES, 'UTF-8') ?>?v=5"></script>
<script src="<?= rt_project_url('Shared/scripts/scroll-video-engine.js') ?>?v=9"></script>
<script src="<?= rt_project_url('Shared/scripts/home-intro.js') ?>?v=14"></script>
<?php else: ?>
<script src="<?= rt_project_url('Shared/scripts/subpages.js') ?>?v=5"></script>
<script src="<?= htmlspecialchars(rt_layout_asset('assets/motion.js'), ENT_QUOTES, 'UTF-8') ?>?v=5"></script>
<?php endif ?>
<script src="<?= rt_project_url('Shared/scripts/public-router.js') ?>?v=1"></script>
</body>
</html>
<?php
}
