<?php require __DIR__ . '/partials.php'; $rt = cl_header('Signal Works', true); ?>
<main>

<!-- Hero: Video läuft automatisch (schneller, ohne Loop), keine Scroll-Steuerung -->
<section class="cl-hero" id="top">
    <video id="cl-hero-video" autoplay muted playsinline preload="auto" poster="<?= cl_image('start3-first-frame.jpg') ?>">
        <source src="<?= cl_video($rt['assets']['hero_video']) ?>" type="video/mp4">
    </video>
    <div class="cl-hero__shade" aria-hidden="true"></div>
    <div class="cl-hero__logo"><img src="<?= cl_image($rt['assets']['logo_dark']) ?>" alt="<?= htmlspecialchars($rt['company']) ?>"></div>
    <div class="cl-hero__copy">
        <p class="cl-label cl-label--light"><?= htmlspecialchars($rt['company']) ?></p>
        <h1>Wagenmeister im Einsatz. Bundesweit. Rund um die Uhr.</h1>
        <p class="cl-hero__claim"><?= $rt['claim'] ?></p>
        <div class="cl-hero__actions">
            <a class="cl-btn" href="#leistungen">Leistungen entdecken</a>
            <a class="cl-btn cl-btn--ghost" href="kontakt">Einsatz anfragen</a>
        </div>
    </div>
    <button class="cl-hero__sound" id="cl-sound" type="button" aria-pressed="false">Ton an</button>
    <div class="cl-hero__ticker" aria-hidden="true"><div>
        <?php for ($i = 0; $i < 2; $i++) foreach ($rt['metrics'] as $metric): ?><span><?= $metric['number'] ?> · <?= $metric['label'] ?></span><?php endforeach ?>
    </div></div>
</section>

<!-- Kennzahlen -->
<section class="cl-metrics" id="content-start">
    <?php foreach ($rt['metrics'] as $i => $metric): ?>
    <div class="cl-metrics__item" data-cl-reveal>
        <small><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></small>
        <strong><?= $metric['number'] ?></strong>
        <span><?= $metric['label'] ?></span>
    </div>
    <?php endforeach ?>
</section>

<!-- Intro -->
<section class="cl-section cl-intro">
    <div class="cl-intro__rail" aria-hidden="true"></div>
    <div>
        <p class="cl-label" data-cl-reveal>RT Rail Time GmbH</p>
        <h2 class="cl-h2" data-cl-reveal><?= $rt['about_title'] ?></h2>
    </div>
    <p class="cl-lead" data-cl-reveal><?= $rt['about_copy'] ?></p>
</section>

<!-- Leistungen -->
<section class="cl-section cl-section--panel" id="leistungen">
    <div class="cl-section__head">
        <div>
            <p class="cl-label cl-label--light" data-cl-reveal>Unsere Leistungen</p>
            <h2 class="cl-h2" data-cl-reveal>Fünf Leistungsbereiche für einen sicheren Eisenbahnbetrieb</h2>
        </div>
        <a class="cl-btn cl-btn--ghost" href="leistungen">Alle Leistungen</a>
    </div>
    <div class="cl-services">
        <?php foreach ($rt['services'] as $i => $service): ?>
        <a class="cl-services__card" href="leistungen#<?= $service['slug'] ?>" data-cl-reveal>
            <figure><img src="<?= cl_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>"></figure>
            <div>
                <span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <h3><?= $service['title'] ?></h3>
                <p><?= $service['copy'] ?></p>
                <b>Mehr erfahren →</b>
            </div>
        </a>
        <?php endforeach ?>
    </div>
</section>

<?php cl_process($rt); ?>

<!-- Team -->
<section class="cl-split">
    <figure data-cl-reveal><img src="<?= cl_image($rt['assets']['team_image']) ?>" alt="Rail Time im Einsatz"></figure>
    <div class="cl-split__panel" data-cl-reveal>
        <p class="cl-label cl-label--light">Unser Team</p>
        <h2 class="cl-h2">60+ qualifizierte Wagenmeister – bundesweit verfügbar</h2>
        <p>Unsere Mitarbeiter werden regelmäßig fortgebildet und vereinen Qualifikationen aus Wagenprüfung, Bahnbetrieb, Qualitätsmanagement, Gefahrgut und Schadwagenmanagement.</p>
    </div>
</section>

<!-- Einsatzgebiet & Ausrüstung: Karte links, Text rechts, kein Segmentfoto (Regel 36) -->
<section class="cl-mapzone">
    <div class="cl-mapzone__map" data-cl-reveal>
        <?php include __DIR__ . '/../../Shared/modules/germany-map.php'; ?>
    </div>
    <div class="cl-mapzone__copy cl-split__panel cl-split__panel--paper" data-cl-reveal>
        <p class="cl-label">Ausrüstung &amp; Technik</p>
        <h2 class="cl-h2">Für sichere und effiziente Einsätze vorbereitet</h2>
        <ul class="cl-equipment">
            <?php foreach (explode('·', $rt['equipment']) as $item): ?><li><?= trim($item) ?></li><?php endforeach ?>
        </ul>
    </div>
</section>

<?php cl_emergency($rt); ?>
</main>
<?php cl_footer($rt, true); ?>
