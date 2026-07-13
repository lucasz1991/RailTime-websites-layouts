<?php require __DIR__ . '/partials.php';
$rt = ad_header('Impressum', false, 'Impressum der RT Rail Time GmbH – Angaben gemäß § 5 TMG.'); ?>
<main class="ad-subpage">

<section class="ad-legal-hero">
    <p class="ad-eyebrow">Rechtliches</p>
    <h1>Impressum</h1>
</section>

<article class="ad-legal">
    <h2>Angaben gemäß § 5 TMG</h2>
    <p>Handelsregister:</p>
    <p>Registergericht:</p>
    <p><strong>Vertreten durch:</strong></p>
    <h2>Kontakt</h2>
    <p>Telefon: <?= $rt['phone'] ?><br>E-Mail: <?= $rt['email'] ?></p>
    <h2>Umsatzsteuer-ID</h2>
    <h2>Angaben zur Berufshaftpflichtversicherung</h2>
    <p><strong>Name und Sitz des Versicherers:</strong></p>
    <h2>Verbraucherstreitbeilegung/Universalschlichtungsstelle</h2>
    <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
    <h2>Haftung für Inhalte</h2>
    <p>Als Diensteanbieter sind wir gemäß § 7 Abs. 1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich.</p>
    <h2>Haftung für Links</h2>
    <p>Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben.</p>
    <h2>Urheberrecht</h2>
    <p>Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht.</p>
</article>

</main>
<?php ad_footer($rt); ?>
