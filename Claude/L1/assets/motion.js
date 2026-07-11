/* Claude Layout L1 — "Signal Works"
   Video: automatischer Ablauf (kein Scroll-Scrubbing), erhöhte
   Geschwindigkeit, optional mit Ton. Bewegungen: ScrollMagic,
   an den realen Scrollfortschritt gebunden (Regel 10/11). */

document.addEventListener('DOMContentLoaded', () => {
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Startseite: Video & Intro ---------- */
  const video = document.getElementById('cl-hero-video');
  if (video) {
    const logo = document.querySelector('.cl-hero__logo');
    const copy = document.querySelector('.cl-hero__copy');
    const nav = document.querySelector('.cl-nav');
    const target = document.getElementById('content-start');
    const soundBtn = document.getElementById('cl-sound');

    video.playbackRate = 1.5;
    video.play().catch(() => {});

    // Ton: Autoplay mit Ton wird von Browsern blockiert — per Klick aktivierbar
    soundBtn?.addEventListener('click', () => {
      video.muted = !video.muted;
      if (!video.muted && video.paused && !video.ended) video.play().catch(() => {});
      soundBtn.setAttribute('aria-pressed', String(!video.muted));
      soundBtn.textContent = video.muted ? 'Ton an' : 'Ton aus';
    });

    // Logo-Intro zeitversetzt über dem Video, danach Copy einblenden (Regel 06)
    let revealed = false;
    const reveal = () => {
      if (revealed) return;
      revealed = true;
      logo?.classList.add('is-visible');
      setTimeout(() => copy?.classList.add('is-visible'), 700);
      // Auto-Scroll zum ersten Inhaltssegment (Regel 07)
      setTimeout(() => { if (scrollY < innerHeight * .3) target?.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 3000);
    };
    setTimeout(() => logo?.classList.add('is-visible'), 1400);
    video.addEventListener('ended', reveal, { once: true });
    // Sicherheitsnetz, falls das Video nicht abspielen kann
    setTimeout(reveal, 6500);
    if (reduced) reveal();

    // Navigation erst nach dem Herunterscrollen einblenden (Regel 08)
    const syncNav = () => nav?.classList.toggle('is-visible', scrollY > innerHeight * .3);
    addEventListener('scroll', syncNav, { passive: true });
    syncNav();
  }

  /* ---------- Akkordeon: genau eine Leistung geöffnet (Regel 16) ---------- */
  const items = [...document.querySelectorAll('[data-accordion-item]')];
  const open = item => {
    items.forEach(other => {
      const panel = other.querySelector('[data-panel]');
      const button = other.querySelector('button');
      const active = other === item;
      other.classList.toggle('is-open', active);
      button?.setAttribute('aria-expanded', String(active));
      if (panel) panel.style.maxHeight = active ? `${panel.scrollHeight}px` : '0px';
    });
  };
  items.forEach(item => item.querySelector('button')?.addEventListener('click', () => open(item)));
  document.querySelectorAll('[data-service-tile]').forEach((tile, index) => {
    tile.addEventListener('click', event => {
      if (!items[index]) return;
      event.preventDefault();
      open(items[index]);
      items[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });
  if (items[0]) open(items[0]);

  /* ---------- Scroll-Reveals: an realen Scrollfortschritt gebunden ---------- */
  if (!window.ScrollMagic || reduced) return;
  const controller = new ScrollMagic.Controller();
  const elements = document.querySelectorAll('[data-cl-reveal], .rt-germany-module');
  elements.forEach(el => {
    // Elemente nahe dem Seitenende früher und kürzer auslösen (Regel 11)
    const nearEnd = document.documentElement.scrollHeight - (el.getBoundingClientRect().top + scrollY) < innerHeight * 1.25;
    new ScrollMagic.Scene({
      triggerElement: el,
      triggerHook: nearEnd ? .97 : .88,
      duration: nearEnd ? 150 : 300
    }).addTo(controller).on('progress', event => {
      const p = 1 - Math.pow(1 - event.progress, 3);
      el.style.setProperty('--cl-o', (.12 + p * .88).toFixed(3));
      el.style.setProperty('--cl-y', `${((1 - p) * 46).toFixed(1)}px`);
    });
  });
});
