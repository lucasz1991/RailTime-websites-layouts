<?php
require_once __DIR__ . '/site-shell.php';

function rt_segment_hero(array $rt, int $theme): void {
    $titles = [2 => 'Sicher. Flexibel. Deutschlandweit im Einsatz.', 3 => 'Präzision auf der Schiene. Bereit für jeden Einsatz.', 5 => 'Kompetent, flexibel und bundesweit im Einsatz'];
    $title = $titles[$theme] ?? $rt['about_title'];
    if ($theme === 1): ?>
<section class='rt-hero'>
    <video <?= rt_video_attrs($rt, false) ?>><source src='<?= rt_video($rt['assets']['hero_video']) ?>' type='video/mp4'></video>
    <div class='rt-intro-logo'>
        <div class='rt-logo-lockup rt-logo-lockup--hero'>
            <div class='rt-logo-theatre'>
                <span class='rt-logo-orbit rt-logo-orbit--outer' aria-hidden='true'></span>
                <span class='rt-logo-orbit rt-logo-orbit--inner' aria-hidden='true'></span>
                <span class='rt-logo-scan' aria-hidden='true'></span>
                <div class='rt-logo-3d' data-rt-logo-3d data-logo-variant='noir-signal' data-model-src='<?= rt_project_url('Codex/logo/d1/rt-logo.glb') ?>' role='img' aria-label='Dreidimensionales RT-Logo'>
                    <canvas aria-hidden='true'></canvas>
                    <img class='rt-logo-3d__fallback' src='<?= rt_project_url('Codex/logo/d1/rt-logo.svg') ?>' alt='' aria-hidden='true'>
                </div>
            </div>
            <img class='rt-logo-wordmark' src='<?= rt_image('logo-txt.png') ?>' alt='Rail Time GmbH'>
            <span class='rt-logo-status' aria-hidden='true'><i></i> Signal verbunden · bundesweit</span>
        </div>
    </div>
    <div class='rt-hero-copy'>
        <p class='rt-kicker'>RT Rail Time GmbH</p>
        <h1 class='rt-display'><?= $title ?></h1>
        <p class='max-w-2xl text-base md:text-lg leading-relaxed mb-8'><?= $rt['claim'] ?></p>
        <a class='rt-button' href='#leistungen'>Leistungen entdecken →</a>
    </div>
    <?php rt_video_scroll_cue(); ?>
</section>
<?php return; endif; ?>
<section class="rt-hero"><video <?= rt_video_attrs($rt, !in_array($theme, [1, 3, 5], true)) ?>><source src="<?= rt_video($rt['assets']['hero_video']) ?>" type="video/mp4"></video><div class="rt-intro-logo"><img src="<?= rt_image($rt['assets']['logo_dark']) ?>" alt="RT Rail Time GmbH"></div><div class="rt-hero-copy"><p class="rt-kicker"><?= $theme === 3 ? 'Einsatzakte / Deutschlandweit' : 'RT Rail Time GmbH' ?></p><h1 class="rt-display"><?= $title ?></h1><p class="max-w-2xl text-base md:text-lg leading-relaxed mb-8"><?= $rt['claim'] ?></p><a class="rt-button" href="#leistungen">Leistungen entdecken →</a></div><?php if (in_array($theme, [1, 3, 5], true)) rt_video_scroll_cue(); ?></section>
<?php }

function rt_segment_metrics(array $rt): void { ?><section class="rt-metrics" id="content-start"><?php foreach ($rt['metrics'] as $i => $metric): ?><div><small>0<?= $i + 1 ?></small><strong><?= $metric['number'] ?></strong><span><?= $metric['label'] ?></span></div><?php endforeach ?></section><?php }

function rt_segment_intro(array $rt, int $theme = 0): void {
    if ($theme === 1): ?>
<section class="rt-section rt-intro-section rt-intro-section--noir">
    <div class="rt-intro-section__media" data-reveal>
        <img src="<?= rt_image($rt['assets']['intro_image']) ?>" alt="Wagenmeister bei der technischen Prüfung eines Güterwagens" loading="lazy" decoding="async">
        <span><b>56+</b> erfahrene Wagenmeister</span>
    </div>
    <div class="rt-intro-section__copy">
        <p class="rt-kicker">RT Rail Time GmbH</p>
        <h2 class="rt-title" data-reveal><?= $rt['claim'] ?></h2>
        <p class="rt-lead"><?= $rt['about_copy'] ?></p>
        <a class="rt-text-link" href="ueber-uns.html">Das Unternehmen kennenlernen <span>→</span></a>
    </div>
</section>
<?php return; endif; ?>
<section class="rt-section rt-intro-section"><p class="rt-kicker">RT Rail Time GmbH</p><h2 class="rt-title" data-reveal><?= $rt['claim'] ?></h2><p class="rt-lead"><?= $rt['about_copy'] ?></p></section><?php
}

function rt_segment_services(array $rt): void { ?><section class="rt-section alt" id="leistungen"><p class="rt-kicker">Unsere Leistungen</p><h2 class="rt-title" data-reveal>Fünf Leistungsbereiche für einen sicheren Eisenbahnbetrieb</h2><div class="rt-service-grid"><?php foreach ($rt['services'] as $i => $service): ?><a class="rt-card service" href="leistungen.html#<?= $service['slug'] ?>"><img src="<?= rt_image($rt['assets']['service_images'][$i]) ?>" alt="<?= htmlspecialchars($service['title']) ?>"><div><span>0<?= $i + 1 ?></span><h3><?= $service['title'] ?></h3><b>Mehr erfahren →</b></div></a><?php endforeach ?></div></section><?php }

function rt_segment_process(array $rt): void { ?><section class="rt-section"><p class="rt-kicker">Zusammenarbeit</p><h2 class="rt-title" data-reveal>Von der Anfrage bis zum zuverlässig ausgeführten Einsatz.</h2><div class="rt-process"><?php foreach ($rt['process'] as $i => $step): ?><div><b>0<?= $i + 1 ?></b><p><?= $step ?></p></div><?php endforeach ?></div></section><?php }

function rt_segment_team(array $rt): void { ?><section class="rt-split"><img src="<?= rt_image($rt['assets']['team_image']) ?>" alt="Rail Time im Einsatz"><div class="bg-gray-900 text-white"><p class="rt-kicker">Unser Team</p><h2 class="rt-title" data-reveal>56+ qualifizierte Wagenmeister – bundesweit verfügbar</h2><p class="leading-relaxed text-gray-300">Unsere Mitarbeiter werden regelmäßig fortgebildet und vereinen Qualifikationen aus Wagenprüfung, Bahnbetrieb, Qualitätsmanagement, Gefahrgut und Schadwagenmanagement.</p></div></section><?php }

function rt_segment_map_technology(array $rt, int $theme): void {
    if ($theme === 1): ?><section class="rt-map-tech rt-map-tech--noir"><div class="rt-map-tech__map"><?php include __DIR__ . '/../modules/germany-map.php'; ?></div><div class="rt-map-tech__copy"><p class="rt-kicker">Ausrüstung &amp; Technik</p><h2 class="rt-title" data-reveal>Für sichere und effiziente Einsätze vorbereitet</h2><ul class="rt-equipment-list"><?php foreach (array_filter(array_map('trim', explode('·', $rt['equipment']))) as $item): ?><li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach ?></ul></div></section><?php
    elseif ($theme === 5): ?><section class="rt-map-tech"><div class="rt-map-tech__map"><?php include __DIR__ . '/../modules/germany-map.php'; ?></div><div class="rt-map-tech__copy"><p class="rt-kicker">Ausrüstung &amp; Technik</p><h2 class="rt-title" data-reveal>Für sichere und effiziente Einsätze vorbereitet</h2><p class="rt-lead"><?= $rt['equipment'] ?></p><img src="<?= rt_image($rt['assets']['equipment_image']) ?>" alt="Ausrüstung und Technik"></div></section><?php
    else: include __DIR__ . '/../modules/germany-map.php'; ?><section class="rt-split"><div><p class="rt-kicker">Ausrüstung &amp; Technik</p><h2 class="rt-title" data-reveal>Für sichere und effiziente Einsätze vorbereitet</h2><p class="rt-lead"><?= $rt['equipment'] ?></p></div><img src="<?= rt_image($rt['assets']['equipment_image']) ?>" alt="Ausrüstung und Technik"></section><?php endif;
}

function rt_segment_emergency(array $rt): void { ?><section class="rt-emergency"><div><p class="rt-kicker">Notfalldienst 24/7</p><h2 class="rt-title"><?= $rt['emergency_title'] ?></h2><p><?= $rt['emergency_copy'] ?></p></div><a class="rt-button" href="tel:<?= $rt['phone_href'] ?>">Notfalldienst anrufen →</a></section><?php }
