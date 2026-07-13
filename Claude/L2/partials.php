<?php
/* Claude Layout L2 — "Editorial Press"
   Dunkles redaktionelles Editorial: 12-Spalten-Raster, Haarlinien,
   Serif-Display + Geometric Sans, Zahlenkolumnen. Referenz (nur
   Struktur/Anmutung): Orvik Studio, Webflow.
   Inhalte, Medien und Module ausschließlich lesend aus /Shared. */

function ed_content(): array {
    static $content;
    return $content ??= require __DIR__ . '/../../Shared/content/railtime-content.php';
}

function ed_public_base(): string { return rtrim((string)($_SERVER['RT_PUBLIC_LAYOUT_BASE'] ?? '.'), '/'); }
function ed_layout_asset(string $file): string { return rtrim((string)($_SERVER['RT_LAYOUT_ASSET_BASE'] ?? '.'), '/') . '/assets/' . ltrim($file, '/'); }
function ed_shared(string $file): string { return rt_project_url('Shared/' . ltrim($file, '/')); }
function ed_image(string $file): string { return ed_shared('assets/images/' . $file); }
function ed_video(string $file): string { return ed_shared('assets/video/' . $file); }
function ed_num(int $i): string { return '(' . str_pad((string)$i, 2, '0', STR_PAD_LEFT) . ')'; }

/* Öffentliche Links ohne Dateiendung (Regel 38); Startseite → Basis-Pfad */
function ed_href(string $target): string {
    $target = preg_replace('/\.(html|php)(?=$|#)/', '', $target);
    return $target === 'index' || str_starts_with($target, 'index#')
        ? './' . substr($target, 5)
        : $target;
}

function ed_header(string $title, bool $home = false): array {
    $rt = ed_content(); ?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($title) ?> | RailTime</title>
<base href="<?= htmlspecialchars(ed_public_base()) ?>/">
<link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars(ed_shared('assets/icons/favicon.svg')) ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= htmlspecialchars(ed_shared('assets/icons/favicon-32x32.png')) ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?= htmlspecialchars(ed_shared('assets/icons/apple-touch-icon.png')) ?>">
<link rel="stylesheet" href="<?= htmlspecialchars(ed_shared('vendor/tailwind.min.css')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,300..700&family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= htmlspecialchars(ed_layout_asset('layout.css')) ?>">
</head>
<body class="ed-body<?= $home ? ' ed-home' : '' ?>">
<header class="ed-nav<?= $home ? '' : ' is-visible' ?>">
    <a class="ed-nav__logo" href="<?= ed_href('index.html') ?>"><img src="<?= ed_image($rt['assets']['logo_horizontal']) ?>" alt="<?= htmlspecialchars($rt['company']) ?>"></a>
    <nav class="ed-nav__menu"><?php foreach ($rt['navigation'] as $i => $item): ?><a href="<?= ed_href($item['href']) ?>"><sup><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></sup><?= $item['label'] ?></a><?php endforeach ?></nav>
    <a class="ed-nav__hotline" href="tel:<?= $rt['phone_href'] ?>">24/7 — <?= $rt['phone'] ?></a>
</header>
<?php return $rt;
}

function ed_footer(array $rt): void { ?>
<footer class="ed-footer">
    <div class="ed-footer__top">
        <p class="ed-footer__claim serif"><?= $rt['claim'] ?></p>
        <img src="<?= ed_image($rt['assets']['logo_dark']) ?>" alt="<?= htmlspecialchars($rt['company']) ?>">
    </div>
    <div class="ed-footer__grid">
        <nav aria-label="Leistungen">
            <b>Leistungen</b>
            <?php foreach ($rt['services'] as $i => $service): ?><a href="leistungen#<?= $service['slug'] ?>"><span><?= ed_num($i + 1) ?></span><?= $service['title'] ?></a><?php endforeach ?>
        </nav>
        <nav aria-label="Kontakt">
            <b>Kontakt</b>
            <a href="kontakt">Kontakt</a>
            <a href="mailto:<?= $rt['email'] ?>"><?= $rt['email'] ?></a>
            <a href="tel:<?= $rt['phone_href'] ?>"><?= $rt['phone'] ?> — Notfalldienst 24/7</a>
        </nav>
        <nav aria-label="Rechtliches">
            <b>Rechtliches</b>
            <a href="impressum">Impressum</a>
            <a href="datenschutz">Datenschutz</a>
        </nav>
    </div>
    <small class="ed-footer__legal">Copyright © <?= date('Y') ?> Rail Time GmbH. Alle Rechte vorbehalten.</small>
</footer>
<script src="<?= htmlspecialchars(ed_shared('vendor/ScrollMagic.min.js')) ?>"></script>
<script src="<?= htmlspecialchars(ed_layout_asset('motion.js')) ?>"></script>
</body>
</html>
<?php }

/* Notfall-Zäsur: schmaler, rot gerahmter Editorial-Kasten */
function ed_emergency(array $rt): void { ?>
<section class="ed-emergency">
    <div class="ed-emergency__frame" data-ed-reveal>
        <p class="ed-label">Notfalldienst 24/7</p>
        <h2 class="serif"><?= $rt['emergency_title'] ?></h2>
        <p class="ed-emergency__copy"><?= $rt['emergency_copy'] ?></p>
        <a class="ed-link" href="tel:<?= $rt['phone_href'] ?>">Notfalldienst anrufen — <?= $rt['phone'] ?></a>
    </div>
</section>
<?php }

/* Prozess als heller Papier-Einschub (redaktionelle Zäsur) */
function ed_process(array $rt): void { ?>
<section class="ed-section ed-section--paper" id="prozess">
    <div class="ed-section__head">
        <p class="ed-label" data-ed-reveal>Zusammenarbeit</p>
        <h2 class="ed-h2 serif" data-ed-reveal>Von der Anfrage bis zum <em>zuverlässig</em> ausgeführten Einsatz.</h2>
    </div>
    <div class="ed-process">
        <?php foreach ($rt['process'] as $i => $step): ?>
        <div class="ed-process__col" data-ed-reveal>
            <span><?= ed_num($i + 1) ?></span>
            <p><?= $step ?></p>
        </div>
        <?php endforeach ?>
    </div>
</section>
<?php }
