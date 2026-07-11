document.addEventListener('DOMContentLoaded', () => {
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
      event.preventDefault();
      open(items[index]);
      items[index]?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  });
  if (items[0]) open(items[0]);

  if (!window.ScrollMagic || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const controller = new ScrollMagic.Controller();
  const animated = [
    ...document.querySelectorAll('main > section'),
    ...document.querySelectorAll('[data-service-tile]'),
    ...items,
    ...document.querySelectorAll('form, .rt-emergency')
  ];

  animated.forEach((element, index) => {
    element.classList.add('rt-scroll-bound');
    const fromTop = index % 2 === 0;
    const distance = element.matches('[data-service-tile]') ? 54 : 72;
    const nearBottom = element.getBoundingClientRect().top + scrollY > document.documentElement.scrollHeight - innerHeight * 1.15;
    const scene = new ScrollMagic.Scene({
      triggerElement: element,
      triggerHook: nearBottom ? 0.96 : 0.84,
      duration: nearBottom ? '28%' : '46%'
    }).addTo(controller);

    scene.on('progress', event => {
      const progress = Math.min(1, Math.max(0, event.progress));
      const eased = 1 - Math.pow(1 - progress, 3);
      const offset = (1 - eased) * distance * (fromTop ? 1 : -1);
      element.style.setProperty('--rt-scroll-y', `${offset}px`);
      element.style.setProperty('--rt-scroll-opacity', String(0.18 + eased * 0.82));
      element.style.setProperty('--rt-scroll-scale', String(0.985 + eased * 0.015));
    });
  });
});
