(() => {
  'use strict';

  const mobileQuery = window.matchMedia('(max-width: 1120px)');
  let instanceIndex = 0;

  const focusableSelector = [
    'a[href]',
    'button:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    'textarea:not([disabled])',
    '[tabindex]:not([tabindex="-1"])'
  ].join(',');

  const getPhoneLink = header => {
    const labelled = header.querySelector('.rt-nav__phone');
    return labelled || [...header.querySelectorAll('a[href^="tel:"]')].find(link => !link.closest('nav')) || null;
  };

  const getFocusable = element => [...element.querySelectorAll(focusableSelector)]
    .filter(item => !item.hasAttribute('disabled') && item.getClientRects().length);

  const initNavigation = header => {
    if (header.dataset.mobileNavigationReady === 'true') return;

    const desktopLinks = header.querySelector('nav');
    if (!desktopLinks) return;

    header.dataset.mobileNavigationReady = 'true';
    instanceIndex += 1;

    let toggle = header.querySelector('.rt-nav__toggle');
    if (!toggle) {
      toggle = document.createElement('button');
      toggle.className = 'rt-nav__toggle';
      toggle.type = 'button';
      header.append(toggle);
    }

    const drawerId = `rt-mobile-drawer-${instanceIndex}`;
    toggle.type = 'button';
    toggle.classList.add('rt-nav__toggle');
    toggle.setAttribute('aria-label', 'Menü öffnen');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-controls', drawerId);
    toggle.innerHTML = '<span></span><span></span><span></span>';
    header.append(toggle);

    const scrim = document.createElement('button');
    scrim.className = 'rt-mobile-menu__scrim';
    scrim.type = 'button';
    scrim.tabIndex = -1;
    scrim.setAttribute('aria-label', 'Menü schließen');

    const drawer = document.createElement('aside');
    drawer.id = drawerId;
    drawer.className = 'rt-mobile-drawer';
    drawer.setAttribute('aria-label', 'Mobile Navigation');
    drawer.setAttribute('aria-hidden', 'true');
    drawer.inert = true;
    drawer.innerHTML = [
      '<div class="rt-mobile-drawer__header">',
      '<span class="rt-mobile-drawer__eyebrow">Rail Time</span>',
      '<button class="rt-mobile-drawer__close" type="button" aria-label="Menü schließen"><span></span><span></span></button>',
      '</div>',
      '<div class="rt-mobile-drawer__signal" aria-hidden="true"><i></i></div>',
      '<div class="rt-mobile-drawer__body"></div>'
    ].join('');

    const drawerBody = drawer.querySelector('.rt-mobile-drawer__body');
    const navigation = desktopLinks.cloneNode(true);
    navigation.className = 'rt-mobile-drawer__links';
    navigation.setAttribute('aria-label', 'Hauptnavigation');
    drawerBody.append(navigation);

    const phone = getPhoneLink(header);
    if (phone) {
      const hotline = phone.cloneNode(true);
      hotline.classList.add('rt-mobile-drawer__hotline');
      if (!hotline.querySelector('span, strong')) {
        const rawPhone = hotline.textContent.trim();
        const phoneNumber = rawPhone.replace(/^(?:notfall(?:dienst)?\s*)?24\s*\/\s*7\s*[·|–-]?\s*/i, '') || rawPhone;
        hotline.textContent = '';
        const label = document.createElement('span');
        label.textContent = 'Notfall 24/7';
        const number = document.createElement('strong');
        number.textContent = phoneNumber;
        hotline.append(label, number);
      }
      drawerBody.append(hotline);
    }

    const footer = document.createElement('p');
    footer.className = 'rt-mobile-drawer__footer';
    footer.textContent = 'Sicher. Flexibel. Bundesweit im Einsatz.';
    drawerBody.append(footer);

    document.body.append(scrim, drawer);

    const closeButton = drawer.querySelector('.rt-mobile-drawer__close');
    let lastFocused = null;
    let isOpen = false;

    const close = ({ restoreFocus = true } = {}) => {
      if (!isOpen) return;
      isOpen = false;
      document.body.classList.remove('rt-mobile-menu-open');
      header.classList.remove('is-mobile-menu-open');
      toggle.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-label', 'Menü öffnen');
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      drawer.inert = true;
      scrim.classList.remove('is-open');
      if (restoreFocus && lastFocused instanceof HTMLElement) lastFocused.focus({ preventScroll: true });
    };

    const open = () => {
      if (isOpen || !mobileQuery.matches) return;
      isOpen = true;
      lastFocused = document.activeElement instanceof HTMLElement ? document.activeElement : toggle;
      document.body.classList.add('rt-mobile-menu-open');
      header.classList.add('is-mobile-menu-open');
      toggle.classList.add('is-open');
      toggle.setAttribute('aria-expanded', 'true');
      toggle.setAttribute('aria-label', 'Menü schließen');
      drawer.inert = false;
      drawer.setAttribute('aria-hidden', 'false');
      scrim.classList.add('is-open');
      requestAnimationFrame(() => {
        drawer.classList.add('is-open');
        closeButton?.focus({ preventScroll: true });
      });
    };

    toggle.addEventListener('click', () => (isOpen ? close() : open()));
    closeButton?.addEventListener('click', () => close());
    scrim.addEventListener('click', () => close());
    navigation.addEventListener('click', event => {
      if (event.target.closest('a')) close({ restoreFocus: false });
    });

    document.addEventListener('keydown', event => {
      if (!isOpen) return;
      if (event.key === 'Escape') {
        event.preventDefault();
        close();
        return;
      }
      if (event.key !== 'Tab') return;

      const focusable = getFocusable(drawer);
      if (!focusable.length) return;
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });

    const closeAtDesktop = event => {
      if (!event.matches) close({ restoreFocus: false });
    };
    mobileQuery.addEventListener?.('change', closeAtDesktop);
  };

  const start = () => document.querySelectorAll('.rt-nav').forEach(initNavigation);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });
  else start();
})();
