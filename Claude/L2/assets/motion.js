/* Claude Layout L2 — "Editorial Press"
   Video: automatischer Ablauf ohne Scroll-Steuerung, normale
   Geschwindigkeit (1,0×), kein Loop, Endbild bleibt stehen,
   Ton per Klick (Regel 04/23). Bewegung: ScrollMagic, an realen
   Scrollfortschritt gebunden — dezente, ruhige Fades statt
   harter Panels (Regel 10/11/27). */

document.addEventListener('DOMContentLoaded', () => {
  const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---------- Startseite: Video, Logo-Intro, Auto-Scroll ---------- */
  const video = document.getElementById('ed-hero-video');
  if (video) {
    const logo = document.querySelector('.ed-hero__logo');
    const copy = document.querySelector('.ed-hero__copy');
    const target = document.getElementById('content-start');
    const soundBtn = document.getElementById('ed-sound');

    video.playbackRate = 1.0; // ohne Beschleunigung
    video.play().catch(() => {});

    // Ton per Klick (Browser blockieren Autoplay mit Ton)
    soundBtn?.addEventListener('click', () => {
      video.muted = !video.muted;
      if (!video.muted && video.paused && !video.ended) video.play().catch(() => {});
      soundBtn.setAttribute('aria-pressed', String(!video.muted));
      soundBtn.textContent = video.muted ? 'Ton an' : 'Ton aus';
    });

    // Logo-Intro zeitversetzt (Regel 06), Copy danach, Auto-Scroll (Regel 07)
    let revealed = false;
    const reveal = () => {
      if (revealed) return;
      revealed = true;
      logo?.classList.add('is-visible');
      setTimeout(() => copy?.classList.add('is-visible'), 800);
      setTimeout(() => {
        if (scrollY < innerHeight * 0.3) target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }, 3000);
    };
    setTimeout(() => logo?.classList.add('is-visible'), 1500);
    video.addEventListener('ended', reveal, { once: true });
    setTimeout(reveal, 9000); // Sicherheitsnetz bei blockiertem Autoplay
    if (reduced) reveal();
  }

  // Navigation: Startseite erst nach dem Scrollen einblenden (Regel 08)
  const nav = document.querySelector('.ed-nav');
  if (document.body.classList.contains('ed-home') && nav) {
    const syncNav = () => nav.classList.toggle('is-visible', scrollY > innerHeight * 0.3);
    addEventListener('scroll', syncNav, { passive: true });
    syncNav();
  }

  /* ---------- Akkordeon: exakt eine Leistung offen (Regel 16) ---------- */
  const items = [...document.querySelectorAll('[data-accordion-item]')];
  const open = item => {
    items.forEach(other => {
      const panel = other.querySelector('[data-panel]');
      const active = other === item;
      other.classList.toggle('is-open', active);
      other.querySelector('button')?.setAttribute('aria-expanded', String(active));
      if (panel) panel.style.maxHeight = active ? `${panel.scrollHeight}px` : '0px';
    });
  };
  items.forEach(item => item.querySelector('button')?.addEventListener('click', () => open(item)));
  addEventListener('resize', () => {
    const current = items.find(i => i.classList.contains('is-open'));
    const panel = current?.querySelector('[data-panel]');
    if (panel) panel.style.maxHeight = `${panel.scrollHeight}px`;
  });
  document.querySelectorAll('[data-service-tile]').forEach((tile, index) => {
    tile.addEventListener('click', event => {
      if (!items[index]) return;
      event.preventDefault();
      open(items[index]);
      items[index].scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });
  if (items[0]) {
    const anchored = location.hash && items.find(i => i.id === location.hash.slice(1));
    open(anchored || items[0]);
  }

  /* ---------- Ruhige Scroll-Reveals (Regel 10/11) ---------- */
  if (!window.ScrollMagic || reduced) {
    document.querySelectorAll('[data-ed-reveal]').forEach(el => {
      el.style.setProperty('--ed-o', '1'); el.style.setProperty('--ed-y', '0px');
    });
    return;
  }
  const controller = new ScrollMagic.Controller();
  document.querySelectorAll('[data-ed-reveal], .rt-germany-module').forEach(el => {
    el.style.setProperty('--ed-o', '0.15');
    el.style.setProperty('--ed-y', '24px');
    const nearEnd = document.documentElement.scrollHeight -
      (el.getBoundingClientRect().top + scrollY) < innerHeight * 1.25;
    new ScrollMagic.Scene({
      triggerElement: el,
      triggerHook: nearEnd ? 0.97 : 0.9,
      duration: nearEnd ? 120 : 260
    }).addTo(controller).on('progress', e => {
      const p = 1 - Math.pow(1 - e.progress, 2);
      el.style.setProperty('--ed-o', (0.15 + p * 0.85).toFixed(3));
      el.style.setProperty('--ed-y', `${((1 - p) * 24).toFixed(1)}px`);
    });
  });
});