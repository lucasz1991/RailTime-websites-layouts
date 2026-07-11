<?php require __DIR__ . '/partials.php'; $rt = ed_header('Kontakt'); ?>
<main class="ed-sub">

<section class="ed-pagehead">
    <p class="ed-label">Kontakt &amp; Einsatzanfrage</p>
    <h1 class="serif">Direkt erreichbar. <em>Deutschlandweit</em> im Einsatz.</h1>
    <p class="ed-pagehead__copy">Ob planbare Wagenmeister-Dienstleistung oder kurzfristiger Notfalleinsatz: Teilen Sie uns Einsatzort, gewünschte Leistung und Zeitpunkt mit.</p>
    <div class="ed-pagehead__contact">
        <a class="ed-link" href="tel:<?= $rt['phone_href'] ?>"><?= $rt['phone'] ?></a>
        <a class="ed-link" href="mailto:<?= $rt['email'] ?>"><?= $rt['email'] ?></a>
    </div>
</section>

<!-- Formular als heller Papier-Einschub (Regel 17) -->
<section class="ed-section ed-section--paper ed-contact">
    <div class="ed-contact__aside">
        <p class="ed-label" data-ed-reveal>Kontaktformular</p>
        <h2 class="ed-h2 serif" data-ed-reveal>Schildern Sie uns Ihren <em>geplanten oder dringenden</em> Einsatz.</h2>
        <p data-ed-reveal>Je vollständiger die Einsatzangaben sind, desto schneller können wir Ihre Anfrage einordnen und bearbeiten.</p>
    </div>
    <form class="ed-form" action="mailto:<?= $rt['email'] ?>" method="post" enctype="text/plain" data-ed-reveal>
        <label><span>(01) Name</span><input name="Name" required></label>
        <label><span>(02) E-Mail</span><input type="email" name="E-Mail" required></label>
        <label><span>(03) Telefon</span><input type="tel" name="Telefon"></label>
        <label><span>(04) Gewünschte Leistung</span><select name="Leistung"><?php foreach ($rt['services'] as $service): ?><option><?= $service['title'] ?></option><?php endforeach ?></select></label>
        <label><span>(05) Einsatzort</span><input name="Einsatzort"></label>
        <label class="ed-form__full"><span>(06) Nachricht</span><textarea rows="6" name="Nachricht" required></textarea></label>
        <button class="ed-form__submit" type="submit">Anfrage per E-Mail vorbereiten —</button>
    </form>
</section>

<?php ed_emergency($rt); ?>
</main>
<?php ed_footer($rt); ?>
