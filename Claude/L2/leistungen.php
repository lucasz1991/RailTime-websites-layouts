<?php require __DIR__ . '/partials.php'; $rt = ed_header('Leistungen'); ?>
<main class="ed-sub">

<section class="ed-pagehead">
    <p class="ed-label">Unsere Leistungen</p>
    <h1 class="serif">Leistungen für <em>sichere Abläufe</em> im Eisenbahngüterverkehr</h1>
    <p class="ed-pagehead__copy">Professionelle Wagenmeister-Dienstleistungen, kurzfristige Notfalleinsätze und technische Unterstützung im kombinierten Verkehr – klar strukturiert, bundesweit verfügbar und auf die konkrete Einsatzlage abgestimmt.</p>
</section>

<!-- Fünf bebilderte Leistungskacheln als Editorial-Figuren (Regel 15) -->
<section class="ed-section">
    <div class="ed-section__head">
        <p class="ed-label" data-ed-reveal>Leistungsbereiche</p>
        <h2 class="ed-h2 serif" data-ed-reveal>Fünf Leistungen. Ein <em>verlässlicher</em> Ansprechpartner.</h2>
    </div>
    <div class="ed-figures">
        <?php foreach ($rt['services'] as $i => $service): ?>
        <a class="ed-figures__item" data-service-tile href="#<?= $service['slug'] ?>" data-ed-reveal>
            <figure><img src="<?= ed_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>"></figure>
            <span><?= ed_num($i + 1) ?></span>
            <h3 class="serif"><?= $service['title'] ?></h3>
        </a>
        <?php endforeach ?>
    </div>
</section>

<!-- Details als Akkordeon: genau eine Leistung geöffnet (Regel 16) -->
<section class="ed-section ed-section--hairline-top">
    <p class="ed-label" data-ed-reveal>Leistungen im Detail</p>
    <div class="ed-accordion">
        <?php foreach ($rt['services'] as $i => $service): ?>
        <article id="<?= $service['slug'] ?>" class="ed-accordion__item" data-accordion-item>
            <button type="button" aria-expanded="false">
                <span><?= ed_num($i + 1) ?></span>
                <strong class="serif"><?= $service['title'] ?></strong>
                <i class="ed-accordion__marker" aria-hidden="true"></i>
            </button>
            <div class="ed-accordion__panel" data-panel>
                <div class="ed-accordion__inner">
                    <figure>
                        <img src="<?= ed_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>">
                        <figcaption><?= $service['title'] ?></figcaption>
                    </figure>
                    <div>
                        <p><?= $service['copy'] ?></p>
                        <h4>Typischer Ablauf</h4>
                        <ol>
                            <li>Anfrage und Einsatzdaten aufnehmen</li>
                            <li>Qualifikation und Ausrüstung abstimmen</li>
                            <li>Einsatz vor Ort durchführen</li>
                            <li>Befund und Rückmeldung dokumentieren</li>
                        </ol>
                        <a class="ed-link" href="kontakt">Einsatz anfragen</a>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach ?>
    </div>
</section>

<?php ed_emergency($rt); ?>
</main>
<?php ed_footer($rt); ?>
