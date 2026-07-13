<?php require __DIR__ . '/partials.php';
$rt = ad_header('Leistungen', false,
    'Wagenmeister-Dienstleistungen, Notfalldienst 24/7, KV- & Trailer-Service, Sonderuntersuchungen und Pufferteller schmieren – bundesweit von RT Rail Time.'); ?>
<main class="ad-subpage">

<?php ad_page_hero($rt, 's4.jpg', 'Unsere Leistungen',
    'Leistungen für sichere Abläufe im Eisenbahngüterverkehr',
    'Professionelle Wagenmeister-Dienstleistungen, kurzfristige Notfalleinsätze und technische Unterstützung im kombinierten Verkehr.',
    'Einsatz anfragen', ad_href('kontakt.html')); ?>

<!-- Fünf bebilderte Leistungskacheln (Regel 15) -->
<section class="ad-module-menu">
    <header class="ad-section-head" data-ad-reveal="up">
        <p class="ad-eyebrow">Leistungsbereiche</p>
        <h2>Fünf Leistungen. Ein verlässlicher Ansprechpartner.</h2>
    </header>
    <div>
        <?php foreach ($rt['services'] as $i => $service): ?>
        <a data-service-tile href="#<?= $service['slug'] ?>" data-ad-reveal="up">
            <span>0<?= $i + 1 ?></span>
            <img src="<?= ad_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>" loading="lazy" decoding="async">
            <h3><?= $service['title'] ?></h3>
            <b>&searr;</b>
        </a>
        <?php endforeach ?>
    </div>
</section>

<!-- Akkordeon: exakt eine Leistung geöffnet (Regel 16) -->
<section class="ad-accordion">
    <header class="ad-section-head" data-ad-reveal="up">
        <p class="ad-eyebrow">Leistungen im Detail</p>
        <h2>Strukturiert. Nachvollziehbar. Einsatzbereit.</h2>
    </header>
    <?php foreach ($rt['services'] as $i => $service): ?>
    <article id="<?= $service['slug'] ?>" data-accordion-item>
        <button type="button" aria-expanded="false">
            <span>0<?= $i + 1 ?></span>
            <strong><?= $service['title'] ?></strong>
            <small><?= $i === 1 ? 'NOTFALL / 24-7' : 'SERVICE / DE' ?></small>
            <i class="accordion-plus" aria-hidden="true">+</i>
        </button>
        <div data-panel>
            <div class="ad-panel">
                <img src="<?= ad_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>" loading="lazy" decoding="async">
                <div>
                    <h3><?= $service['title'] ?></h3>
                    <p><?= $service['copy'] ?></p>
                    <ol>
                        <li>Anfrage und Einsatzdaten aufnehmen</li>
                        <li>Qualifikation und Ausrüstung abstimmen</li>
                        <li>Einsatz vor Ort durchführen</li>
                        <li>Befund und Rückmeldung dokumentieren</li>
                    </ol>
                    <a class="ad-arrow-link" href="<?= ad_href('kontakt.html') ?>">Einsatz anfragen <b>&rarr;</b></a>
                </div>
            </div>
        </div>
    </article>
    <?php endforeach ?>
</section>

<?php ad_process($rt, true); ?>
<?php ad_emergency($rt); ?>
</main>
<?php ad_footer($rt); ?>
