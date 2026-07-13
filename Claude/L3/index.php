<?php require __DIR__ . '/partials.php';
$rt = ad_header('Atlas Direct', true,
    'RT Rail Time GmbH – professionelle Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr. 60+ erfahrene Wagenmeister, Notfalldienst 24/7, bundesweit im Einsatz.'); ?>
<main class="ad-main">

<!-- Hero: Video läuft automatisch einmal durch (kein Loop, Regel 04), keine Scroll-Steuerung.
     Poster ist das echte erste Videoframe (Regel 39), Endbild bleibt stehen. -->
<section class="ad-hero" id="top" aria-label="Rail Time Startvideo">
    <video id="ad-hero-video" autoplay muted playsinline preload="auto" poster="<?= ad_image('start3-first-frame.png') ?>">
        <source src="<?= ad_video($rt['assets']['hero_video']) ?>" type="video/mp4">
    </video>
    <div class="ad-hero__shade" aria-hidden="true"></div>
    <div class="ad-hero__logo" id="ad-hero-logo">
        <span class="ad-orbit ad-orbit--one" aria-hidden="true"></span>
        <span class="ad-orbit ad-orbit--two" aria-hidden="true"></span>
        <?php ad_logo_lockup('hero'); ?>
    </div>
    <div class="ad-hero__status" aria-hidden="true"><span>RT / 01</span><i></i><span>DE / BUNDESWEIT</span><i></i><span>24 / 7</span></div>
    <div class="ad-scroll-cue" id="ad-scroll-cue" aria-hidden="true"><span><i></i></span><small>Scrollen</small></div>
</section>

<!-- Intro: heller Auftakt mit Kennzahlen -->
<section class="ad-intro" id="content-start">
    <div class="ad-intro__lead" data-ad-reveal="up">
        <p class="ad-eyebrow">RT Rail Time GmbH <span></span> Eisenbahngüterverkehr</p>
        <h1>Ihr verlässlicher Partner für professionelle Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr.</h1>
    </div>
    <figure class="ad-intro__image" data-ad-reveal="right">
        <img src="<?= ad_image('s3.jpg') ?>" alt="Wagenmeister im Einsatz" loading="lazy" decoding="async">
        <figcaption>Kompetent. Flexibel. Bundesweit im Einsatz.</figcaption>
    </figure>
    <div class="ad-intro__facts" data-ad-reveal="up">
        <?php foreach ($rt['metrics'] as $metric): ?><div><strong><?= $metric['number'] ?></strong><span><?= $metric['label'] ?></span></div><?php endforeach ?>
    </div>
    <a class="ad-arrow-link" href="<?= ad_href('kontakt.html') ?>">Einsatz anfragen <b>&rarr;</b></a>
</section>

<!-- Laufband -->
<section class="ad-rail" aria-hidden="true"><div><span>Sicher</span><i></i><span>Flexibel</span><i></i><span>Rund um die Uhr</span><i></i><span>Bundesweit im Einsatz</span><i></i><span>Sicher</span><i></i><span>Flexibel</span></div></section>

<!-- Leistungen -->
<section class="ad-services" id="leistungen">
    <header class="ad-section-head is-split" data-ad-reveal="up">
        <div>
            <p class="ad-eyebrow">01 <span></span> Unsere Leistungen</p>
            <h2>Fünf Leistungsbereiche für einen sicheren Eisenbahnbetrieb</h2>
        </div>
        <a class="ad-arrow-link" href="<?= ad_href('leistungen.html') ?>">Alle Leistungen <b>&rarr;</b></a>
    </header>
    <div class="ad-services__grid">
        <?php foreach ($rt['services'] as $i => $service): ?>
        <a href="<?= ad_href('leistungen.html') ?>#<?= $service['slug'] ?>" class="ad-service" data-ad-reveal="up">
            <span>0<?= $i + 1 ?></span>
            <figure><img src="<?= ad_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>" loading="lazy" decoding="async"></figure>
            <h3><?= $service['title'] ?></h3>
            <b>&rarr;</b>
        </a>
        <?php endforeach ?>
    </div>
</section>

<!-- Über uns -->
<section class="ad-team">
    <div class="ad-team__visual" data-ad-reveal="left">
        <figure><img src="<?= ad_image('s4.jpg') ?>" alt="Mitarbeiter im Bahnbetrieb" loading="lazy" decoding="async"></figure>
        <figure><img src="<?= ad_image('s1.jpg') ?>" alt="Wagenmeister von RT Rail Time" loading="lazy" decoding="async"></figure>
    </div>
    <div class="ad-team__copy" data-ad-reveal="right">
        <p class="ad-eyebrow">02 <span></span> Über RT Rail Time</p>
        <h2><?= $rt['about_title'] ?></h2>
        <p><?= $rt['about_copy'] ?></p>
        <a class="ad-arrow-link" href="<?= ad_href('ueber-uns.html') ?>">Unternehmen kennenlernen <b>&rarr;</b></a>
        <div class="ad-team__seal"><strong>60+</strong><span>qualifizierte<br>Wagenmeister</span></div>
    </div>
</section>

<?php ad_process($rt); ?>

<!-- Einsatzgebiet & Technik: Karte links, Text rechts, kein Segmentfoto (Regel 36) -->
<section class="ad-network">
    <div class="ad-network__map" data-ad-reveal="left">
        <?php include __DIR__ . '/../../Shared/modules/germany-map.php'; ?>
    </div>
    <div class="ad-network__copy" data-ad-reveal="right">
        <p class="ad-eyebrow">04 <span></span> Ausrüstung &amp; Technik</p>
        <h2>Für sichere und effiziente Einsätze vorbereitet</h2>
        <p><?= $rt['equipment'] ?></p>
        <a class="ad-arrow-link" href="<?= ad_href('kontakt.html') ?>">Technik anfragen <b>&rarr;</b></a>
    </div>
</section>

<?php ad_emergency($rt); ?>
</main>
<?php ad_footer($rt); ?>
