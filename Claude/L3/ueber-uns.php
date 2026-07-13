<?php require __DIR__ . '/partials.php';
$rt = ad_header('Über uns', false,
    'Die RT Rail Time GmbH ist Ihr kompetenter, flexibler Partner für Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr – mit hohem Anspruch an Sicherheit und Qualität.'); ?>
<main class="ad-subpage">

<?php ad_page_hero($rt, 's3.jpg', 'Ihr Partner im Eisenbahnbetrieb',
    'Über RT Rail Time GmbH',
    'Kompetent, flexibel und mit höchstem Anspruch an Sicherheit, Qualität und Kundenzufriedenheit.',
    'Einsatz anfragen', ad_href('kontakt.html')); ?>

<!-- Unternehmensprofil -->
<section class="ad-about-detail">
    <figure data-ad-reveal="left">
        <img src="<?= ad_image('s1.jpg') ?>" alt="Wagenmeister von RT Rail Time" loading="lazy" decoding="async">
        <figcaption>RT / Mitarbeiter im Einsatz</figcaption>
    </figure>
    <div data-ad-reveal="right">
        <p class="ad-eyebrow">Im Detail</p>
        <h2>Maßgeschneiderte Lösungen für den Eisenbahngüterverkehr.</h2>
        <p>Die RT Rail Time GmbH ist Ihr kompetenter und flexibler Partner für Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr. Mit hohem Anspruch an Sicherheit und Qualität bieten wir maßgeschneiderte Lösungen für unterschiedliche Herausforderungen im Bereich der Wagenmeisterdienstleistungen.</p>
        <p>Unser Handeln ist auf die Zufriedenheit unserer Kunden ausgerichtet. Präzise Arbeitsweise, zuverlässige Kommunikation und schnelle Reaktion bilden die Grundlage unserer Zusammenarbeit.</p>
    </div>
</section>

<!-- Philosophie -->
<section class="ad-values">
    <header class="ad-section-head" data-ad-reveal="up">
        <p class="ad-eyebrow">Unsere Philosophie</p>
        <h2>Vertrauen entsteht durch transparente und zuverlässige Zusammenarbeit.</h2>
    </header>
    <div>
        <article data-ad-reveal="up"><b>01</b><h3>Sicherheit &amp; Qualität</h3><p>Nachhaltiges Qualitätsmanagement unterstützt Leistungen auf hohem fachlichem Niveau.</p></article>
        <article data-ad-reveal="up"><b>02</b><h3>Transparenz</h3><p>Klare Abstimmung und nachvollziehbare Kommunikation schaffen Vertrauen.</p></article>
        <article data-ad-reveal="up"><b>03</b><h3>Flexibilität</h3><p>Kurze Reaktionszeiten ermöglichen auch die Bearbeitung kurzfristiger Kundenanfragen.</p></article>
    </div>
</section>

<?php ad_process($rt); ?>
<?php ad_emergency($rt); ?>
</main>
<?php ad_footer($rt); ?>
