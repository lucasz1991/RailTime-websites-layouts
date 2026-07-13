window.RailTimeMotion = (() => {
  const root = document.documentElement;
  const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const canAnimate = Boolean(window.ScrollMagic) && !reducedMotion;

  const releaseCloak = (disableMotion = false) => {
    clearTimeout(window.__rtMotionCloakTimer);
    root.classList.remove('rt-motion-pending');
    if (disableMotion) root.classList.remove('rt-motion-enabled');
  };

  if (!canAnimate) {
    releaseCloak(true);
    return { reveal: () => {}, clip: () => {}, ready: () => releaseCloak(true) };
  }

  const controller = new ScrollMagic.Controller();
  const clamp = (number) => Math.max(0, Math.min(1, number));
  const nearEnd = (element) => (
    document.documentElement.scrollHeight
    - (element.getBoundingClientRect().top + scrollY)
    < innerHeight * 1.3
  );

  const clip = (element) => {
    element?.parentElement?.classList.add('rt-motion-clip');
  };

  const reveal = (
    selector,
    {
      axis = 'y',
      distance = 42,
      duration = 300,
      hook = .84,
      alternate = true
    } = {}
  ) => {
    document.querySelectorAll(selector).forEach((element, index) => {
      const early = nearEnd(element);
      const sign = alternate && index % 2 ? -1 : 1;
      const draw = (rawProgress) => {
        const progress = 1 - Math.pow(1 - clamp(rawProgress), 3);
        const x = axis === 'x' ? (1 - progress) * distance * sign : 0;
        const y = axis === 'y' ? (1 - progress) * distance * sign : 0;
        element.style.opacity = (.12 + progress * .88).toFixed(3);
        element.style.transform = 'translate3d(' + x + 'px,' + y + 'px,0)';
      };

      clip(element);
      draw(0);

      const scene = new ScrollMagic.Scene({
        triggerElement: element,
        triggerHook: early ? .97 : hook,
        duration: early ? Math.min(duration, 170) : duration
      }).on('progress', (event) => draw(event.progress)).addTo(controller);

      requestAnimationFrame(() => scene.update(true));
    });
  };

  const ready = () => requestAnimationFrame(() => releaseCloak(false));
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ready, { once: true });
  } else {
    ready();
  }
  addEventListener('load', () => controller.update(true), { once: true });

  return { reveal, clip, ready };
})();
