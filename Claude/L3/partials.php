<?php
/* Claude Layout L3 — "Atlas Direct"
   Optimierte Neufassung von Codex Layout 3 (Signal Atlas) auf Nutzerwunsch:
   gleicher Gestaltungscharakter, aber das Startvideo läuft einfach automatisch
   ab (One-Shot ohne Loop, Regel 04/23) statt über die Scroll-Trägheitssteuerung.
   Eigenständiger Seitenrahmen; Inhalte, Medien und Module ausschließlich aus
   /Shared (nur lesend). Bildmarke: neues D2-Monogramm (Claude/logo/d2). */

require_once __DIR__ . '/../../Shared/config/layout-registry.php';

function ad_content(): array {
    static $content;
    return $content ??= require __DIR__ . '/../../Shared/content/railtime-content.php';
}

function ad_public_base(): string { return rtrim((string)($_SERVER['RT_PUBLIC_LAYOUT_BASE'] ?? '.'), '/'); }
function ad_layout_asset(string $file): string { return rtrim((string)($_SERVER['RT_LAYOUT_ASSET_BASE'] ?? '.'), '/') . '/assets/' . ltrim($file, '/'); }
function ad_shared(string $file): string { return rt_project_url('Shared/' . ltrim($file, '/')); }
function ad_image(string $file): string { return ad_shared('assets/images/' . $file); }
function ad_video(string $file): string { return ad_shared('assets/video/' . $file); }
function ad_mark(): string { return rt_project_url('Claude/logo/d2/rt-logo.svg'); }

/* Öffentliche Links ohne Dateiendung (Regel 38); Startseite → Basis-Pfad */
function ad_href(string $target): string {
    $target = preg_replace('/\.(html|php)(?=$|#)/', '', $target);
    return $target === 'index' || str_starts_with($target, 'index#')
        ? './' . substr($target, 5)
        : $target;
}

/* Markenzeichen: D2-Monogramm + Wortmarke aus Shared */
function ad_logo_lockup(string $variant = 'nav'): void { $rt = ad_content(); ?>
<span class="ad-lockup ad-lockup--<?= htmlspecialchars($variant) ?>" role="img" aria-label="<?= htmlspecialchars($rt['company']) ?>">
    <img class="ad-lockup__mark" src="<?= htmlspecialchars(ad_mark()) ?>" alt="" aria-hidden="true">
    <img class="ad-lockup__wordmark" src="<?= htmlspecialchars(ad_image('logo-txt.png')) ?>" alt="" aria-hidden="true">
</span>
<?php }

function ad_header(string $title, bool $home = false, string $description = ''): array {
    $rt = ad_content();
    $description = $description !== '' ? $description : $rt['claim']; ?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#090c11">
<meta name="description" content="<?= htmlspecialchars($description) ?>">
<meta property="og:title" content="<?= htmlspecialchars($title) ?> | RailTime">
<meta property="og:description" content="<?= htmlspecialchars($description) ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="de_DE">
<title><?= htmlspecialchars($title) ?> | RailTime</title>
<base href="<?= htmlspecialchars(ad_public_base()) ?>/">
<link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars(ad_mark()) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= htmlspecialchars(ad_shared('vendor/tailwind.min.css')) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(ad_layout_asset('layout.css')) ?>?v=1">
<?php if ($home): ?>
<link rel="preload" as="image" href="<?= htmlspecialchars(ad_image('start3-first-frame.png')) ?>">
<?php endif ?>
</head>
<body class="ad-body<?= $home ? ' ad-home' : '' ?>">
<header class="ad-nav<?= $home ? '' : ' is-visible' ?>">
    <a class="ad-nav__brand" href="<?= ad_href('index.html') ?>" aria-label="<?= htmlspecialchars($rt['company']) ?> – Startseite"><?php ad_logo_lockup('nav'); ?></a>
    <button class="ad-nav__toggle" type="button" aria-label="Menü öffnen" aria-expanded="false"><span></span><span></span></button>
    <nav aria-label="Hauptnavigation"><?php foreach ($rt['navigation'] as $item): ?><a href="<?= ad_href($item['href']) ?>"><?= $item['label'] ?></a><?php endforeach ?></nav>
    <a class="ad-nav__phone" href="tel:<?= $rt['phone_href'] ?>"><span>Notfall 24/7</span><strong><?= $rt['phone'] ?></strong></a>
</header>
<?php return $rt;
}

function ad_footer(array $rt): void { ?>
<footer class="ad-footer">
    <div class="ad-footer__brand">
        <?php ad_logo_lockup('footer'); ?>
        <p>Ihr verlässlicher Partner im Eisenbahnbetrieb.<br>Sicher. Flexibel. Deutschlandweit im Einsatz.</p>
        <a class="ad-footer__hotline" href="tel:<?= $rt['phone_href'] ?>"><span>Notfalldienst 24/7</span><strong><?= $rt['phone'] ?></strong></a>
    </div>
    <nav aria-label="Leistungen"><b>Leistungen</b><?php foreach ($rt['services'] as $service): ?><a href="<?= ad_href('leistungen.html') ?>#<?= $service['slug'] ?>"><?= $service['title'] ?></a><?php endforeach ?></nav>
    <nav aria-label="Kontakt und Rechtliches"><b>Kontakt</b><a href="<?= ad_href('kontakt.html') ?>">Kontakt</a><a href="mailto:<?= $rt['email'] ?>"><?= $rt['email'] ?></a><span><?= $rt['address'] ?></span><a href="<?= ad_href('impressum.html') ?>">Impressum</a><a href="<?= ad_href('datenschutz.html') ?>">Datenschutz</a></nav>
    <small>Copyright © <?= date('Y') ?> Rail Time GmbH. Alle Rechte vorbehalten.</small>
</footer>
<script src="<?= htmlspecialchars(ad_shared('vendor/ScrollMagic.min.js')) ?>"></script>
<script src="<?= htmlspecialchars(ad_layout_asset('motion.js')) ?>?v=1"></script>
</body>
</html>
<?php }

/* Notfall-Band (Inhalt aus Shared) — einheitlich auf Start- und Unterseiten */
function ad_emergency(array $rt): void { ?>
<section class="ad-emergency">
    <figure data-ad-reveal="left"><img src="<?= ad_image('s2.jpg') ?>" alt="Technische Untersuchung am Güterwagen" loading="lazy" decoding="async"></figure>
    <div data-ad-reveal="right">
        <p class="ad-eyebrow">Notfalldienst 24/7</p>
        <h2><?= $rt['emergency_title'] ?></h2>
        <p><?= $rt['emergency_copy'] ?></p>
        <a class="ad-button" href="tel:<?= $rt['phone_href'] ?>">Notfalldienst anrufen <b>&rarr;</b></a>
    </div>
</section>
<?php }

/* Prozess-Schritte (Inhalt aus Shared); helle Variante für Unterseiten */
function ad_process(array $rt, bool $compact = false): void { ?>
<section class="ad-process<?= $compact ? ' is-compact' : '' ?>">
    <header class="ad-section-head" data-ad-reveal="up">
        <p class="ad-eyebrow"><?= $compact ? 'Ablauf' : '03 <span></span> Zusammenarbeit' ?></p>
        <h2>Von der Anfrage bis zum zuverlässig ausgeführten Einsatz.</h2>
    </header>
    <div class="ad-process__steps">
        <?php foreach ($rt['process'] as $i => $step): ?>
        <article data-ad-reveal="up"><b>0<?= $i + 1 ?></b><span></span><p><?= $step ?></p></article>
        <?php endforeach ?>
    </div>
</section>
<?php }

/* Unterseiten-Kopf mit Bild, Kennzahlen-Aside und optionalem CTA */
function ad_page_hero(array $rt, string $image, string $kicker, string $title, string $copy, string $ctaLabel = '', string $ctaHref = ''): void { ?>
<section class="ad-page-hero" style="--image:url('<?= ad_image($image) ?>')">
    <div>
        <p class="ad-eyebrow"><?= $kicker ?></p>
        <h1><?= $title ?></h1>
        <p><?= $copy ?></p>
        <?php if ($ctaLabel !== ''): ?><a class="ad-button" href="<?= $ctaHref ?>"><?= $ctaLabel ?> <b>&rarr;</b></a><?php endif ?>
    </div>
    <aside>
        <?php foreach ($rt['metrics'] as $metric): ?><div><strong><?= $metric['number'] ?></strong><span><?= $metric['label'] ?></span></div><?php endforeach ?>
    </aside>
</section>
<?php }
