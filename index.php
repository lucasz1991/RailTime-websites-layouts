<?php
require_once __DIR__ . '/Shared/config/layout-registry.php';

// Keep the implementation folder private in the visible public URL. The
// presentation itself stays the single source of truth; only its project-root
// relative Shared links need one fewer parent segment below /logo-mockup/.
if (($_GET['rt_public'] ?? '') === 'logo-mockup') {
    if (($_GET['rt_logo_redirect'] ?? '') === '1') {
        header('Location: ' . rt_project_url('logo-mockup/'), true, 301);
        exit;
    }

    $logoOverviewFile = __DIR__ . '/Codex/logo/index.html';

    if (!is_file($logoOverviewFile)) {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Logo-Mockup nicht gefunden.';
        exit;
    }

    $logoOverview = file_get_contents($logoOverviewFile);
    if ($logoOverview === false) {
        http_response_code(500);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Logo-Mockup konnte nicht geladen werden.';
        exit;
    }

    $logoOverview = str_replace(
        ['../../Shared/', 'href="../../"'],
        ['../Shared/', 'href="../"'],
        $logoOverview
    );

    header('Content-Type: text/html; charset=UTF-8');
    echo $logoOverview;
    exit;
}

// All public routes enter through index.php. Some managed Plesk setups deny
// direct HTTP access to every other PHP file, while internal includes remain
// available to the application.
if (isset($_GET['layout'])) {
    require __DIR__ . '/layout-preview.php';
    exit;
}

function rt_layout_meta(string $dir, array $fallbackNames): array {
    $name = $fallbackNames[basename($dir)] ?? basename($dir);
    $desc = 'Layout-Entwurf nach den verbindlichen Projektregeln.';
    $rows = [];

    foreach (['layout.csv', 'Layout Rules.csv'] as $candidate) {
        $file = $dir . '/' . $candidate;
        if (!is_file($file)) {
            continue;
        }
        foreach (array_map(fn($l) => str_getcsv($l, ';', '"', '\\'), file($file)) as $row) {
            if (!isset($row[0], $row[1])) {
                continue;
            }
            $key = trim((string)$row[0]);
            $value = trim((string)$row[1]);
            if ($key !== '' && $value !== '') {
                $rows[mb_strtolower($key)] = $value;
            }
        }
    }

    if (!empty($rows['layoutname'])) {
        $name = $rows['layoutname'];
    } elseif (!empty($rows['layout'])) {
        $name = preg_replace('/^[^-]+-\s*/', '', $rows['layout']) ?: $rows['layout'];
    }

    foreach (['konzept', 'beschreibung', 'startseite', 'seitenaufbau'] as $key) {
        if (!empty($rows[$key])) {
            $desc = $rows[$key];
            break;
        }
    }

    $badgeText = [];
    foreach (['konzept', 'seitenaufbau', 'startseite', 'bewegung', 'typografie', 'leistungen', 'unterseiten'] as $key) {
        if (!empty($rows[$key])) {
            $badgeText[] = $rows[$key];
        }
    }

    $joined = mb_strtolower(implode(' ', $badgeText));
    $map = [
        'Video Intro' => ['video', 'autoplay'],
        'Logo Intro' => ['logo'],
        'ScrollMagic' => ['scrollmagic'],
        'Akkordeon' => ['akkordeon', 'accordion'],
        'Kachelraster' => ['kacheln', 'karten', 'raster', 'grid'],
        'Modulaufbau' => ['module', 'modul'],
        'Editorial' => ['editorial', 'magazin'],
        'Technisch' => ['technisch', 'leitstand', 'engineering', 'industrial', 'neo-brutalismus'],
        'Statusleisten' => ['status', 'kennzahlen', 'datenleiste'],
        'Deutschlandkarte' => ['deutschlandkarte', 'karte'],
        'Kontaktformular' => ['formular', 'kontakt'],
        'Space Mono' => ['space mono'],
        'DM Mono' => ['dm mono'],
        'Manrope' => ['manrope'],
        'Asymmetrisch' => ['asymmetr'],
        'Horizontal' => ['horizontal'],
        'Prozesslinie' => ['prozess', 'spurfolge'],
        'Helle Akzente' => ['helle', 'weiss', 'weiß'],
    ];

    $badges = [];
    foreach ($map as $label => $needles) {
        foreach ($needles as $needle) {
            if (mb_strpos($joined, $needle) !== false) {
                $badges[] = $label;
                break;
            }
        }
        if (count($badges) >= 6) {
            break;
        }
    }

    if (!$badges) {
        $badges = ['RailTime CI', 'Mehrseitig', 'Startvideo'];
    }

    return [$name, $desc, array_slice(array_values(array_unique($badges)), 0, 6)];
}

$layoutNames = [
    'Layout Entwurf 1' => 'Noir Motion',
    'Layout Entwurf 2' => 'Signal Compact',
    'Layout Entwurf 3' => 'Industrial Grid',
    'Layout Entwurf 4' => 'Atlas Editorial',
    'Layout Entwurf 5' => 'Horizon Signature',
];

$entries = array_values(rt_layout_registry());
$baseUrl = rt_project_base_url();
?>
<!doctype html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>RailTime Layout-Entwuerfe</title>
<script>
/* Bewusste Notfalloption: ?lite=1 deaktiviert GPU-lastige Intro-Effekte. */
(function () {
    var lite = new URLSearchParams(location.search).get('lite') === '1';
    if (lite) document.documentElement.classList.add('lite-mode');
})();
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
<link rel='preconnect' href='https://esm.sh' crossorigin>
<link rel='icon' type='image/svg+xml' href='<?= rt_project_url('Shared/assets/icons/favicon.svg') ?>?v=1'>
<link rel='icon' type='image/png' sizes='32x32' href='<?= rt_project_url('Shared/assets/icons/favicon-32x32.png') ?>?v=1'>
<link rel='apple-touch-icon' sizes='180x180' href='<?= rt_project_url('Shared/assets/icons/apple-touch-icon.png') ?>?v=1'>
<link rel='preload' href='<?= rt_project_url('Codex/logo/d2/rt-logo.glb') ?>' as='fetch' type='model/gltf-binary' crossorigin>
<link rel='stylesheet' href='<?= rt_project_url('Shared/styles/logo-3d.css') ?>?v=5'>
<script type='module' src='<?= rt_project_url('Shared/scripts/logo-3d.js') ?>?v=9'></script>
<style>
:root{--red:#e4002b;--ink:#0b0e13;--panel:#111820;--line:rgba(255,255,255,.12)}
*{box-sizing:border-box}
html,body{max-width:100%;overflow-x:clip}
body.intro-active{overflow:hidden}
.intro-screen{position:fixed;z-index:20;inset:0;display:grid;place-items:center;overflow:hidden;isolation:isolate;background:#05070a;color:#fff;transition:opacity 1.25s cubic-bezier(.4,0,.2,1),visibility 1.25s ease}
.intro-screen::before{content:"";position:absolute;z-index:-3;inset:-30%;background:radial-gradient(circle at 50% 45%,rgba(228,0,43,.3),transparent 22%),conic-gradient(from 210deg at 50% 50%,#05070a,#160812,#05070a,#240713,#05070a);animation:introAtmosphere 7s ease-in-out both}
.intro-screen::after{content:"";position:absolute;z-index:5;inset:0;pointer-events:none;background:linear-gradient(90deg,rgba(0,0,0,.76),transparent 18%,transparent 82%,rgba(0,0,0,.76)),radial-gradient(ellipse at center,transparent 25%,rgba(0,0,0,.62) 100%);mix-blend-mode:multiply}
.intro-screen.is-leaving{opacity:0;visibility:hidden;pointer-events:none;transform:scale(1.025)}
.intro-screen__grid{position:absolute;z-index:-2;inset:0;opacity:0;background-image:linear-gradient(rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px);background-size:8vw 8vw;mask-image:linear-gradient(90deg,transparent,#000 24%,#000 76%,transparent);animation:introGrid 2s .35s ease forwards}
.intro-screen__beam{position:absolute;z-index:-1;top:-30%;left:50%;width:2px;height:160%;background:linear-gradient(transparent,#ff2449 38%,#fff 50%,#ff2449 62%,transparent);box-shadow:0 0 70px 22px rgba(228,0,43,.2);transform:translateX(-50%) scaleY(0);animation:introBeam 2.2s .2s cubic-bezier(.16,1,.3,1) forwards}
.intro-screen__rails{position:absolute;z-index:1;inset:0;pointer-events:none}.intro-screen__rails i{position:absolute;top:50%;width:38vw;height:1px;background:linear-gradient(90deg,transparent,rgba(255,63,91,.72));transform:scaleX(0)}.intro-screen__rails i:first-child{left:0;transform-origin:right;animation:introRail 1.25s 1.05s ease forwards}.intro-screen__rails i:last-child{right:0;transform-origin:left;background:linear-gradient(90deg,rgba(255,63,91,.72),transparent);animation:introRail 1.25s 1.05s ease forwards}
.intro-screen__inner{position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;width:min(1040px,calc(100% - 44px));text-align:center}
.intro-screen__logo{display:none}
.intro-screen__brand{order:-1;margin:0 auto 16px;opacity:0;transform:translateY(-28px) scale(.88);animation:introLogo 1.25s .28s cubic-bezier(.16,1,.3,1) forwards}
.intro-screen__brand .rt-logo-3d{width:clamp(104px,8.5vw,154px)}
.intro-screen__brand .rt-logo-wordmark{width:clamp(178px,14vw,270px);margin-top:-7px}
.intro-screen__mark{width:112px;height:3px;margin:0 auto 30px;background:linear-gradient(90deg,transparent,#ff2347 20%,#fff 50%,#ff2347 80%,transparent);transform:scaleX(0);transform-origin:center;animation:introLine 1.15s .55s cubic-bezier(.16,1,.3,1) forwards}
.intro-screen__kicker{margin:0 0 18px;color:#ff6680;font:500 11px 'DM Mono',monospace;letter-spacing:.32em;text-transform:uppercase;opacity:0;transform:translateY(12px);animation:introUp .8s .75s ease forwards}
.intro-screen h2{margin:0;color:#fff;font-size:clamp(58px,10vw,148px);line-height:.74;letter-spacing:-.075em;text-transform:uppercase}
.intro-screen h2 span{display:block;opacity:0}.intro-screen__title-main{transform:translateY(58px);animation:introTitle 1.3s .95s cubic-bezier(.16,1,.3,1) forwards}.intro-screen__title-accent{color:#ff294d;transform:translateY(72px);animation:introTitle 1.35s 1.12s cubic-bezier(.16,1,.3,1) forwards}
.intro-screen__signature{display:grid;justify-items:center;gap:7px;margin:48px 0 0;opacity:0;transform:translateY(22px);animation:introUp 1s 1.75s cubic-bezier(.16,1,.3,1) forwards}.intro-screen__signature span{color:#8f99a6;font:500 10px 'DM Mono',monospace;letter-spacing:.25em;text-transform:uppercase}.intro-screen__signature strong{font-size:clamp(28px,4.5vw,64px);line-height:1;letter-spacing:-.045em;color:#fff;text-shadow:0 0 38px rgba(255,41,77,.24)}.intro-screen__signature em{margin-top:3px;color:#ff5b76;font:500 11px 'DM Mono',monospace;letter-spacing:.18em;text-transform:uppercase;font-style:normal}
.intro-screen__meta{display:flex;justify-content:center;gap:clamp(20px,5vw,70px);margin:38px 0 0;color:#727c88;font:500 9px 'DM Mono',monospace;letter-spacing:.2em;text-transform:uppercase;opacity:0;animation:introUp .9s 2.25s ease forwards}.intro-screen__meta span::before{content:"";display:inline-block;width:18px;height:1px;margin:0 9px 3px 0;background:#e4002b}
.intro-screen__hint{margin:42px 0 0;color:#77818c;font:500 9px 'DM Mono',monospace;letter-spacing:.2em;text-transform:uppercase;opacity:0;animation:introUp .8s 2.65s ease forwards}.intro-screen__hint::after{content:"";display:block;width:1px;height:42px;margin:12px auto 0;background:linear-gradient(#ff294d,transparent);animation:introPulse 1.5s 2.9s infinite}
@keyframes introLine{to{transform:scaleX(1)}}
@keyframes introLogo{to{opacity:1;transform:translateY(0) scale(1)}}
@keyframes introTitle{to{opacity:1;transform:translateY(0)}}
@keyframes introUp{to{opacity:1;transform:translateY(0)}}
@keyframes introGrid{to{opacity:.7;transform:scale(1.04)}}
@keyframes introRail{to{transform:scaleX(1)}}
@keyframes introBeam{0%{transform:translateX(-50%) scaleY(0);opacity:0}40%{opacity:1}100%{transform:translateX(-50%) scaleY(1);opacity:.25}}
@keyframes introAtmosphere{0%{transform:scale(1.2) rotate(-6deg);filter:brightness(.6)}100%{transform:scale(1) rotate(0);filter:brightness(1)}}
@keyframes introPulse{50%{opacity:.2;transform:translateY(7px)}}
@media(prefers-reduced-motion:reduce){.intro-screen *,.intro-screen::before{animation-duration:.01ms!important;animation-delay:0s!important}}
body{margin:0;background:radial-gradient(circle at 82% 0,rgba(228,0,43,.22) 0,transparent 30%),#0b0e13;color:#f5f6f7;font-family:Manrope,Arial,sans-serif}
.wrap{width:100%;margin:0;padding:1.2vh 0 2vh}
.wrap>.eyebrow,.wrap>h1,.wrap>.intro{margin-left:clamp(20px,5vw,96px);margin-right:clamp(20px,5vw,96px)}
.eyebrow{color:#ff4765;font:500 12px 'DM Mono',monospace;letter-spacing:.14em;text-transform:uppercase}
h1{max-width:850px;margin:16px 0 12px;font-size:clamp(38px,5vw,68px);line-height:.97;letter-spacing:-.055em}
.intro{margin:0 0 26px;max-width:860px;color:#c5cad0;font-size:14px;line-height:1.6}
.carousel{position:relative;width:100%;margin-top:0}
.carousel__head{display:flex;align-items:center;justify-content:space-between;gap:18px;margin:0 0 12px;padding:0 clamp(20px,5vw,96px)}
.carousel__head p{margin:0;color:#89939f;font:500 10px 'DM Mono',monospace;letter-spacing:.12em;text-transform:uppercase}
.carousel__head-tools{display:flex;align-items:center;gap:12px}
.brand-overview-link{display:inline-flex;align-items:center;min-height:38px;padding:0 13px;border:1px solid rgba(255,255,255,.16);background:#111820;color:#fff;text-decoration:none;font:500 9px 'DM Mono',monospace;letter-spacing:.1em;text-transform:uppercase;transition:background .2s,border-color .2s}
.brand-overview-link:hover,.brand-overview-link:focus-visible{border-color:var(--red);outline:0;background:var(--red)}
.carousel__controls{display:flex;gap:8px}
.carousel__control{width:42px;height:38px;border:1px solid var(--line);background:#151c24;color:#fff;font-size:22px;line-height:1;cursor:pointer;transition:background .2s,border-color .2s}
.carousel__control:hover{background:var(--red);border-color:var(--red)}
.grid{position:relative;display:block;max-width:100%;overflow:clip;padding:12px 0 20px;cursor:grab;user-select:none;touch-action:pan-y;contain:layout paint;perspective:1800px}
.grid.is-dragging{cursor:grabbing}
.grid__track{display:flex;align-items:stretch;gap:18px;width:max-content;transform-style:preserve-3d;will-change:transform}
.card{position:relative;z-index:1;display:grid;flex:0 0 86vw;height:calc(100dvh - clamp(70px,8vh,112px));min-width:0;min-height:650px;grid-template-rows:auto minmax(0,1fr);border:1px solid var(--line);background:var(--panel);opacity:.7;filter:saturate(.76) brightness(.8);transform:perspective(1800px) translateZ(-70px) scale(.92);transition:border-color .3s ease,opacity .45s ease,filter .45s ease,transform .55s cubic-bezier(.16,1,.3,1),box-shadow .45s ease}
.card.is-before{transform-origin:right center;transform:perspective(1800px) rotateY(8deg) translateZ(-82px) scale(.92)}.card.is-after{transform-origin:left center;transform:perspective(1800px) rotateY(-8deg) translateZ(-82px) scale(.92)}
.card.is-focused{z-index:4;border-color:#ff4965;opacity:1;filter:none;transform:perspective(1800px) rotateY(0) translateZ(0) scale(1);box-shadow:0 30px 90px rgba(0,0,0,.34)}.card:not(.is-focused):hover{opacity:.9;filter:saturate(.92) brightness(.94)}
.card__body{display:flex;min-width:0;flex-direction:column;justify-content:flex-start;padding:24px clamp(24px,3vw,46px) 20px}
.number{color:#ff4965;font:500 12px 'DM Mono',monospace}
.card h2{max-width:450px;margin:10px 0 8px;font-size:clamp(20px,2vw,28px);line-height:1.04;letter-spacing:-.04em}
.badges{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 16px;padding:0;list-style:none}
.badges li{display:inline-flex;align-items:center;padding:7px 10px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.04);color:#eef2f7;font:500 10px 'DM Mono',monospace;letter-spacing:.08em;text-transform:uppercase}
.card p{margin:0 0 18px;color:#c5cad0;font-size:14px;max-width:520px}
.card__actions{display:flex;align-items:center;flex-wrap:wrap;gap:10px 18px;margin-top:auto}.card__open{display:inline-flex;width:fit-content;padding:12px 16px;border:0;background:var(--red);color:#fff;text-decoration:none;font:500 11px 'DM Mono';letter-spacing:.08em;text-transform:uppercase;cursor:pointer}
.card__open:hover{background:#b30022}
.card__preview{display:inline-flex;width:fit-content;margin:0;padding:10px 0;border:0;background:transparent;color:#ff697f;text-decoration:none;font:500 10px 'DM Mono',monospace;letter-spacing:.1em;text-transform:uppercase;cursor:pointer}
.card__preview:hover{color:#fff}
.shots{position:relative;min-width:0;min-height:0;border-top:1px solid var(--line);background:#0d1117}
.iframe-preview figure{margin:0}
.iframe-preview{position:relative;display:block;height:100%;overflow:hidden;background:radial-gradient(circle at 50% 12%,rgba(228,0,43,.2),transparent 42%),linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,0));}
.iframe-preview__refresh{position:absolute;z-index:9;top:12px;right:12px;display:grid;place-items:center;width:38px;height:38px;padding:0;border:1px solid rgba(255,255,255,.18);border-radius:50%;background:rgba(7,10,14,.88);color:#fff;box-shadow:0 10px 28px rgba(0,0,0,.42);opacity:0;pointer-events:none;backdrop-filter:blur(12px);cursor:pointer;transform:scale(.82);transition:background .2s ease,border-color .2s ease,opacity .25s ease,transform .25s ease}.card.is-focused .iframe-preview__refresh{opacity:1;pointer-events:auto;transform:scale(1)}.iframe-preview__refresh:hover,.iframe-preview__refresh:focus-visible{border-color:var(--red);outline:0;background:var(--red);transform:scale(1.07)}.iframe-preview__refresh svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.iframe-preview__refresh.is-refreshing svg{animation:previewRefreshSpin .7s cubic-bezier(.4,0,.2,1)}@keyframes previewRefreshSpin{to{transform:rotate(360deg)}}
.iframe-device{position:absolute;margin:0;padding:6px;border:4px solid #202832;border-radius:12px;background:#05070a;box-shadow:0 14px 32px rgba(0,0,0,.45);overflow:hidden;cursor:zoom-in;transition:box-shadow .3s ease}
.card.is-focused .iframe-device{box-shadow:0 18px 38px rgba(0,0,0,.62)}
.iframe-device:hover,.iframe-device:focus-visible,.card.is-focused .iframe-device:hover,.card.is-focused .iframe-device:focus-visible{outline:0;box-shadow:0 16px 38px rgba(0,0,0,.5),0 0 24px rgba(228,0,43,.18)}
.iframe-device--desktop{z-index:1;top:26px;left:17%;width:66%;padding:6px 6px 28px;aspect-ratio:1.49;overflow:visible;border-radius:13px 13px 4px 4px}.iframe-device--desktop::before{content:"";position:absolute;z-index:-1;left:43%;bottom:-21px;width:14%;height:22px;background:linear-gradient(90deg,#151d27,#3a4655,#151d27)}.iframe-device--desktop::after{content:"";position:absolute;z-index:-1;left:31%;bottom:-27px;width:38%;height:7px;border-radius:9px 9px 3px 3px;background:linear-gradient(90deg,#18212b,#465465,#18212b);box-shadow:0 6px 12px rgba(0,0,0,.42)}.iframe-device--tablet{z-index:3;bottom:56px;left:2%;width:24%;aspect-ratio:.762}.iframe-device--mobile{z-index:4;right:6%;bottom:56px;width:10.5%;aspect-ratio:.5;border-radius:15px}
.iframe-device__viewport{position:absolute;inset:6px;overflow:hidden;background:#05070a}.iframe-device--desktop .iframe-device__viewport{inset:6px 6px 28px}
.iframe-device__viewport iframe{position:absolute;top:0;left:0;border:0;transform-origin:top left;pointer-events:none;background:#05070a;will-change:transform;opacity:0}
/* Bildschirme sind schwarz ("aus") und schalten sich nach dem Laden aller Geräte synchron ein */
.iframe-device__viewport iframe.is-on{opacity:1;animation:rtScreenOn .62s cubic-bezier(.25,.6,.3,1) both}
@keyframes rtScreenOn{0%{opacity:0;filter:brightness(3.4) saturate(.15)}12%{opacity:1;filter:brightness(2.6) saturate(.3)}45%{filter:brightness(1.5) saturate(.8)}100%{opacity:1;filter:brightness(1) saturate(1)}}
.iframe-device small{display:none}
.iframe-preview__nav{position:absolute;z-index:8;left:50%;bottom:8px;display:flex;align-items:center;gap:10px;transform:translateX(-50%);padding:5px;border:1px solid rgba(255,255,255,.13);background:rgba(7,10,14,.88);box-shadow:0 10px 24px rgba(0,0,0,.35);backdrop-filter:blur(10px)}.iframe-preview__nav button{display:grid;place-items:center;width:31px;height:28px;border:1px solid rgba(255,255,255,.16);background:#141b23;color:#fff;font-size:17px;cursor:pointer}.iframe-preview__nav button:hover{border-color:var(--red);background:var(--red)}.iframe-preview__page{min-width:104px;text-align:center;color:#dce2e9;font:500 9px 'DM Mono',monospace;letter-spacing:.1em;text-transform:uppercase}
.empty{padding:34px;border:1px dashed #4b5563;color:#929aa5}
.live-preview{width:100vw;max-width:none;height:100dvh;max-height:none;margin:0;padding:0;border:0;background:#080b10;color:#fff}
.live-preview::backdrop{background:rgba(0,0,0,.85);backdrop-filter:blur(9px)}
.live-preview__shell{display:grid;grid-template-rows:auto 1fr;height:100%}
.live-preview__bar{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:68px;padding:11px clamp(16px,3vw,44px);border-bottom:1px solid var(--line);background:rgba(12,16,22,.94);backdrop-filter:blur(18px)}
.live-preview__meta{min-width:0}.live-preview__eyebrow{display:block;margin-bottom:4px;color:#ff536d;font:500 10px 'DM Mono',monospace;letter-spacing:.14em;text-transform:uppercase}.live-preview__title{margin:0;font-size:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.live-preview__actions{display:flex;align-items:center;gap:8px}.live-preview__device{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line);padding:9px 11px;background:#111820;color:#cbd2db;font:500 10px 'DM Mono',monospace;letter-spacing:.08em;text-transform:uppercase;cursor:pointer}.live-preview__device::before{content:"";display:block;width:16px;height:11px;border:1.5px solid currentColor;border-radius:2px;box-shadow:0 3px 0 -1px currentColor}.live-preview__device[data-preview-device="tablet"]::before{width:10px;height:14px;border-radius:2px;box-shadow:none}.live-preview__device[data-preview-device="mobile"]::before{width:7px;height:14px;border-radius:3px;box-shadow:none}.live-preview__device.is-active,.live-preview__device:hover{border-color:var(--red);background:var(--red);color:#fff}.live-preview__close{width:42px;height:38px;margin-left:10px;border:1px solid var(--line);background:transparent;color:#fff;font-size:22px;cursor:pointer}.live-preview__close:hover{background:#fff;color:#0b0e13}
.live-preview__stage{position:relative;display:grid;place-items:center;min-height:0;overflow:auto;padding:clamp(14px,1.8vw,28px);cursor:zoom-out;background:radial-gradient(circle at 50% 18%,rgba(228,0,43,.16),transparent 36%),linear-gradient(135deg,#090c12,#101720)}
.live-preview__device-shell{--left-bezel:12px;--right-bezel:12px;--top-bezel:12px;--bottom-bezel:34px;position:relative;flex:none;border:0;border-radius:13px;background:linear-gradient(145deg,#3b4653,#11161d 44%,#27313c);box-shadow:0 32px 80px rgba(0,0,0,.64),inset 0 0 0 1px rgba(255,255,255,.14);overflow:visible;cursor:default;transition:width .34s cubic-bezier(.16,1,.3,1),height .34s cubic-bezier(.16,1,.3,1),border-radius .3s ease,background .3s ease}
.live-preview__viewport{position:absolute;z-index:2;left:var(--left-bezel);right:var(--right-bezel);top:var(--top-bezel);bottom:var(--bottom-bezel);overflow:hidden;border-radius:5px;background:#fff;box-shadow:0 0 0 1px rgba(0,0,0,.85),inset 0 0 16px rgba(0,0,0,.16)}.live-preview__viewport iframe{position:absolute;left:0;top:0;border:0;transform-origin:top left;background:#fff;pointer-events:auto;touch-action:auto;overscroll-behavior:contain}
.live-preview__device-shell[data-mode="desktop"]::before{content:"";position:absolute;left:44%;bottom:-27px;width:12%;height:28px;background:linear-gradient(90deg,#111820,#465363 50%,#111820);clip-path:polygon(20% 0,80% 0,100% 100%,0 100%)}.live-preview__device-shell[data-mode="desktop"]::after{content:"";position:absolute;left:32%;bottom:-34px;width:36%;height:8px;border-radius:9px 9px 3px 3px;background:linear-gradient(90deg,#121922,#586575,#121922);box-shadow:0 8px 18px rgba(0,0,0,.52)}
.live-preview__device-shell[data-mode="tablet"]{--left-bezel:14px;--right-bezel:14px;--top-bezel:18px;--bottom-bezel:18px;border-radius:28px;background:linear-gradient(145deg,#4a5562,#0b0f14 52%,#303a46)}.live-preview__device-shell[data-mode="tablet"]::before{content:"";position:absolute;z-index:4;left:50%;top:7px;width:5px;height:5px;border-radius:50%;background:#030508;box-shadow:0 0 0 1px #647080}
.live-preview__device-shell[data-mode="mobile"]{--left-bezel:10px;--right-bezel:10px;--top-bezel:18px;--bottom-bezel:12px;border-radius:36px;background:linear-gradient(145deg,#56616e,#090c10 48%,#313b46);box-shadow:0 32px 90px rgba(0,0,0,.72),inset 0 0 0 1px rgba(255,255,255,.2)}.live-preview__device-shell[data-mode="mobile"]::before{content:"";position:absolute;z-index:5;left:50%;top:7px;width:52px;height:8px;border-radius:12px;background:#030407;transform:translateX(-50%);box-shadow:inset 0 0 0 1px rgba(255,255,255,.05)}.live-preview__device-shell[data-mode="mobile"] .live-preview__viewport{border-radius:24px}
@media(min-width:1800px){.card{flex-basis:88vw;min-height:760px}.card__body{padding-left:clamp(40px,3.2vw,72px);padding-right:clamp(40px,3.2vw,72px)}}
@media(max-width:1050px){.card{flex-basis:94vw;height:calc(100dvh - 76px);min-height:620px;opacity:.72}.card.is-before{transform:perspective(1400px) rotateY(5deg) translateZ(-48px) scale(.94)}.card.is-after{transform:perspective(1400px) rotateY(-5deg) translateZ(-48px) scale(.94)}.iframe-device--desktop{top:18px;left:15%;width:70%}.iframe-device--tablet{top:37%;bottom:auto;left:4%;width:25%}.iframe-device--mobile{top:44%;right:4%;bottom:auto;width:13%}}
/* Lite-Mode: statische Gradients statt animierter Conic-/Blend-Ebenen, kein 3D-Logo,
   keine Perspektiv-Transforms und Backdrop-Filter — verhindert Safari-GPU-Abstürze auf iOS */
html.lite-mode .intro-screen::before{animation:none;inset:0;background:radial-gradient(circle at 50% 42%,rgba(228,0,43,.24),transparent 44%),#05070a}
html.lite-mode .intro-screen::after,html.lite-mode .intro-screen__grid,html.lite-mode .intro-screen__beam,html.lite-mode .intro-screen__rails{display:none}
html.lite-mode .intro-screen__signature strong{text-shadow:none}
html.lite-mode .intro-screen__hint::after{animation:none}
html.lite-mode .rt-logo-3d canvas{display:none}
html.lite-mode .grid{perspective:none}
html.lite-mode .card,html.lite-mode .card.is-before,html.lite-mode .card.is-after{transform:none;filter:none;transition:border-color .3s ease,opacity .35s ease}
html.lite-mode .card{opacity:.78}
html.lite-mode .card.is-focused{opacity:1;box-shadow:none}
html.lite-mode .iframe-preview__refresh,html.lite-mode .iframe-preview__nav,html.lite-mode .live-preview__bar,html.lite-mode .live-preview::backdrop{backdrop-filter:none}
@media(max-width:640px){.wrap{padding-top:1vh}.carousel__head p{display:none}.brand-overview-link{font-size:0;padding:0 12px}.brand-overview-link::before{content:"Logo";font-size:9px}.card{flex-basis:96vw;height:min(760px,88vh);min-height:600px}.card__body{padding:20px 20px 15px}.card p{font-size:13px;margin-bottom:12px}.badges{margin-bottom:12px}.shots{height:auto}.iframe-device--desktop{top:16px;left:7%;width:86%}.iframe-device--tablet,.iframe-device--mobile{display:none}.iframe-preview__page{min-width:86px}.live-preview__bar{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:8px;padding:9px 10px}.live-preview__eyebrow{display:none}.live-preview__title{font-size:14px}.live-preview__actions{gap:4px}.live-preview__device{gap:0;padding:8px 7px;font-size:0}.live-preview__device::before{margin:0}.live-preview__device[data-preview-device="tablet"],.live-preview__device[data-preview-device="mobile"]{display:none}.live-preview__close{width:36px;margin-left:2px}.live-preview__stage{padding:12px}}
</style>
</head>
<body class="intro-active">
<section class="intro-screen" aria-label="Layout Präsentation" role="dialog">
    <div class="intro-screen__grid" aria-hidden="true"></div>
    <div class="intro-screen__beam" aria-hidden="true"></div>
    <div class="intro-screen__rails" aria-hidden="true"><i></i><i></i></div>
    <div class="intro-screen__inner">
        <img class="intro-screen__logo" src="<?= rt_project_url('Shared/assets/images/logo-stacked-darkbg.png') ?>" alt="RailTime GmbH">
        <div class="intro-screen__mark"></div>
        <p class="intro-screen__kicker">RailTime · Layout Präsentation</p>
        <h2><span class="intro-screen__title-main">Layout</span><span class="intro-screen__title-accent">Entwürfe.</span></h2>
        <p class="intro-screen__signature"><span>Präsentiert von</span><strong>Lucas M. Zacharias</strong><em>für RailTime GmbH</em></p>
        <div class="intro-screen__meta" aria-hidden="true"><span>Konzept</span><span>Bewegung</span><span>Präzision</span></div>
        <p class="intro-screen__hint">Die Übersicht wird geöffnet</p>
        <div class='intro-screen__brand rt-logo-lockup'>
            <div class='rt-logo-3d' data-rt-logo-3d data-model-src='<?= rt_project_url('Codex/logo/d2/rt-logo.glb') ?>' role='img' aria-label='Dreidimensionales RT-Logo'>
                <canvas aria-hidden='true'></canvas>
                <img class='rt-logo-3d__fallback' src='<?= rt_project_url('Codex/logo/d2/rt-logo.svg') ?>' alt='' aria-hidden='true'>
            </div>
            <img class='rt-logo-wordmark' src='<?= rt_project_url('Shared/assets/images/logo-txt-darkbg.png') ?>' alt='Rail Time GmbH'>
        </div>
    </div>
</section>
<main class="wrap">

<?php if (!$entries): ?>
<section class="empty">Noch keine Layoutordner gefunden.</section>
<?php else: ?>
<section class="carousel" aria-label="Layout Entwuerfe">
    <div class="carousel__head">
        <p>Ziehe, wische oder nutze die Pfeile &ndash; fortlaufende Auswahl</p>
        <div class="carousel__head-tools">
            <a class="brand-overview-link" href="<?= rt_project_url('logo-mockup/') ?>" target="_blank" rel="noopener">Logo &amp; Favicon Übersicht &nearr;</a>
            <div class="carousel__controls" aria-label="Carousel Steuerung">
                <button class="carousel__control" type="button" data-rail-prev aria-label="Vorherige Layouts">&lsaquo;</button>
                <button class="carousel__control" type="button" data-rail-next aria-label="Naechste Layouts">&rsaquo;</button>
            </div>
        </div>
    </div>
    <div class="grid" aria-label="Layout Karten" data-layout-rail>
<?php foreach ($entries as $entry):
    [$name, $desc, $badges] = rt_layout_meta($entry['dir'], $layoutNames); ?>
<article class="card" data-layout-id="<?= $entry['id'] ?>" data-layout-name="<?= htmlspecialchars($name, ENT_QUOTES) ?>" data-preview-url="layouts/<?= $entry['id'] ?>">
    <div class="card__body">
        <span class="number"><?= str_pad((string)$entry['id'], 2, '0', STR_PAD_LEFT) ?></span>
        <h2><?= htmlspecialchars($name) ?></h2>
        <ul class="badges" aria-label="Layoutmerkmale">
            <?php foreach ($badges as $badge): ?>
            <li><?= htmlspecialchars($badge) ?></li>
            <?php endforeach ?>
        </ul>
        <p><?= htmlspecialchars(mb_strimwidth($desc, 0, 220, '...')) ?></p>
        <div class="card__actions">
            <button class="card__open" type="button" data-open-preview>Vorschau öffnen &rarr;</button>
            <a class="card__preview" href="layouts/<?= $entry['id'] ?>" target="_blank" rel="noopener">Live ansehen &nearr;</a>
        </div>
    </div>
    <div class="shots">
        <div class="iframe-preview">
            <button class="iframe-preview__refresh" type="button" data-preview-refresh aria-label="Alle drei Geräte-Vorschauen neu laden" title="Vorschauen neu laden">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6v5h-5"></path><path d="M18.2 15a7 7 0 1 1-.4-7.5L20 10"></path></svg>
            </button>
            <figure class="iframe-device iframe-device--desktop" data-preview-width="1440" data-preview-height="900" data-preview-device-trigger="desktop" role="button" tabindex="0" aria-label="Desktop-Vorschau vergrößern">
                <div class="iframe-device__viewport"><iframe loading="lazy" data-src="layouts/<?= $entry['id'] ?>" title="Layout <?= htmlspecialchars($name) ?> Desktop Vorschau"></iframe></div>
                <small>desktop</small>
            </figure>
            <figure class="iframe-device iframe-device--tablet" data-preview-width="834" data-preview-height="1112" data-preview-device-trigger="tablet" role="button" tabindex="0" aria-label="Tablet-Vorschau vergrößern">
                <div class="iframe-device__viewport"><iframe loading="lazy" data-src="layouts/<?= $entry['id'] ?>" title="Layout <?= htmlspecialchars($name) ?> Tablet Vorschau"></iframe></div>
                <small>tablet</small>
            </figure>
            <figure class="iframe-device iframe-device--mobile" data-preview-width="390" data-preview-height="844" data-preview-device-trigger="mobile" role="button" tabindex="0" aria-label="Smartphone-Vorschau vergrößern">
                <div class="iframe-device__viewport"><iframe loading="lazy" data-src="layouts/<?= $entry['id'] ?>" title="Layout <?= htmlspecialchars($name) ?> Mobile Vorschau"></iframe></div>
                <small>mobile</small>
            </figure>
            <nav class="iframe-preview__nav" aria-label="Unterseiten der Layoutvorschau" data-preview-pages>
                <button type="button" data-page-prev aria-label="Vorherige Unterseite">&lsaquo;</button>
                <span class="iframe-preview__page" data-page-name>Startseite</span>
                <button type="button" data-page-next aria-label="Nächste Unterseite">&rsaquo;</button>
            </nav>
        </div>
    </div>
</article>
<?php endforeach ?>
</div>
</section>
<?php endif ?>
</main>
<dialog class="live-preview" data-live-preview aria-label="Live Layout Vorschau">
    <div class="live-preview__shell">
        <header class="live-preview__bar">
            <div class="live-preview__meta">
                <span class="live-preview__eyebrow">RailTime &middot; Live-Vergleich</span>
                <h2 class="live-preview__title" data-preview-title>Layout Vorschau</h2>
            </div>
            <div class="live-preview__actions">
                <button type="button" class="live-preview__device is-active" data-preview-device="desktop">Desktop</button>
                <button type="button" class="live-preview__device" data-preview-device="tablet">Tablet</button>
                <button type="button" class="live-preview__device" data-preview-device="mobile">Smartphone</button>
                <button type="button" class="live-preview__close" data-preview-close aria-label="Vorschau schliessen">&times;</button>
            </div>
        </header>
        <div class="live-preview__stage" data-preview-stage>
            <div class="live-preview__device-shell" data-preview-shell data-mode="desktop" role="group" aria-label="Interaktive Desktop-Vorschau">
                <div class="live-preview__viewport"><iframe title="Live Layout Vorschau" data-preview-frame></iframe></div>
            </div>
        </div>
    </div>
</dialog>
<script>
const mobileOverviewQuery = matchMedia('(max-width: 640px)');
const isMobileOverview = () => mobileOverviewQuery.matches;
const isConstrainedPreviewMode = () => isMobileOverview() || matchMedia('(max-width: 760px), (hover: none), (pointer: coarse)').matches;

const fitCardFrames = (scope = document) => {
    scope.querySelectorAll('.iframe-preview').forEach((stage) => {
        const desktop = stage.querySelector('.iframe-device--desktop');
        const tablet = stage.querySelector('.iframe-device--tablet');
        const mobile = stage.querySelector('.iframe-device--mobile');
        if (!desktop || !stage.clientWidth || !stage.clientHeight) return;
        const width = stage.clientWidth;
        const height = stage.clientHeight;
        const setBox = (element, box) => {
            element.style.width = box.width + 'px';
            element.style.height = box.height + 'px';
            element.style.left = box.left + 'px';
            element.style.right = 'auto';
            element.style.top = box.top == null ? 'auto' : box.top + 'px';
            element.style.bottom = box.bottom == null ? 'auto' : box.bottom + 'px';
        };
        if (isMobileOverview()) {
            const top = 16;
            const navReserve = 58;
            const desktopWidth = Math.max(180, Math.min(width * .86, Math.max(180, height - top - navReserve) * 1.49));
            setBox(desktop, { width: desktopWidth, height: desktopWidth / 1.49, left: (width - desktopWidth) / 2, top });
            return;
        }

        if (!tablet || !mobile) return;
        const compact = innerWidth <= 1050 || width <= 900;
        if (!compact) {
            const top = 18;
            const bottom = 54;
            const desktopWidth = Math.max(220, Math.min(width * .60, Math.max(220, height - top - bottom - 68) * 1.49));
            const desktopHeight = desktopWidth / 1.49;
            const desktopLeft = (width - desktopWidth) / 2;
            const tabletWidth = Math.min(desktopWidth * .32, width * .24);
            const mobileWidth = Math.min(desktopWidth * .17, width * .12);
            setBox(desktop, { width: desktopWidth, height: desktopHeight, left: desktopLeft, top });
            setBox(tablet, { width: tabletWidth, height: tabletWidth / .762, left: Math.max(12, desktopLeft - tabletWidth + desktopWidth * .08), bottom });
            setBox(mobile, { width: mobileWidth, height: mobileWidth / .5, left: Math.min(width - mobileWidth - 12, desktopLeft + desktopWidth - mobileWidth * .18), bottom });
            return;
        }

        const narrow = innerWidth <= 640 || width <= 560;
        const top = narrow ? 16 : 18;
        const navReserve = narrow ? 58 : 62;
        const desktopFraction = narrow ? .78 : .74;
        const tabletRatio = narrow ? .35 : .33;
        const mobileRatio = narrow ? .19 : .17;
        const tabletOverlap = .68;
        const mobileOverlap = .62;
        const desktopHeightRatio = 1 / 1.49;
        const tabletHeightRatio = tabletRatio / .762;
        const mobileHeightRatio = mobileRatio / .5;
        const stackRatio = Math.max(
            desktopHeightRatio + tabletHeightRatio * (1 - tabletOverlap),
            desktopHeightRatio + mobileHeightRatio * (1 - mobileOverlap)
        );
        const availableStackHeight = Math.max(180, height - top - navReserve);
        const desktopWidth = Math.max(180, Math.min(width * desktopFraction, availableStackHeight / stackRatio));
        const desktopHeight = desktopWidth / 1.49;
        const desktopLeft = (width - desktopWidth) / 2;
        const tabletWidth = Math.max(68, desktopWidth * tabletRatio);
        const tabletHeight = tabletWidth / .762;
        const mobileWidth = Math.max(38, desktopWidth * mobileRatio);
        const mobileHeight = mobileWidth / .5;
        const desktopBottom = top + desktopHeight;
        const tabletLeft = Math.max(8, desktopLeft - tabletWidth * .65);
        const mobileLeft = Math.min(width - mobileWidth - 8, desktopLeft + desktopWidth - mobileWidth * .35);
        setBox(desktop, { width: desktopWidth, height: desktopHeight, left: desktopLeft, top });
        setBox(tablet, { width: tabletWidth, height: tabletHeight, left: tabletLeft, top: desktopBottom - tabletHeight * tabletOverlap });
        setBox(mobile, { width: mobileWidth, height: mobileHeight, left: mobileLeft, top: desktopBottom - mobileHeight * mobileOverlap });
    });
    scope.querySelectorAll('.iframe-device').forEach((device) => {
        const viewport = device.querySelector('.iframe-device__viewport');
        const frame = device.querySelector('iframe');
        const nativeW = Number(device.dataset.previewWidth || 0);
        const nativeH = Number(device.dataset.previewHeight || 0);
        if (!viewport || !frame || !nativeW || !nativeH || !viewport.clientWidth || !viewport.clientHeight) return;
        const scale = Math.min(viewport.clientWidth / nativeW, viewport.clientHeight / nativeH);
        frame.style.width = nativeW + 'px';
        frame.style.height = nativeH + 'px';
        frame.style.left = ((viewport.clientWidth - nativeW * scale) / 2) + 'px';
        frame.style.top = ((viewport.clientHeight - nativeH * scale) / 2) + 'px';
        frame.style.transform = 'scale(' + scale + ')';
    });
};
const preparePreviewFrame = (frame) => {
    const hideInternalScrollbars = () => {
        try {
            const doc = frame.contentDocument;
            if (!doc?.head || doc.getElementById('railtime-preview-scrollbars')) return;
            const style = doc.createElement('style');
            style.id = 'railtime-preview-scrollbars';
            style.textContent = 'html,body,*{scrollbar-width:none!important;-ms-overflow-style:none!important}*::-webkit-scrollbar{display:none!important;width:0!important;height:0!important;background:transparent!important}';
            doc.head.append(style);
        } catch (_) {}
    };
    if (!frame.dataset.previewPrepared) {
        frame.dataset.previewPrepared = '1';
        frame.addEventListener('load', hideInternalScrollbars);
    }
    hideInternalScrollbars();
};
/* Vor jedem src-Wechsel bewaffnen: Promise, das mit dem load-Event des Frames erfüllt wird */
const armFrameLoad = (frame) => {
    frame.classList.remove('is-on');
    frame.__rtLoadPromise = new Promise((resolve) => {
        frame.addEventListener('load', resolve, { once: true });
        window.setTimeout(resolve, 4500); // Sicherheitsnetz: nie ewig schwarz bleiben
    });
};
/* Synchrones Einschalten: erst wenn ALLE Geräte-Frames der Karte geladen sind
   UND mindestens 260ms seit Kartenfokus vergangen sind, gehen alle Bildschirme
   gleichzeitig an (Regel: keine Verzögerung zwischen den Geräten). */
const powerOnCard = (card) => {
    const frames = [...card.querySelectorAll('.iframe-preview iframe')].filter((frame) => frame.isConnected && frame.hasAttribute('src'));
    if (!frames.length) return;
    const token = Symbol('power-on');
    card.__rtPowerToken = token;
    frames.forEach((frame) => frame.classList.remove('is-on'));
    const loads = frames.map((frame) => frame.__rtLoadPromise || Promise.resolve());
    Promise.all([...loads, new Promise((resolve) => window.setTimeout(resolve, 260))]).then(() => {
        if (card.__rtPowerToken !== token) return;
        requestAnimationFrame(() => {
            if (card.__rtPowerToken !== token) return;
            fitCardFrames(card);
            frames.forEach((frame) => {
                if (!frame.isConnected) return;
                frame.classList.add('is-on');
                /* Animation/Video in allen Frames im selben Moment neu starten */
                try {
                    frame.contentDocument?.querySelectorAll('video').forEach((video) => {
                        try { video.currentTime = 0; } catch (_) {}
                        video.play?.()?.catch?.(() => {});
                    });
                    frame.contentWindow?.dispatchEvent(new frame.contentWindow.CustomEvent('railtime:preview-sync'));
                } catch (_) {}
            });
        });
    });
};
document.querySelectorAll('.iframe-device__viewport').forEach((viewport) => {
    const frame = viewport.querySelector('iframe');
    if (!frame) return;
    viewport.dataset.previewSrc = frame.dataset.src || '';
    viewport.dataset.previewTitle = frame.title || '';
});
const cardPreviewFrames = (card) => [...card.querySelectorAll('.iframe-device__viewport iframe')];
const hydrateCard = (card) => {
    if (!card) return;
    card.querySelectorAll('.iframe-device__viewport').forEach((viewport) => {
        const desktopViewport = viewport.closest('.iframe-device--desktop');
        const mobileViewport = viewport.closest('.iframe-device--mobile');
        const shouldKeepViewport = isMobileOverview()
            ? Boolean(desktopViewport)
            : !isConstrainedPreviewMode() || Boolean(mobileViewport);
        if (!shouldKeepViewport) {
            const frame = viewport.querySelector('iframe');
            if (frame?.isConnected) {
                frame.src = 'about:blank';
                frame.remove();
            }
            return;
        }
        let frame = viewport.querySelector('iframe');
        if (!frame) {
            frame = document.createElement('iframe');
            frame.loading = 'lazy';
            frame.dataset.src = viewport.dataset.previewSrc || card.dataset.previewUrl || ('layouts/' + card.dataset.layoutId);
            frame.title = viewport.dataset.previewTitle || ('Layout ' + card.dataset.layoutId + ' Vorschau');
            viewport.append(frame);
        }
        preparePreviewFrame(frame);
        if (!frame.hasAttribute('src')) {
            armFrameLoad(frame);
            frame.src = frame.dataset.src || card.dataset.previewUrl || ('layouts/' + card.dataset.layoutId);
        } else if (!frame.__rtLoadPromise) {
            frame.__rtLoadPromise = Promise.resolve();
        }
    });
    requestAnimationFrame(() => fitCardFrames(card));
    powerOnCard(card);
};
const unloadCard = (card) => {
    if (!card) return;
    card.__rtPowerToken = null;
    previewScrollStates.delete(card);
    delete card.dataset.contentCenter;
    card.querySelectorAll('.iframe-device__viewport').forEach((viewport) => {
        const frame = viewport.querySelector('iframe');
        if (!frame) return;
        viewport.dataset.previewSrc = frame.dataset.src || viewport.dataset.previewSrc || '';
        viewport.dataset.previewTitle = frame.title || viewport.dataset.previewTitle || '';
        frame.src = 'about:blank';
        frame.remove();
    });
};
const previewScrollStates = new WeakMap();
const bindCardPreviewScroll = (card) => {
    card.addEventListener('wheel', (event) => {
        const frames = [...card.querySelectorAll('.iframe-preview iframe')].filter((frame) => frame.src && frame.src !== 'about:blank');
        if (!frames.length) return;
        const previewDelta = event.deltaY * (event.deltaMode === 1 ? 16 : event.deltaMode === 2 ? innerHeight : 1);
        const introStates = frames.map((frame) => {
            try {
                const win = frame.contentWindow;
                const doc = frame.contentDocument;
                const video = doc.querySelector('[data-hero-video]');
                return {
                    win,
                    locked: doc.body.classList.contains('rt-intro-playing'),
                    introOnce: video?.dataset.heroPlayback === 'intro-once',
                    scrollVideo: video?.dataset.scrollVideoEngine === 'shared-inertial-native-v3'
                };
            } catch (_) { return null; }
        }).filter(Boolean);
        if (introStates.some((state) => state.locked && state.introOnce)) {
            event.preventDefault();
            return;
        }
        const introWindows = introStates.filter((state) => state.locked && state.scrollVideo).map((state) => state.win);
        if (introWindows.length) {
            introWindows.forEach((win) => win.dispatchEvent(new win.CustomEvent('railtime:preview-scroll', { detail: { deltaY: previewDelta } })));
            event.preventDefault();
            return;
        }
        const metrics = frames.map((frame) => {
            try {
                const win = frame.contentWindow;
                const doc = frame.contentDocument;
                const height = Math.max(doc.documentElement.scrollHeight, doc.body?.scrollHeight || 0);
                return height > win.innerHeight ? { win, height, viewport: win.innerHeight } : null;
            } catch (_) { return null; }
        }).filter(Boolean);
        if (!metrics.length) return;

        let state = previewScrollStates.get(card);
        if (!state) {
            const reference = metrics[0];
            const contentCenter = Math.max(0, Math.min(1, (reference.win.scrollY + reference.viewport / 2) / reference.height));
            state = { contentCenter, targetContentCenter: contentCenter, animationFrame: 0, metrics };
            previewScrollStates.set(card, state);
        }
        state.metrics = metrics;
        state.targetContentCenter = Math.max(0, Math.min(1, state.targetContentCenter + event.deltaY * .00042));
        card.dataset.contentCenter = state.targetContentCenter.toFixed(4);

        if (!state.animationFrame) {
            const renderSynchronizedScroll = () => {
                state.contentCenter += (state.targetContentCenter - state.contentCenter) * .18;
                state.metrics.forEach(({ win, height, viewport }) => {
                    const maxScroll = Math.max(0, height - viewport);
                    const targetY = Math.max(0, Math.min(maxScroll, state.contentCenter * height - viewport / 2));
                    win.scrollTo(0, targetY);
                });
                if (Math.abs(state.targetContentCenter - state.contentCenter) > .00015) {
                    state.animationFrame = requestAnimationFrame(renderSynchronizedScroll);
                } else {
                    state.contentCenter = state.targetContentCenter;
                    state.animationFrame = 0;
                }
            };
            state.animationFrame = requestAnimationFrame(renderSynchronizedScroll);
        }
        event.preventDefault();
    }, { passive: false });
};
const previewPages = [
    { path: '', label: 'Startseite' },
    { path: 'ueber-uns', label: 'Über uns' },
    { path: 'leistungen', label: 'Leistungen' },
    { path: 'kontakt', label: 'Kontakt' },
    { path: 'impressum', label: 'Impressum' },
    { path: 'datenschutz', label: 'Datenschutz' }
];
const bindCardPageNavigation = (card) => {
    const nav = card.querySelector('[data-preview-pages]');
    if (!nav) return;
    const label = nav.querySelector('[data-page-name]');
    let pageIndex = 0;
    const showPage = (nextIndex) => {
        pageIndex = (nextIndex + previewPages.length) % previewPages.length;
        const page = previewPages[pageIndex];
        const base = 'layouts/' + card.dataset.layoutId;
        const url = page.path ? base + '/' + page.path : base;
        label.textContent = page.label;
        card.dataset.previewUrl = url;
        previewScrollStates.delete(card);
        delete card.dataset.contentCenter;
        card.querySelectorAll('.iframe-device__viewport').forEach((viewport) => {
            viewport.dataset.previewSrc = url;
            const frame = viewport.querySelector('iframe');
            if (frame) {
                frame.dataset.src = url;
                armFrameLoad(frame);
                frame.src = url;
                preparePreviewFrame(frame);
            }
        });
        powerOnCard(card);
    };
    nav.addEventListener('pointerdown', (event) => event.stopPropagation());
    nav.querySelector('[data-page-prev]')?.addEventListener('click', (event) => { event.stopPropagation(); showPage(pageIndex - 1); });
    nav.querySelector('[data-page-next]')?.addEventListener('click', (event) => { event.stopPropagation(); showPage(pageIndex + 1); });
};
const bindCardRefresh = (card) => {
    const button = card.querySelector('[data-preview-refresh]');
    if (!button) return;
    button.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        hydrateCard(card);
        previewScrollStates.delete(card);
        delete card.dataset.contentCenter;
        const cleanUrl = card.dataset.previewUrl || ('layouts/' + card.dataset.layoutId);
        const refreshUrl = new URL(cleanUrl, location.href);
        refreshUrl.searchParams.set('previewReload', Date.now().toString(36));
        card.querySelectorAll('.iframe-preview iframe').forEach((frame) => {
            frame.dataset.src = cleanUrl;
            frame.addEventListener('load', () => fitCardFrames(card), { once: true });
            armFrameLoad(frame);
            frame.src = refreshUrl.href;
        });
        powerOnCard(card);
        button.classList.remove('is-refreshing');
        void button.offsetWidth;
        button.classList.add('is-refreshing');
        window.setTimeout(() => button.classList.remove('is-refreshing'), 720);
    });
};
document.querySelectorAll('.card').forEach((card) => {
    bindCardPreviewScroll(card);
    bindCardPageNavigation(card);
    bindCardRefresh(card);
});
window.addEventListener('resize', () => fitCardFrames(), { passive: true });
{
    const intro = document.querySelector('.intro-screen');
    const liteIntro = document.documentElement.classList.contains('lite-mode');
    let introDone = false;
    const dismissIntro = () => {
        if (introDone) return;
        introDone = true;
        intro?.classList.add('is-leaving');
        document.body.classList.remove('intro-active');
        document.dispatchEvent(new CustomEvent('railtime:overview-intro-dispose'));
        document.dispatchEvent(new CustomEvent('railtime:overview-ready'));
        window.setTimeout(() => intro?.remove(), 1400);
    };
    /* Lite-Mode (iOS/Touch): deutlich kürzeres Intro; Tippen überspringt sofort */
    window.setTimeout(dismissIntro, liteIntro ? 2600 : 5200);
    intro?.addEventListener('pointerdown', dismissIntro, { once: true });
}
const layoutRail = document.querySelector('[data-layout-rail]');
if (layoutRail && layoutRail.children.length > 1) {
    const track = document.createElement('div');
    track.className = 'grid__track';
    while (layoutRail.firstElementChild) track.append(layoutRail.firstElementChild);
    layoutRail.append(track);
    const sessionKey = 'railtime.layoutOverview.activeLayout';
    let rememberedLayout = '';
    try { rememberedLayout = sessionStorage.getItem(sessionKey) || ''; } catch (_) {}
    const rememberedCard = rememberedLayout ? track.querySelector('[data-layout-id="' + CSS.escape(rememberedLayout) + '"]') : null;
    if (rememberedCard) {
        while (track.firstElementChild !== rememberedCard) track.append(track.firstElementChild);
    }
    track.prepend(track.lastElementChild);

    const gap = 18;
    const card = () => track.firstElementChild;
    const step = () => card().offsetWidth + gap;
    const railInset = () => Math.max(0, (layoutRail.clientWidth - card().offsetWidth) / 2);
    let position = 0;
    let storedLayout = rememberedLayout;
    const rememberFocusedCard = (focusedCard) => {
        const id = focusedCard?.dataset.layoutId || '';
        if (!id || id === storedLayout) return;
        storedLayout = id;
        try { sessionStorage.setItem(sessionKey, id); } catch (_) {}
    };
    let activePreviewCard = null;
    let previewsReady = !isConstrainedPreviewMode();
    const updateFocus = () => {
        const railRect = layoutRail.getBoundingClientRect();
        const center = railRect.left + railRect.width / 2;
        let closest = null, distance = Infinity;
        const items = [...track.children];
        const centers = new Map();
        items.forEach((card) => {
            const rect = card.getBoundingClientRect();
            const cardCenter = rect.left + rect.width / 2;
            centers.set(card, cardCenter);
            const nextDistance = Math.abs(cardCenter - center);
            if (nextDistance < distance) { closest = card; distance = nextDistance; }
        });
        items.forEach((item) => {
            const itemCenter = centers.get(item) || center;
            item.classList.toggle('is-focused', item === closest);
            item.classList.toggle('is-before', item !== closest && itemCenter < center);
            item.classList.toggle('is-after', item !== closest && itemCenter > center);
        });
        rememberFocusedCard(closest);
        if (closest !== activePreviewCard) {
            items.forEach((item) => {
                if (item !== closest) unloadCard(item);
            });
            activePreviewCard = closest;
            if (previewsReady) hydrateCard(activePreviewCard);
        }
    };
    document.addEventListener('railtime:overview-ready', () => {
        previewsReady = true;
        hydrateCard(activePreviewCard);
    }, { once: true });
    document.addEventListener('railtime:preview-open', () => {
        if (isConstrainedPreviewMode()) unloadCard(activePreviewCard);
    });
    document.addEventListener('railtime:preview-close', () => {
        if (isConstrainedPreviewMode() && previewsReady) hydrateCard(activePreviewCard);
    });
    const renderPosition = () => {
        if (layoutRail.scrollLeft) layoutRail.scrollLeft = 0;
        track.style.transform = 'translate3d(' + position + 'px,0,0)';
        updateFocus();
    };
    const settleCentered = () => {
        position = -step();
        renderPosition();
    };
    const initialPosition = () => {
        track.style.paddingLeft = railInset() + 'px';
        track.style.paddingRight = railInset() + 'px';
        settleCentered();
    };
    requestAnimationFrame(initialPosition);
    window.addEventListener('resize', () => { initialPosition(); fitCardFrames(); }, { passive: true });

    let dragging = false, startX = 0, startPosition = 0, animating = false;
    const animateTo = (target, complete) => {
        if (animating) return;
        animating = true;
        const from = position;
        const distance = target - from;
        const duration = 680;
        const started = performance.now();
        const frame = (now) => {
            const progress = Math.min(1, (now - started) / duration);
            const eased = 1 - Math.pow(1 - progress, 4);
            position = from + distance * eased;
            renderPosition();
            if (progress < 1) {
                requestAnimationFrame(frame);
                return;
            }
            complete?.();
            animating = false;
            renderPosition();
        };
        requestAnimationFrame(frame);
    };
    const move = (direction) => {
        if (animating) return;
        const s = step();
        if (direction > 0) {
            animateTo(-s * 2, () => {
                track.append(track.firstElementChild);
                settleCentered();
            });
        } else {
            animateTo(0, () => {
                track.prepend(track.lastElementChild);
                settleCentered();
            });
        }
    };
    layoutRail.addEventListener('pointerdown', (event) => {
        if (animating || event.target.closest('a,button,[role="button"]')) return;
        dragging = true; startX = event.clientX; startPosition = position;
        layoutRail.classList.add('is-dragging'); layoutRail.setPointerCapture?.(event.pointerId);
    });
    layoutRail.addEventListener('pointermove', (event) => {
        if (!dragging) return;
        position = startPosition + event.clientX - startX;
        renderPosition();
    });
    ['pointerup','pointercancel','pointerleave'].forEach(type => layoutRail.addEventListener(type, () => {
        if (!dragging) return;
        dragging = false;
        layoutRail.classList.remove('is-dragging');
        const s = step();
        const delta = position - startPosition;
        if (delta < -s * .18) move(1);
        else if (delta > s * .18) move(-1);
        else animateTo(-s, settleCentered);
    }));
    document.querySelector('[data-rail-prev]')?.addEventListener('click', () => move(-1));
    document.querySelector('[data-rail-next]')?.addEventListener('click', () => move(1));
}

const preview = document.querySelector('[data-live-preview]');
if (preview) {
    const title = preview.querySelector('[data-preview-title]');
    const stage = preview.querySelector('[data-preview-stage]');
    const shell = preview.querySelector('[data-preview-shell]');
    const frame = preview.querySelector('[data-preview-frame]');
    preparePreviewFrame(frame);
    const devices = {
        desktop: { width: 1440, height: 900, chromeX: 24, chromeY: 46, extraBottom: 32, label: 'Desktop' },
        tablet: { width: 834, height: 1112, chromeX: 28, chromeY: 36, extraBottom: 0, label: 'Tablet' },
        mobile: { width: 390, height: 844, chromeX: 20, chromeY: 30, extraBottom: 0, label: 'Smartphone' }
    };
    let activeDevice = 'desktop';
    let previewOpener = null;
    const setActiveDevice = (device) => {
        activeDevice = isMobileOverview() ? 'desktop' : (devices[device] ? device : 'desktop');
        shell.dataset.mode = activeDevice;
        shell.setAttribute('aria-label', 'Interaktive ' + devices[activeDevice].label + '-Vorschau');
        preview.querySelectorAll('[data-preview-device]').forEach(button => {
            button.classList.toggle('is-active', button.dataset.previewDevice === activeDevice);
            button.setAttribute('aria-pressed', button.dataset.previewDevice === activeDevice ? 'true' : 'false');
        });
    };
    const fitPreview = () => {
        const spec = devices[activeDevice];
        const stageStyle = getComputedStyle(stage);
        const availableW = Math.max(220, stage.clientWidth - parseFloat(stageStyle.paddingLeft) - parseFloat(stageStyle.paddingRight));
        const availableH = Math.max(220, stage.clientHeight - parseFloat(stageStyle.paddingTop) - parseFloat(stageStyle.paddingBottom));
        const scale = Math.max(.18, Math.min(
            (availableW - spec.chromeX) / spec.width,
            (availableH - spec.chromeY - spec.extraBottom) / spec.height,
            .84
        ));
        shell.style.width = (spec.width * scale + spec.chromeX) + 'px';
        shell.style.height = (spec.height * scale + spec.chromeY) + 'px';
        frame.style.width = spec.width + 'px';
        frame.style.height = spec.height + 'px';
        frame.style.transform = 'scale(' + scale + ')';
    };
    const openPreview = (card, requestedDevice = 'desktop') => {
        if (!card) return;
        previewOpener = document.activeElement;
        title.textContent = card.dataset.layoutName || 'Layout Vorschau';
        frame.title = (card.dataset.layoutName || 'Layout') + ' · interaktive Vorschau';
        frame.src = card.dataset.previewUrl || ('layouts/' + card.dataset.layoutId);
        setActiveDevice(requestedDevice);
        if (!preview.open) preview.showModal();
        document.dispatchEvent(new CustomEvent('railtime:preview-open'));
        requestAnimationFrame(() => requestAnimationFrame(fitPreview));
    };
    const closePreview = () => {
        if (preview.open) preview.close();
    };
    document.addEventListener('click', (event) => {
        const deviceTrigger = event.target.closest('[data-preview-device-trigger]');
        const trigger = deviceTrigger || event.target.closest('[data-open-preview]');
        if (!trigger) return;
        const card = trigger.closest('[data-layout-id]');
        if (!card) return;
        event.preventDefault();
        event.stopPropagation();
        openPreview(card, deviceTrigger?.dataset.previewDeviceTrigger || 'desktop');
    });
    document.addEventListener('keydown', (event) => {
        const trigger = event.target.closest('[data-preview-device-trigger]');
        if (!trigger || !['Enter', ' '].includes(event.key)) return;
        event.preventDefault();
        openPreview(trigger.closest('[data-layout-id]'), trigger.dataset.previewDeviceTrigger);
    });
    preview.querySelectorAll('[data-preview-device]').forEach((button) => button.addEventListener('click', () => {
        setActiveDevice(button.dataset.previewDevice);
        requestAnimationFrame(fitPreview);
    }));
    preview.querySelector('[data-preview-close]')?.addEventListener('click', closePreview);
    preview.addEventListener('click', (event) => {
        if (event.target === preview || event.target === stage) closePreview();
    });
    preview.addEventListener('close', () => {
        frame.src = 'about:blank';
        document.dispatchEvent(new CustomEvent('railtime:preview-close'));
        previewOpener?.focus?.({ preventScroll: true });
        previewOpener = null;
    });
    frame.addEventListener('load', () => { if (preview.open) fitPreview(); });
    window.addEventListener('resize', () => {
        if (!preview.open) return;
        if (isMobileOverview()) setActiveDevice('desktop');
        fitPreview();
    }, { passive: true });
}
</script>
</body>
</html>
