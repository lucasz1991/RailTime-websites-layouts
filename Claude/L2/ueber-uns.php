<?php require __DIR__ . '/partials.php'; $rt = ed_header('Über uns'); ?>
<main class="ed-sub">

<section class="ed-pagehead">
    <p class="ed-label">Ihr Partner im Eisenbahnbetrieb</p>
    <h1 class="serif">Über <em>RT Rail Time</em> GmbH</h1>
    <p class="ed-pagehead__copy">Die RT Rail Time GmbH ist Ihr kompetenter und flexibler Partner für Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr.</p>
</section>

<section class="ed-split">
    <figure data-ed-reveal>
        <img src="<?= ed_image('s4.jpg') ?>" alt="Rail Time im Einsatz">
        <figcaption>Rail Time im Einsatz</figcaption>
    </figure>
    <div class="ed-split__text" data-ed-reveal>
        <p class="ed-label">Im Detail</p>
        <h2 class="ed-h2 serif">Maßgeschneiderte Lösungen für den <em>Eisenbahngüterverkehr</em>.</h2>
        <p>Die RT Rail Time GmbH ist Ihr kompetenter und flexibler Partner für Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr. Mit hohem Anspruch an Sicherheit und Qualität bieten wir maßgeschneiderte Lösungen für unterschiedliche Herausforderungen im Bereich der Wagenmeisterdienstleistungen.</p>
        <p>Unser Handeln ist auf die Zufriedenheit unserer Kunden ausgerichtet. Präzise Arbeitsweise, zuverlässige Kommunikation und schnelle Reaktion bilden die Grundlage unserer Zusammenarbeit.</p>
        <a class="ed-link" href="kontakt">Einsatz anfragen</a>
    </div>
</section>

<section class="ed-section ed-section--hairline-top">
    <div class="ed-section__head">
        <p class="ed-label" data-ed-reveal>Unsere Philosophie</p>
        <h2 class="ed-h2 serif" data-ed-reveal>Vertrauen entsteht durch <em>transparente</em> und zuverlässige Zusammenarbeit.</h2>
    </div>
    <div class="ed-process">
        <div class="ed-process__col" data-ed-reveal><span>(A)</span><h3 class="serif">Sicherheit &amp; Qualität</h3><p>Nachhaltiges Qualitätsmanagement unterstützt Leistungen auf hohem fachlichem Niveau.</p></div>
        <div class="ed-process__col" data-ed-reveal><span>(B)</span><h3 class="serif">Transparenz</h3><p>Klare Abstimmung und nachvollziehbare Kommunikation schaffen Vertrauen.</p></div>
        <div class="ed-process__col" data-ed-reveal><span>(C)</span><h3 class="serif">Flexibilität</h3><p>Kurze Reaktionszeiten ermöglichen auch die Bearbeitung kurzfristiger Kundenanfragen.</p></div>
    </div>
</section>

<?php ed_process($rt); ed_emergency($rt); ?>
</main>
<?php ed_footer($rt); ?>
