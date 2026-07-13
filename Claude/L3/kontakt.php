<?php require __DIR__ . '/partials.php';
$rt = ad_header('Kontakt', false,
    'Kontakt zur RT Rail Time GmbH: Einsatzanfrage, Notfalldienst 24/7 unter 0160 1881848, kontakt@rail-time.de – deutschlandweit im Einsatz.'); ?>
<main class="ad-subpage">

<?php ad_page_hero($rt, 's1.jpg', 'Kontakt &amp; Einsatzanfrage',
    'Direkt erreichbar. Deutschlandweit im Einsatz.',
    'Ob planbare Wagenmeister-Dienstleistung oder kurzfristiger Notfalleinsatz: Teilen Sie uns Einsatzort, gewünschte Leistung und Zeitpunkt mit.',
    $rt['phone'], 'tel:' . $rt['phone_href']); ?>

<!-- Kontaktdaten + Formular (Regel 17) -->
<section class="ad-contact">
    <aside data-ad-reveal="left">
        <p class="ad-eyebrow">Ihre Anfrage</p>
        <h2>Schnelle Abstimmung für einen sicheren Eisenbahnbetrieb.</h2>
        <p>Je vollständiger die Einsatzangaben sind, desto schneller können wir Ihre Anfrage einordnen und bearbeiten.</p>
        <dl>
            <div><dt>Notfalldienst 24/7</dt><dd><a href="tel:<?= $rt['phone_href'] ?>"><?= $rt['phone'] ?></a></dd></div>
            <div><dt>E-Mail &amp; Einsatzanfrage</dt><dd><a href="mailto:<?= $rt['email'] ?>"><?= $rt['email'] ?></a></dd></div>
            <div><dt>Firmensitz</dt><dd><?= $rt['address'] ?></dd></div>
        </dl>
    </aside>
    <form action="mailto:<?= $rt['email'] ?>" method="post" enctype="text/plain" data-ad-reveal="right">
        <header><b>RT / EINSATZANFRAGE</b><span>Pflichtfelder *</span></header>
        <label>Name *<input name="Name" autocomplete="name" required></label>
        <label>E-Mail *<input name="E-Mail" type="email" autocomplete="email" required></label>
        <label>Telefon<input name="Telefon" type="tel" autocomplete="tel"></label>
        <label>Einsatzort<input name="Einsatzort"></label>
        <label class="full">Gewünschte Leistung<select name="Leistung"><?php foreach ($rt['services'] as $service): ?><option><?= $service['title'] ?></option><?php endforeach ?></select></label>
        <label class="full">Nachricht *<textarea name="Nachricht" rows="6" required></textarea></label>
        <button class="ad-button" type="submit">Anfrage per E-Mail vorbereiten <b>&rarr;</b></button>
    </form>
</section>

<?php ad_emergency($rt); ?>
</main>
<?php ad_footer($rt); ?>
