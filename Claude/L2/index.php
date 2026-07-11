<?php require __DIR__ . '/partials.php'; $rt = ed_header('Editorial Press', true); ?>
<main>

<!-- Hero: Autoplay-Video (Regel 04/23), redaktionelle Kopfzeile -->
<section class="ed-hero" id="top">
    <video id="ed-hero-video" autoplay muted playsinline preload="auto" poster="<?= ed_image('start3-first-frame.jpg') ?>">
        <source src="<?= ed_video($rt['assets']['hero_video']) ?>" type="video/mp4">
    </video>
    <div class="ed-hero__shade" aria-hidden="true"></div>
    <div class="ed-hero__logo"><img src="<?= ed_image($rt['assets']['logo_dark']) ?>" alt="<?= htmlspecialchars($rt['company']) ?>"></div>
    <div class="ed-hero__copy">
        <p class="ed-label"><?= htmlspecialchars($rt['company']) ?></p>
        <h1 class="serif">Wagenmeister im Einsatz — <em>sicher, flexibel,</em> bundesweit.</h1>
        <p class="ed-hero__claim"><?= $rt['claim'] ?></p>
        <div class="ed-hero__actions">
            <a class="ed-link" href="#leistungen">Leistungen entdecken</a>
            <a class="ed-link" href="kontakt">Einsatz anfragen</a>
        </div>
    </div>
    <button class="ed-hero__sound" id="ed-sound" type="button" aria-pressed="false">Ton an</button>
</section>

<!-- Kennzahlen als redaktionelle Zahlenkolumne -->
<section class="ed-metrics" id="content-start">
    <?php foreach ($rt['metrics'] as $i => $metric): ?>
    <div class="ed-metrics__col" data-ed-reveal>
        <span><?= ed_num($i + 1) ?></span>
        <strong class="serif"><?= $metric['number'] ?></strong>
        <p><?= $metric['label'] ?></p>
    </div>
    <?php endforeach ?>
</section>

<!-- Intro: großes Serif-Statement, schmale Begleitspalte -->
<section class="ed-section ed-intro">
    <p class="ed-label" data-ed-reveal>RT Rail Time GmbH</p>
    <h2 class="ed-intro__statement serif" data-ed-reveal><?= $rt['about_title'] ?></h2>
    <p class="ed-intro__aside" data-ed-reveal><?= $rt['about_copy'] ?></p>
</section>

<!-- Leistungen: nummerierter Editorial-Index -->
<section class="ed-section" id="leistungen">
    <div class="ed-section__head">
        <p class="ed-label" data-ed-reveal>Unsere Leistungen</p>
        <h2 class="ed-h2 serif" data-ed-reveal>Fünf Leistungsbereiche für einen <em>sicheren</em> Eisenbahnbetrieb</h2>
    </div>
    <div class="ed-index">
        <?php foreach ($rt['services'] as $i => $service): ?>
        <a class="ed-index__row" href="leistungen#<?= $service['slug'] ?>" data-ed-reveal>
            <span class="ed-index__num"><?= ed_num($i + 1) ?></span>
            <div class="ed-index__body">
                <h3 class="serif"><?= $service['title'] ?></h3>
                <p><?= $service['copy'] ?></p>
            </div>
            <figure class="ed-index__figure"><img src="<?= ed_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>"></figure>
            <b class="ed-index__more">Mehr erfahren</b>
        </a>
        <?php endforeach ?>
    </div>
</section>

<?php ed_process($rt); ?>

<!-- Team -->
<section class="ed-split">
    <figure data-ed-reveal>
        <img src="<?= ed_image($rt['assets']['team_image']) ?>" alt="Rail Time im Einsatz">
        <figcaption>Unser Team im Einsatz</figcaption>
    </figure>
    <div class="ed-split__text" data-ed-reveal>
        <p class="ed-label">Unser Team</p>
        <h2 class="ed-h2 serif">56+ qualifizierte Wagenmeister — <em>bundesweit</em> verfügbar</h2>
        <p>Unsere Mitarbeiter werden regelmäßig fortgebildet und vereinen Qualifikationen aus Wagenprüfung, Bahnbetrieb, Qualitätsmanagement, Gefahrgut und Schadwagenmanagement.</p>
    </div>
</section>

<!-- Einsatzgebiet & Ausrüstung: Karte links, redaktionelle Liste rechts, kein Segmentfoto (Regel 36) -->
<section class="ed-section ed-mapzone">
    <div class="ed-mapzone__map" data-ed-reveal>
        <?php include __DIR__ . '/../../Shared/modules/germany-map.php'; ?>
    </div>
    <div class="ed-mapzone__copy">
        <p class="ed-label" data-ed-reveal>Ausrüstung &amp; Technik</p>
        <h2 class="ed-h2 serif" data-ed-reveal>Für sichere und <em>effiziente</em> Einsätze vorbereitet</h2>
        <ul class="ed-equipment__list" data-ed-reveal>
            <?php foreach (explode('·', $rt['equipment']) as $i => $item): ?><li><span><?= ed_num($i + 1) ?></span><?= trim($item) ?></li><?php endforeach ?>
        </ul>
    </div>
</section>

<?php ed_emergency($rt); ?>
</main>
<?php ed_footer($rt); ?>
