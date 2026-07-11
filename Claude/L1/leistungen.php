<?php require __DIR__ . '/partials.php'; $rt = cl_header('Leistungen'); ?>
<main class="cl-sub">

<section class="cl-pagehead">
    <div>
        <p class="cl-label cl-label--light">Unsere Leistungen</p>
        <h1>Leistungen für sichere Abläufe im Eisenbahngüterverkehr</h1>
        <p class="cl-pagehead__copy">Professionelle Wagenmeister-Dienstleistungen, kurzfristige Notfalleinsätze und technische Unterstützung im kombinierten Verkehr – klar strukturiert, bundesweit verfügbar und auf die konkrete Einsatzlage abgestimmt.</p>
    </div>
    <div class="cl-pagehead__index" aria-hidden="true">
        <?php foreach ($rt['services'] as $i => $service): ?><span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?> <?= $service['title'] ?></span><?php endforeach ?>
    </div>
</section>

<!-- Fünf bebilderte Leistungskacheln -->
<section class="cl-section">
    <p class="cl-label" data-cl-reveal>Leistungsbereiche</p>
    <h2 class="cl-h2" data-cl-reveal>Fünf Leistungen. Ein verlässlicher Ansprechpartner.</h2>
    <div class="cl-tiles">
        <?php foreach ($rt['services'] as $i => $service): ?>
        <a class="cl-tiles__tile" data-service-tile href="#<?= $service['slug'] ?>" data-cl-reveal>
            <img src="<?= cl_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>">
            <div>
                <span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <h3><?= $service['title'] ?></h3>
            </div>
        </a>
        <?php endforeach ?>
    </div>
</section>

<!-- Details als Akkordeon: genau eine Leistung geöffnet -->
<section class="cl-section cl-section--panel">
    <p class="cl-label cl-label--light" data-cl-reveal>Leistungen im Detail</p>
    <div class="cl-accordion">
        <?php foreach ($rt['services'] as $i => $service): ?>
        <article id="<?= $service['slug'] ?>" class="cl-accordion__item" data-accordion-item>
            <button type="button" aria-expanded="false">
                <span><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <strong><?= $service['title'] ?></strong>
                <i class="cl-accordion__plus" aria-hidden="true"></i>
            </button>
            <div class="cl-accordion__panel" data-panel>
                <div class="cl-accordion__inner">
                    <img src="<?= cl_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>">
                    <div>
                        <p><?= $service['copy'] ?></p>
                        <h4>Typischer Ablauf</h4>
                        <ol>
                            <li>Anfrage und Einsatzdaten aufnehmen</li>
                            <li>Qualifikation und Ausrüstung abstimmen</li>
                            <li>Einsatz vor Ort durchführen</li>
                            <li>Befund und Rückmeldung dokumentieren</li>
                        </ol>
                        <a class="cl-btn" href="kontakt">Einsatz anfragen</a>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach ?>
    </div>
</section>

<?php cl_emergency($rt); ?>
</main>
<?php cl_footer($rt); ?>
