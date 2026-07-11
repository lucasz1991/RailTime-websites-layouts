<?php require __DIR__ . '/partials.php'; $rt = cl_header('Über uns'); ?>
<main class="cl-sub">

<section class="cl-pagehead cl-pagehead--image">
    <div>
        <p class="cl-label cl-label--light">Ihr Partner im Eisenbahnbetrieb</p>
        <h1>Über RT Rail Time GmbH</h1>
        <p class="cl-pagehead__copy">Die RT Rail Time GmbH ist Ihr kompetenter und flexibler Partner für Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr.</p>
        <a class="cl-btn" href="kontakt">Einsatz anfragen</a>
    </div>
    <figure><img src="<?= cl_image('s4.jpg') ?>" alt="Rail Time im Einsatz"></figure>
</section>

<section class="cl-section cl-about">
    <p class="cl-label" data-cl-reveal>Im Detail</p>
    <h2 class="cl-h2" data-cl-reveal>Maßgeschneiderte Lösungen für den Eisenbahngüterverkehr.</h2>
    <div class="cl-about__cols">
        <p data-cl-reveal>Die RT Rail Time GmbH ist Ihr kompetenter und flexibler Partner für Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr. Mit hohem Anspruch an Sicherheit und Qualität bieten wir maßgeschneiderte Lösungen für unterschiedliche Herausforderungen im Bereich der Wagenmeisterdienstleistungen.</p>
        <p data-cl-reveal>Unser Handeln ist auf die Zufriedenheit unserer Kunden ausgerichtet. Präzise Arbeitsweise, zuverlässige Kommunikation und schnelle Reaktion bilden die Grundlage unserer Zusammenarbeit.</p>
    </div>
</section>

<section class="cl-section cl-section--panel">
    <p class="cl-label cl-label--light" data-cl-reveal>Unsere Philosophie</p>
    <h2 class="cl-h2" data-cl-reveal>Vertrauen entsteht durch transparente und zuverlässige Zusammenarbeit.</h2>
    <div class="cl-values">
        <article data-cl-reveal><span>A</span><h3>Sicherheit &amp; Qualität</h3><p>Nachhaltiges Qualitätsmanagement unterstützt Leistungen auf hohem fachlichem Niveau.</p></article>
        <article data-cl-reveal><span>B</span><h3>Transparenz</h3><p>Klare Abstimmung und nachvollziehbare Kommunikation schaffen Vertrauen.</p></article>
        <article data-cl-reveal><span>C</span><h3>Flexibilität</h3><p>Kurze Reaktionszeiten ermöglichen auch die Bearbeitung kurzfristiger Kundenanfragen.</p></article>
    </div>
</section>

<?php cl_process($rt); cl_emergency($rt); ?>
</main>
<?php cl_footer($rt); ?>
