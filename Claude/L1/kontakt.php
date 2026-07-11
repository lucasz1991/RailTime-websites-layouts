<?php require __DIR__ . '/partials.php'; $rt = cl_header('Kontakt'); ?>
<main class="cl-sub">

<section class="cl-pagehead">
    <div>
        <p class="cl-label cl-label--light">Kontakt &amp; Einsatzanfrage</p>
        <h1>Direkt erreichbar. Deutschlandweit im Einsatz.</h1>
        <p class="cl-pagehead__copy">Ob planbare Wagenmeister-Dienstleistung oder kurzfristiger Notfalleinsatz: Teilen Sie uns Einsatzort, gewünschte Leistung und Zeitpunkt mit.</p>
    </div>
    <div class="cl-pagehead__contactcard">
        <p class="cl-label cl-label--light">Direktkontakt</p>
        <a href="tel:<?= $rt['phone_href'] ?>"><?= $rt['phone'] ?></a>
        <a href="mailto:<?= $rt['email'] ?>"><?= $rt['email'] ?></a>
        <small>Notfalldienst rund um die Uhr</small>
    </div>
</section>

<section class="cl-section cl-contact">
    <div>
        <p class="cl-label" data-cl-reveal>Kontaktformular</p>
        <h2 class="cl-h2" data-cl-reveal>Schildern Sie uns Ihren geplanten oder dringenden Einsatz.</h2>
        <p class="cl-lead" data-cl-reveal>Je vollständiger die Einsatzangaben sind, desto schneller können wir Ihre Anfrage einordnen und bearbeiten.</p>
    </div>
    <form class="cl-form" action="mailto:<?= $rt['email'] ?>" method="post" enctype="text/plain" data-cl-reveal>
        <label>Name<input name="Name" required></label>
        <label>E-Mail<input type="email" name="E-Mail" required></label>
        <label>Telefon<input type="tel" name="Telefon"></label>
        <label>Gewünschte Leistung<select name="Leistung"><?php foreach ($rt['services'] as $service): ?><option><?= $service['title'] ?></option><?php endforeach ?></select></label>
        <label>Einsatzort<input name="Einsatzort"></label>
        <label class="cl-form__full">Nachricht<textarea rows="6" name="Nachricht" required></textarea></label>
        <button class="cl-btn cl-form__submit" type="submit">Anfrage per E-Mail vorbereiten</button>
    </form>
</section>

<?php cl_emergency($rt); ?>
</main>
<?php cl_footer($rt); ?>
