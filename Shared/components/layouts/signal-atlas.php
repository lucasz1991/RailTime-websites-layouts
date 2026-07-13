<?php
require_once __DIR__.'/../site-shell.php';

function rt3_home_v2(): void { $rt=rt_document_start('Signal Atlas',3,true); ?>
<main class="sa-main">
<section class="sa-hero sa-hero--intro rt-hero is-video-intro-playing" aria-label="Rail Time Startvideo">
    <video <?= rt_video_attrs($rt, true, false, 'auto') ?> data-hero-playback="intro-once"><source src="<?= rt_video($rt['assets']['hero_video']) ?>" type="video/mp4"></video>
    <div class="sa-hero__shade"></div>
    <div class="rt-intro-logo">
        <div class="sa-hero__brand" role="img" aria-label="RT Rail Time GmbH">
            <div class="sa-logo-stage" aria-hidden="true">
                <span class="sa-orbit sa-orbit--one"></span>
                <div class="rt-logo-3d sa-logo-3d" data-rt-logo-3d data-logo-variant="full-spin" data-logo-wait-for-reveal data-model-src="<?= rt_project_url('Codex/logo/d2/rt-logo.glb') ?>">
                    <canvas aria-hidden="true"></canvas>
                    <img class="rt-logo-3d__fallback" src="<?= rt_project_url('Codex/logo/d2/rt-logo.svg') ?>" alt="" aria-hidden="true">
                </div>
            </div>
            <img class="rt-logo-wordmark" src="<?= rt_image('logo-txt.png') ?>" alt="" aria-hidden="true">
        </div>
    </div>
    <button class="sa-video-start" type="button" data-intro-start>Startvideo abspielen</button>
    <div class="sa-hero__status" aria-hidden="true"><b>Deutschlandweit im Einsatz</b><i></i><b>24/7 Notfalldienst</b></div>
</section>
<section class="sa-intro" id="content-start"><div class="sa-intro__lead sa-motion-up"><p class="sa-eyebrow">RT Rail Time GmbH <span></span> Eisenbahng&uuml;terverkehr</p><h1>Ihr verl&auml;sslicher Partner f&uuml;r professionelle Wagenmeister-Dienstleistungen im Eisenbahng&uuml;terverkehr.</h1></div><figure class="sa-intro__image sa-motion-right"><img src="<?= rt_image('s3.jpg') ?>" alt="Wagenmeister im Einsatz"><figcaption>Kompetent. Flexibel. Bundesweit im Einsatz.</figcaption></figure><div class="sa-intro__facts sa-motion-up"><?php foreach($rt['metrics'] as $metric): ?><div><strong><?= $metric['number'] ?></strong><span><?= $metric['label'] ?></span></div><?php endforeach ?></div><a class="sa-arrow-link" href="kontakt.html">Einsatz anfragen <b>&rarr;</b></a></section>
<section class="sa-rail"><div><span>Sicher</span><i></i><span>Flexibel</span><i></i><span>Rund um die Uhr</span><i></i><span>Bundesweit im Einsatz</span><i></i><span>Sicher</span><i></i><span>Flexibel</span></div></section>
<section class="sa-services" id="leistungen"><header class="sa-section-head sa-motion-up"><p class="sa-eyebrow">01 <span></span> Unsere Leistungen</p><h2>F&uuml;nf Leistungsbereiche f&uuml;r einen sicheren Eisenbahnbetrieb</h2><a class="sa-arrow-link" href="leistungen.html">Alle Leistungen <b>&rarr;</b></a></header><div class="sa-services__grid"><?php foreach($rt['services'] as $i=>$service): ?><a href="leistungen.html#<?= $service['slug'] ?>" class="sa-service sa-motion-up"><span>0<?= $i+1 ?></span><figure><img src="<?= rt_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title'],ENT_QUOTES,'UTF-8') ?>"></figure><h3><?= $service['title'] ?></h3><b>&rarr;</b></a><?php endforeach ?></div></section>
<section class="sa-team"><div class="sa-team__visual sa-motion-left"><figure><img src="<?= rt_image('s4.jpg') ?>" alt="Mitarbeiter im Bahnbetrieb"></figure><figure><img src="<?= rt_image('s1.jpg') ?>" alt="Wagenmeister von RT Rail Time"></figure></div><div class="sa-team__copy sa-motion-right"><p class="sa-eyebrow">02 <span></span> &Uuml;ber RT Rail Time</p><h2><?= $rt['about_title'] ?></h2><p><?= $rt['about_copy'] ?></p><a class="sa-arrow-link" href="ueber-uns.html">Unternehmen kennenlernen <b>&rarr;</b></a><div class="sa-team__seal"><strong>60+</strong><span>qualifizierte<br>Wagenmeister</span></div></div></section>
<section class="sa-process"><header class="sa-section-head sa-motion-up"><p class="sa-eyebrow">03 <span></span> Zusammenarbeit</p><h2>Von der Anfrage bis zum zuverl&auml;ssig ausgef&uuml;hrten Einsatz.</h2></header><div class="sa-process__steps"><?php foreach($rt['process'] as $i=>$step): ?><article class="sa-motion-up"><b>0<?= $i+1 ?></b><span></span><p><?= $step ?></p></article><?php endforeach ?></div></section>
<section class="sa-network"><div class="sa-network__map sa-motion-left"><?php include __DIR__.'/../../modules/germany-map.php'; ?></div><div class="sa-network__copy sa-motion-right"><p class="sa-eyebrow">04 <span></span> Ausr&uuml;stung &amp; Technik</p><h2>F&uuml;r sichere und effiziente Eins&auml;tze vorbereitet</h2><p><?= $rt['equipment'] ?></p><a class="sa-arrow-link" href="kontakt.html">Technik anfragen <b>&rarr;</b></a></div></section>
<section class="sa-emergency"><figure class="sa-motion-left"><img src="<?= rt_image('s2.jpg') ?>" alt="Technische Untersuchung am G&uuml;terwagen"></figure><div class="sa-motion-right"><p class="sa-eyebrow">Notfalldienst 24/7</p><h2><?= $rt['emergency_title'] ?></h2><p><?= $rt['emergency_copy'] ?></p><a class="sa-button" href="tel:<?= $rt['phone_href'] ?>">Notfalldienst anrufen <b>&rarr;</b></a></div></section>
</main><?php rt_footer($rt); rt_document_end(true); }
