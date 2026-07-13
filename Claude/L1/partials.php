<?php
/* Claude Layout L1 — "Signal Works"
   Eigenständiger Seitenrahmen. Inhalte, Medien und Module kommen
   ausschließlich aus /Shared (nur lesend). Referenz-Anmutung:
   Premium-Industrial-Template ("Industrial X"), frei nachempfunden. */

function cl_content(): array {
    static $content;
    return $content ??= require __DIR__ . '/../../Shared/content/railtime-content.php';
}

function cl_public_base(): string { return rtrim((string)($_SERVER['RT_PUBLIC_LAYOUT_BASE'] ?? '.'), '/'); }
function cl_layout_asset(string $file): string { return rtrim((string)($_SERVER['RT_LAYOUT_ASSET_BASE'] ?? '.'), '/') . '/assets/' . ltrim($file, '/'); }
function cl_shared(string $file): string { return rt_project_url('Shared/' . ltrim($file, '/')); }
function cl_image(string $file): string { return cl_shared('assets/images/' . $file); }
function cl_video(string $file): string { return cl_shared('assets/video/' . $file); }

/* Öffentliche Links ohne Dateiendung (Regel 38); Startseite → Basis-Pfad */
function cl_href(string $target): string {
    $target = preg_replace('/\.(html|php)(?=$|#)/', '', $target);
    return $target === 'index' || str_starts_with($target, 'index#')
        ? './' . substr($target, 5)
        : $target;
}

function cl_header(string $title, bool $home = false): array {
    $rt = cl_content(); ?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($title) ?> | RailTime</title>
<base href="<?= htmlspecialchars(cl_public_base()) ?>/">
<link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars(cl_shared('assets/icons/favicon.svg')) ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars(cl_shared('assets/icons/favicon-32x32.png')) ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= htmlspecialchars(cl_shared('assets/icons/apple-touch-icon.png')) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(cl_shared('vendor/tailwind.min.css')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=Archivo+Black&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= htmlspecialchars(cl_layout_asset('layout.css')) ?>">
</head>
<body class="cl-body<?= $home ? ' cl-home' : '' ?>">
<header class="cl-nav<?= $home ? '' : ' is-visible' ?>">
    <a class="cl-nav__logo" href="<?= cl_href('index.html') ?>"><img src="<?= cl_image($rt['assets']['logo_horizontal']) ?>" alt="<?= htmlspecialchars($rt['company']) ?>"></a>
    <nav class="cl-nav__menu"><?php foreach ($rt['navigation'] as $item): ?><a href="<?= cl_href($item['href']) ?>"><?= $item['label'] ?></a><?php endforeach ?></nav>
    <a class="cl-nav__hotline" href="tel:<?= $rt['phone_href'] ?>"><span>24/7</span><?= $rt['phone'] ?></a>
</header>
<?php return $rt;
}

function cl_footer(array $rt, bool $home = false): void { ?>
<footer class="cl-footer">
    <div class="cl-footer__stripe" aria-hidden="true"></div>
    <div class="cl-footer__grid">
        <div class="cl-footer__brand">
            <img src="<?= cl_image($rt['assets']['logo_dark']) ?>" alt="<?= htmlspecialchars($rt['company']) ?>">
            <p><?= $rt['claim'] ?></p>
            <a class="cl-footer__phone" href="tel:<?= $rt['phone_href'] ?>"><small>Notfalldienst 24/7</small><?= $rt['phone'] ?></a>
        </div>
        <nav class="cl-footer__col" aria-label="Leistungen">
            <b>Leistungen</b>
            <?php foreach ($rt['services'] as $service): ?><a href="leistungen#<?= $service['slug'] ?>"><?= $service['title'] ?></a><?php endforeach ?>
        </nav>
        <nav class="cl-footer__col" aria-label="Kontakt und Rechtliches">
            <b>Kontakt</b>
            <a href="kontakt">Kontakt</a>
            <a href="mailto:<?= $rt['email'] ?>"><?= $rt['email'] ?></a>
            <a href="impressum">Impressum</a>
            <a href="datenschutz">Datenschutz</a>
        </nav>
    </div>
    <small class="cl-footer__legal">Copyright © <?= date('Y') ?> Rail Time GmbH. Alle Rechte vorbehalten.</small>
</footer>
<script src="<?= htmlspecialchars(cl_shared('vendor/ScrollMagic.min.js')) ?>"></script>
<script src="<?= htmlspecialchars(cl_layout_asset('motion.js')) ?>"></script>
</body>
</html>
<?php }

/* Notfall-Band (Inhalt aus Shared) */
function cl_emergency(array $rt): void { ?>
<section class="cl-emergency" data-cl-reveal>
    <div>
        <p class="cl-label">Notfalldienst 24/7</p>
        <h2><?= $rt['emergency_title'] ?></h2>
        <p class="cl-emergency__copy"><?= $rt['emergency_copy'] ?></p>
    </div>
    <a class="cl-btn cl-btn--light" href="tel:<?= $rt['phone_href'] ?>">Notfalldienst anrufen</a>
</section>
<?php }

/* Prozess-Schritte (Inhalt aus Shared) */
function cl_process(array $rt): void { ?>
<section class="cl-section" id="prozess">
    <p class="cl-label" data-cl-reveal>Zusammenarbeit</p>
    <h2 class="cl-h2" data-cl-reveal>Von der Anfrage bis zum zuverlässig ausgeführten Einsatz.</h2>
    <div class="cl-process">
        <?php foreach ($rt['process'] as $i => $step): ?>
        <article class="cl-process__step" data-cl-reveal>
            <span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <p><?= $step ?></p>
        </article>
        <?php endforeach ?>
    </div>
</section>
<?php }
