<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/contact.php';

function wf_emergency(array $rt): void
{
    ?>
<section class="ig-emergency rt-emergency">
    <div>
        <span class="ig-code">ALERT / 24-7</span>
        <p class="ig-kicker">Notfalldienst 24/7</p>
        <h2><?= wf_escape($rt['emergency_title']) ?></h2>
        <p><?= wf_escape($rt['emergency_copy']) ?></p>
        <a class="ig-button" href="tel:<?= wf_escape($rt['phone_href']) ?>">Notfalldienst anrufen <b class="rt-action-arrow" aria-hidden="true"></b></a>
    </div>
    <figure>
        <img src="<?= wf_escape(rt_image('s2.jpg')) ?>"<?= rt_image_size_attrs('s2.jpg') ?> alt="Technische Untersuchung an einem Güterwagen" loading="lazy" decoding="async">
        <figcaption>Technische Unterstützung / bundesweit</figcaption>
    </figure>
</section>
<?php
}

function wf_page_hero(array $rt, string $image, string $code, string $kicker, string $title, string $copy, string $buttonLabel = 'Einsatz anfragen'): void
{
    ?>
<section class="ig-page-hero" style="--image:url('<?= wf_escape(rt_image($image)) ?>')">
    <div>
        <span class="ig-code"><?= wf_escape($code) ?></span>
        <p class="ig-kicker"><?= wf_escape($kicker) ?></p>
        <h1><?= wf_escape($title) ?></h1>
        <p><?= wf_escape($copy) ?></p>
        <a class="ig-button" href="<?= wf_escape(wf_route_url('contact')) ?>"><?= wf_escape($buttonLabel) ?> <b class="rt-action-arrow" aria-hidden="true"></b></a>
    </div>
    <aside aria-label="Rail Time in Zahlen">
        <?php foreach ($rt['metrics'] as $metric): ?>
        <div><strong><?= wf_escape($metric['number']) ?></strong><span><?= wf_escape($metric['label']) ?></span></div>
        <?php endforeach ?>
    </aside>
</section>
<?php
}

function wf_services(): void
{
    $rt = rt_document_start('Leistungen', 3);
    ?>
<main class="ig-subpage" id="main-content">
    <?php wf_page_hero(
        $rt,
        's4.jpg',
        'SERVICE SYSTEM / 01–05',
        'Unsere Leistungen',
        'Leistungen für sichere Abläufe im Eisenbahngüterverkehr',
        'Professionelle Wagenmeister-Dienstleistungen, kurzfristige Notfalleinsätze und technische Unterstützung im kombinierten Verkehr.'
    ); ?>

    <section class="ig-module-menu" aria-labelledby="service-menu-title">
        <header><p class="ig-kicker">Leistungsbereiche</p><h2 id="service-menu-title">Fünf Leistungen. Ein verlässlicher Ansprechpartner.</h2></header>
        <div>
            <?php foreach ($rt['services'] as $index => $service): $image = $rt['assets']['service_images'][$index]; ?>
            <a data-service-tile href="#<?= wf_escape($service['slug']) ?>">
                <span>0<?= $index + 1 ?></span>
                <img src="<?= wf_escape(rt_image($image)) ?>"<?= rt_image_size_attrs($image) ?> alt="<?= wf_escape($service['title']) ?>" loading="lazy" decoding="async">
                <h3><?= wf_escape($service['title']) ?></h3>
                <b class="rt-action-arrow" aria-hidden="true"></b>
            </a>
            <?php endforeach ?>
        </div>
    </section>

    <section class="ig-accordion" aria-labelledby="service-detail-title">
        <header><span class="ig-code">DETAIL / ACCORDION</span><p class="ig-kicker">Leistungen im Detail</p><h2 id="service-detail-title">Strukturiert. Nachvollziehbar. Einsatzbereit.</h2></header>
        <?php foreach ($rt['services'] as $index => $service):
            $image = $rt['assets']['service_images'][$index];
            $buttonId = 'service-button-' . ($index + 1);
            $panelId = 'service-panel-' . ($index + 1);
        ?>
        <article id="<?= wf_escape($service['slug']) ?>" data-accordion-item>
            <h3>
                <button id="<?= $buttonId ?>" type="button" aria-expanded="false" aria-controls="<?= $panelId ?>">
                    <span>0<?= $index + 1 ?></span>
                    <strong><?= wf_escape($service['title']) ?></strong>
                    <small><?= $index === 1 ? 'NOTFALL / 24-7' : 'SERVICE / DE' ?></small>
                    <i class="accordion-plus" aria-hidden="true">+</i>
                </button>
            </h3>
            <div id="<?= $panelId ?>" data-panel role="region" aria-labelledby="<?= $buttonId ?>">
                <div class="ig-panel">
                    <img src="<?= wf_escape(rt_image($image)) ?>"<?= rt_image_size_attrs($image) ?> alt="<?= wf_escape($service['title']) ?>" loading="lazy" decoding="async">
                    <div>
                        <h4><?= wf_escape($service['title']) ?></h4>
                        <p><?= wf_escape($service['copy']) ?></p>
                        <ol><li>Anfrage und Einsatzdaten aufnehmen</li><li>Qualifikation und Ausrüstung abstimmen</li><li>Einsatz vor Ort durchführen</li><li>Befund und Rückmeldung dokumentieren</li></ol>
                        <a href="<?= wf_escape(wf_route_url('contact')) ?>">Einsatz anfragen <b class="rt-action-arrow" aria-hidden="true"></b></a>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach ?>
    </section>

    <section class="ig-process compact" aria-labelledby="service-process-title">
        <header><p class="ig-kicker">Ablauf</p><h2 id="service-process-title">Von der Anfrage bis zur Rückmeldung.</h2></header>
        <div><?php foreach ($rt['process'] as $index => $step): ?><article><b>0<?= $index + 1 ?></b><span></span><p><?= wf_escape($step) ?></p></article><?php endforeach ?></div>
    </section>
    <?php wf_emergency($rt); ?>
</main>
<?php
    rt_footer($rt);
    rt_document_end();
}

function wf_about(): void
{
    $rt = rt_document_start('Über uns', 3);
    ?>
<main class="ig-subpage" id="main-content">
    <?php wf_page_hero(
        $rt,
        's3.jpg',
        'COMPANY / RAILTIME',
        'Ihr Partner im Eisenbahnbetrieb',
        'Über RT Rail Time GmbH',
        'Kompetent, flexibel und mit hohem Anspruch an Sicherheit, Qualität und Kundenzufriedenheit.'
    ); ?>

    <section class="ig-about-detail">
        <figure><img src="<?= wf_escape(rt_image('s1.jpg')) ?>"<?= rt_image_size_attrs('s1.jpg') ?> alt="Wagenmeister von RT Rail Time im Einsatz" loading="lazy" decoding="async"><figcaption>RT / Mitarbeiter im Einsatz</figcaption></figure>
        <div><span class="ig-code">PROFILE / 01</span><p class="ig-kicker">Im Detail</p><h2>Maßgeschneiderte Lösungen für den Eisenbahngüterverkehr.</h2><p>Die RT Rail Time GmbH ist Ihr kompetenter und flexibler Partner für Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr. Mit hohem Anspruch an Sicherheit und Qualität bieten wir passende Lösungen für unterschiedliche Herausforderungen im Bereich der Wagenmeisterdienstleistungen.</p><p>Unser Handeln ist auf die Zufriedenheit unserer Kunden ausgerichtet. Präzise Arbeitsweise, zuverlässige Kommunikation und schnelle Reaktion bilden die Grundlage unserer Zusammenarbeit.</p></div>
    </section>

    <section class="ig-values" aria-labelledby="values-title">
        <header><p class="ig-kicker">Unsere Philosophie</p><h2 id="values-title">Vertrauen entsteht durch transparente und zuverlässige Zusammenarbeit.</h2></header>
        <div>
            <article><b>01</b><h3>Sicherheit &amp; Qualität</h3><p>Nachhaltiges Qualitätsmanagement unterstützt Leistungen auf hohem fachlichem Niveau.</p></article>
            <article><b>02</b><h3>Transparenz</h3><p>Klare Abstimmung und nachvollziehbare Kommunikation schaffen Vertrauen.</p></article>
            <article><b>03</b><h3>Flexibilität</h3><p>Kurze Reaktionszeiten ermöglichen auch die Bearbeitung kurzfristiger Kundenanfragen.</p></article>
        </div>
    </section>

    <section class="ig-process" aria-labelledby="about-process-title">
        <header><p class="ig-kicker">Zusammenarbeit</p><h2 id="about-process-title">Von der Anfrage bis zum zuverlässig ausgeführten Einsatz.</h2></header>
        <div><?php foreach ($rt['process'] as $index => $step): ?><article><b>0<?= $index + 1 ?></b><span></span><p><?= wf_escape($step) ?></p></article><?php endforeach ?></div>
    </section>
    <?php wf_emergency($rt); ?>
</main>
<?php
    rt_footer($rt);
    rt_document_end();
}

function wf_contact(): void
{
    wf_start_session();
    $rt = rt_document_start('Kontakt', 3);
    ?>
<main class="ig-subpage" id="main-content">
    <?php wf_page_hero(
        $rt,
        's1.jpg',
        'CONTACT / OPERATIONS',
        'Kontakt & Einsatzanfrage',
        'Direkt erreichbar. Deutschlandweit im Einsatz.',
        'Ob planbare Wagenmeister-Dienstleistung oder kurzfristiger Notfalleinsatz: Teilen Sie uns Einsatzort, gewünschte Leistung und Zeitpunkt mit.',
        'Einsatz anfragen'
    ); ?>

    <section class="ig-contact" aria-labelledby="contact-title">
        <aside><span class="ig-code">REQUEST / NEW</span><p class="ig-kicker">Ihre Anfrage</p><h2 id="contact-title">Schnelle Abstimmung für einen sicheren Eisenbahnbetrieb.</h2><p>Je vollständiger die Einsatzangaben sind, desto schneller können wir Ihre Anfrage einordnen und bearbeiten.</p><dl><div><dt>Notfalldienst 24/7</dt><dd><a href="tel:<?= wf_escape($rt['phone_href']) ?>"><?= wf_escape($rt['phone']) ?></a></dd></div><div><dt>E-Mail &amp; Einsatzanfrage</dt><dd><a href="mailto:<?= wf_escape($rt['email']) ?>"><?= wf_escape($rt['email']) ?></a></dd></div><div><dt>Firmensitz</dt><dd><?= wf_escape($rt['address']) ?></dd></div></dl></aside>
        <?php wf_render_contact_form($rt); ?>
    </section>
    <?php wf_emergency($rt); ?>
</main>
<?php
    rt_footer($rt);
    rt_document_end();
}

function wf_legal(string $type): void
{
    $imprint = $type === 'imprint';
    $rt = rt_document_start($imprint ? 'Impressum' : 'Datenschutzerklärung', 3);
    ?>
<main class="ig-subpage" id="main-content">
    <section class="ig-legal-hero"><span class="ig-code">LEGAL / <?= $imprint ? 'IMPRINT' : 'PRIVACY' ?></span><p class="ig-kicker">Rechtliches</p><h1><?= $imprint ? 'Impressum' : 'Datenschutzerklärung' ?></h1></section>
    <article class="ig-legal">
    <?php if ($imprint): ?>
        <h2>Angaben gemäß § 5 DDG</h2>
        <p><strong><?= wf_escape($rt['company']) ?></strong><br><?= wf_escape($rt['postal_address']['street']) ?><br><?= wf_escape($rt['postal_address']['postal_code'] . ' ' . $rt['postal_address']['locality']) ?><br>Deutschland</p>
        <p><strong>Vertreten durch:</strong><br><?= wf_legal_value('managing_director') ?></p>
        <h2>Kontakt</h2>
        <p>Telefon: <a href="tel:<?= wf_escape($rt['phone_href']) ?>"><?= wf_escape($rt['phone']) ?></a><br>E-Mail: <a href="mailto:<?= wf_escape($rt['email']) ?>"><?= wf_escape($rt['email']) ?></a></p>
        <h2>Registereintrag</h2>
        <p>Registergericht: <?= wf_legal_value('register_court') ?><br>Registernummer: <?= wf_legal_value('register_number') ?></p>
        <h2>Umsatzsteuer-Identifikationsnummer</h2>
        <p><?= wf_legal_value('vat_id') ?></p>
        <h2>Berufshaftpflichtversicherung</h2>
        <p>Versicherer: <?= wf_legal_value('liability_insurer') ?><br>Anschrift: <?= wf_legal_value('liability_insurer_address') ?></p>
        <h2>Verbraucherstreitbeilegung</h2>
        <p>Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.</p>
        <h2>Haftung für Inhalte</h2>
        <p>Wir sind für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Eine Verpflichtung zur Überwachung übermittelter oder gespeicherter fremder Informationen besteht nur im Rahmen der gesetzlichen Vorgaben.</p>
        <h2>Haftung für Links</h2>
        <p>Unser Angebot kann Links zu externen Websites Dritter enthalten. Auf deren Inhalte haben wir keinen Einfluss; für diese Inhalte ist stets der jeweilige Anbieter verantwortlich.</p>
        <h2>Urheberrecht</h2>
        <p>Die durch den Seitenbetreiber erstellten Inhalte und Werke auf dieser Website unterliegen dem deutschen Urheberrecht. Eine Verwertung außerhalb der gesetzlichen Grenzen bedarf der vorherigen Zustimmung.</p>
    <?php else: ?>
        <h2>1. Verantwortliche Stelle</h2>
        <p><strong><?= wf_escape($rt['company']) ?></strong><br><?= wf_escape($rt['postal_address']['street']) ?><br><?= wf_escape($rt['postal_address']['postal_code'] . ' ' . $rt['postal_address']['locality']) ?><br>Telefon: <a href="tel:<?= wf_escape($rt['phone_href']) ?>"><?= wf_escape($rt['phone']) ?></a><br>E-Mail: <a href="mailto:<?= wf_escape($rt['email']) ?>"><?= wf_escape($rt['email']) ?></a></p>
        <h2>2. Hosting und Server-Protokolle</h2>
        <p>Beim Aufruf dieser Website verarbeitet der Webserver technisch erforderliche Verbindungsdaten, insbesondere IP-Adresse, Zeitpunkt, aufgerufene Adresse, übertragene Datenmenge, Referrer sowie Browser- und Betriebssysteminformationen. Die Verarbeitung dient der sicheren und stabilen Bereitstellung der Website.</p>
        <p><strong>Hosting-Anbieter:</strong> <?= wf_legal_value('hosting_provider') ?><br><strong>Speicherdauer der Server-Protokolle:</strong> <?= wf_legal_value('server_log_retention') ?></p>
        <h2>3. Kontaktaufnahme und Kontaktformular</h2>
        <p>Wenn Sie uns per E-Mail, Telefon oder Kontaktformular kontaktieren, verarbeiten wir Ihre Angaben zur Bearbeitung Ihrer Anfrage und für mögliche Anschlussfragen. Das Formular übermittelt die eingegebenen Daten per E-Mail an RT Rail Time; eine zusätzliche Speicherung in einer Website-Datenbank ist in diesem Paket nicht vorgesehen.</p>
        <h2>4. Technisch notwendige Sitzung</h2>
        <p>Für den Schutz des Kontaktformulars wird eine technisch notwendige Sitzung mit einem temporären Cookie verwendet. Sie enthält ein Sicherheitsmerkmal gegen missbräuchliche Formularaufrufe und wird beim Schließen des Browsers beendet.</p>
        <h2>5. Externe Dienste</h2>
        <p>Schriftarten, Skripte, das 3D-Modell und Medien werden in diesem Website-Paket lokal bereitgestellt. Eine Webanalyse oder ein Werbe-Tracking ist nicht eingebunden.</p>
        <h2>6. Ihre Rechte</h2>
        <p>Sie können im Rahmen der gesetzlichen Voraussetzungen Auskunft, Berichtigung, Löschung, Einschränkung der Verarbeitung sowie Datenübertragbarkeit verlangen und einer Verarbeitung widersprechen. Zudem besteht ein Beschwerderecht bei einer zuständigen Datenschutzaufsichtsbehörde.</p>
        <h2>7. Aktualität</h2>
        <p>Diese Datenschutzerklärung ist auf den Funktionsumfang des bereitgestellten Website-Pakets abgestimmt. Änderungen am Hosting, Formularversand oder an eingebundenen Diensten müssen hier vor Veröffentlichung nachvollzogen werden.</p>
    <?php endif ?>
    </article>
</main>
<?php
    rt_footer($rt);
    rt_document_end();
}
