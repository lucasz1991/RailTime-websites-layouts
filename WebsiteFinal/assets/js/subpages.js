document.addEventListener('DOMContentLoaded', () => {
  const items = [...document.querySelectorAll('[data-accordion-item]')];
  const closeTimers = new WeakMap();

  const setPanelState = (item, active, immediate = false) => {
    const panel = item.querySelector('[data-panel]');
    const button = item.querySelector('button[aria-controls]');
    const oldTimer = panel ? closeTimers.get(panel) : 0;
    if (oldTimer) clearTimeout(oldTimer);

    item.classList.toggle('is-open', active);
    button?.setAttribute('aria-expanded', String(active));
    if (!panel) return;

    if (active) {
      panel.hidden = false;
      requestAnimationFrame(() => { panel.style.maxHeight = `${panel.scrollHeight}px`; });
      return;
    }

    panel.style.maxHeight = '0px';
    if (immediate) {
      panel.hidden = true;
      return;
    }
    const timer = window.setTimeout(() => {
      if (!item.classList.contains('is-open')) panel.hidden = true;
      closeTimers.delete(panel);
    }, 560);
    closeTimers.set(panel, timer);
  };

  const open = (item, options = {}) => {
    if (!item) return;
    items.forEach(other => setPanelState(other, other === item, Boolean(options.immediate)));
    if (options.updateHash) history.replaceState(null, '', `#${item.id}`);
  };

  items.forEach(item => {
    item.querySelector('button[aria-controls]')?.addEventListener('click', () => {
      const isOpen = item.classList.contains('is-open');
      if (isOpen) setPanelState(item, false);
      else open(item, { updateHash: true });
    });
  });

  document.querySelectorAll('[data-service-tile]').forEach((tile, index) => {
    tile.addEventListener('click', event => {
      event.preventDefault();
      const item = items[index];
      open(item, { updateHash: true });
      item?.scrollIntoView({ behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth', block: 'center' });
    });
  });

  const hashItem = location.hash ? items.find(item => `#${item.id}` === location.hash) : null;
  if (hashItem) open(hashItem, { immediate: true });
  else if (items[0]) open(items[0], { immediate: true });

  addEventListener('hashchange', () => {
    const item = items.find(candidate => `#${candidate.id}` === location.hash);
    if (item) open(item, { immediate: true });
  });

  addEventListener('resize', () => {
    const openPanel = document.querySelector('[data-accordion-item].is-open [data-panel]');
    if (openPanel) openPanel.style.maxHeight = `${openPanel.scrollHeight}px`;
  }, { passive: true });

  if (!window.ScrollMagic || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const controller = new ScrollMagic.Controller();
  const animated = [
    ...document.querySelectorAll('main > section'),
    ...document.querySelectorAll('[data-service-tile]'),
    ...items,
    ...document.querySelectorAll('form, .rt-emergency')
  ];

  [...new Set(animated)].forEach((element, index) => {
    element.classList.add('rt-scroll-bound');
    element.parentElement?.classList.add('rt-motion-clip');
    const fromTop = index % 2 === 0;
    const distance = element.matches('[data-service-tile]') ? 54 : 72;
    const nearBottom = element.getBoundingClientRect().top + scrollY > document.documentElement.scrollHeight - innerHeight * 1.15;
    const draw = rawProgress => {
      const progress = Math.min(1, Math.max(0, rawProgress));
      const eased = 1 - Math.pow(1 - progress, 3);
      const offset = (1 - eased) * distance * (fromTop ? 1 : -1);
      element.style.setProperty('--rt-scroll-y', `${offset}px`);
      element.style.setProperty('--rt-scroll-opacity', String(0.18 + eased * 0.82));
      element.style.setProperty('--rt-scroll-scale', String(0.985 + eased * 0.015));
    };
    draw(0);

    const scene = new ScrollMagic.Scene({
      triggerElement: element,
      triggerHook: nearBottom ? 0.96 : 0.84,
      duration: nearBottom ? '28%' : '46%'
    }).addTo(controller);

    scene.on('progress', event => draw(event.progress));
    requestAnimationFrame(() => scene.update(true));
  });
});
