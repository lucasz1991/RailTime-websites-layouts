<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';

function wf_home(): void
{
    $rt = rt_document_start('Startseite', 3, true);
    ?>
<main class="sa-main" id="main-content">
    <section class="sa-hero sa-hero--intro rt-hero is-video-intro-playing" aria-label="Rail Time Startvideo">
        <video <?= rt_video_attrs($rt, false, false, 'auto') ?> data-hero-playback="intro-once" data-hero-start-progress="0.20" data-hero-playback-rate="1.15" data-logo-reveal-at="5.08" data-hero-dock-delay="1500"><source src="<?= wf_escape(rt_video($rt['assets']['hero_video'])) ?>" type="video/mp4"></video>
        <div class="sa-hero__shade"></div>
        <div class="rt-intro-logo">
            <div class="sa-hero__brand" role="img" aria-label="RT Rail Time GmbH">
                <div class="sa-logo-stage" aria-hidden="true">
                    <span class="rt-logo-orbit rt-logo-orbit--outer"></span>
                    <span class="rt-logo-orbit rt-logo-orbit--inner"></span>
                    <span class="rt-logo-scan"></span>
                    <div class="rt-logo-3d sa-logo-3d" data-rt-logo-3d data-logo-variant="full-spin" data-logo-wait-for-reveal data-model-src="<?= wf_escape(wf_url('assets/models/rt-logo.glb')) ?>">
                        <canvas aria-hidden="true"></canvas>
                        <img class="rt-logo-3d__fallback" src="<?= wf_escape(wf_url('assets/logo/rt-logo.svg')) ?>" alt="" width="512" height="512" aria-hidden="true">
                    </div>
                </div>
            </div>
        </div>
        <button class="sa-video-start" type="button" data-intro-start>Startvideo abspielen</button>
        <div class="sa-hero__status" aria-hidden="true"><b>Deutschlandweit im Einsatz</b><i></i><b>24/7 Notfalldienst</b></div>
    </section>

    <section class="sa-intro" id="unternehmen">
        <div class="sa-intro__lead sa-motion-up"><p class="sa-eyebrow">RT Rail Time GmbH <span></span> Eisenbahngüterverkehr</p><h1>Ihr verlässlicher Partner für professionelle Wagenmeister-Dienstleistungen im Eisenbahngüterverkehr.</h1></div>
        <figure class="sa-intro__image sa-motion-right"><img src="<?= wf_escape(rt_image('s3.jpg')) ?>"<?= rt_image_size_attrs('s3.jpg') ?> alt="Wagenmeister von RT Rail Time im Einsatz" loading="lazy" decoding="async"><figcaption>Kompetent. Flexibel. Bundesweit im Einsatz.</figcaption></figure>
        <div class="sa-intro__facts sa-motion-up"><?php foreach ($rt['metrics'] as $metric): ?><div><strong><?= wf_escape($metric['number']) ?></strong><span><?= wf_escape($metric['label']) ?></span></div><?php endforeach ?></div>
        <a class="sa-arrow-link" href="<?= wf_escape(wf_route_url('contact')) ?>">Einsatz anfragen <b class="rt-action-arrow" aria-hidden="true"></b></a>
    </section>

    <section class="sa-rail" aria-label="Unsere Stärken"><div><span>Sicher</span><i></i><span>Flexibel</span><i></i><span>Rund um die Uhr</span><i></i><span>Bundesweit im Einsatz</span><i></i><span>Sicher</span><i></i><span>Flexibel</span></div></section>

    <section class="sa-services" id="leistungen">
        <header class="sa-section-head sa-motion-up"><p class="sa-eyebrow">01 <span></span> Unsere Leistungen</p><h2>Fünf Leistungsbereiche für einen sicheren Eisenbahnbetrieb</h2><a class="sa-arrow-link" href="<?= wf_escape(wf_route_url('services')) ?>">Alle Leistungen <b class="rt-action-arrow" aria-hidden="true"></b></a></header>
        <div class="sa-services__grid"><?php foreach ($rt['services'] as $i => $service): $image = $rt['assets']['service_images'][$i]; ?><a href="<?= wf_escape(wf_route_url('services', $service['slug'])) ?>" class="sa-service sa-motion-up"><span>0<?= $i + 1 ?></span><figure><img src="<?= wf_escape(rt_image($image)) ?>"<?= rt_image_size_attrs($image) ?> alt="<?= wf_escape($service['title']) ?>" loading="lazy" decoding="async"></figure><h3><?= wf_escape($service['title']) ?></h3><b class="rt-action-arrow" aria-hidden="true"></b></a><?php endforeach ?></div>
    </section>

    <section class="sa-team">
        <div class="sa-team__visual sa-motion-left"><figure><img src="<?= wf_escape(rt_image('s4.jpg')) ?>"<?= rt_image_size_attrs('s4.jpg') ?> alt="Mitarbeiter von RT Rail Time im Bahnbetrieb" loading="lazy" decoding="async"></figure><figure><img src="<?= wf_escape(rt_image('s1.jpg')) ?>"<?= rt_image_size_attrs('s1.jpg') ?> alt="Wagenmeister von RT Rail Time" loading="lazy" decoding="async"></figure></div>
        <div class="sa-team__copy sa-motion-right"><p class="sa-eyebrow">02 <span></span> Über RT Rail Time</p><h2><?= wf_escape($rt['about_title']) ?></h2><p><?= wf_escape($rt['about_copy']) ?></p><a class="sa-arrow-link" href="<?= wf_escape(wf_route_url('about')) ?>">Unternehmen kennenlernen <b class="rt-action-arrow" aria-hidden="true"></b></a><div class="sa-team__seal"><strong>60+</strong><span>qualifizierte<br>Wagenmeister</span></div></div>
    </section>

    <section class="sa-process">
        <header class="sa-section-head sa-motion-up"><p class="sa-eyebrow">03 <span></span> Zusammenarbeit</p><h2>Von der Anfrage bis zum zuverlässig ausgeführten Einsatz.</h2></header>
        <div class="sa-process__steps"><?php foreach ($rt['process'] as $i => $step): ?><article class="sa-motion-up"><b>0<?= $i + 1 ?></b><span></span><p><?= wf_escape($step) ?></p></article><?php endforeach ?></div>
    </section>

    <section class="sa-network">
        <div class="sa-network__map sa-motion-left"><?php include dirname(__DIR__) . '/germany-map.php'; ?></div>
        <div class="sa-network__copy sa-motion-right"><p class="sa-eyebrow">04 <span></span> Ausrüstung &amp; Technik</p><h2>Für sichere und effiziente Einsätze vorbereitet</h2><p><?= wf_escape($rt['equipment']) ?></p><a class="sa-arrow-link" href="<?= wf_escape(wf_route_url('contact')) ?>">Technik anfragen <b class="rt-action-arrow" aria-hidden="true"></b></a></div>
    </section>

    <section class="sa-emergency">
        <figure class="sa-motion-left"><img src="<?= wf_escape(rt_image('s2.jpg')) ?>"<?= rt_image_size_attrs('s2.jpg') ?> alt="Technische Untersuchung an einem Güterwagen" loading="lazy" decoding="async"></figure>
        <div class="sa-motion-right"><p class="sa-eyebrow">Notfalldienst 24/7</p><h2><?= wf_escape($rt['emergency_title']) ?></h2><p><?= wf_escape($rt['emergency_copy']) ?></p><a class="sa-button" href="tel:<?= wf_escape($rt['phone_href']) ?>">Notfalldienst anrufen <b class="rt-action-arrow" aria-hidden="true"></b></a></div>
    </section>
</main>
<?php
    rt_footer($rt);
    rt_document_end(true);
}
