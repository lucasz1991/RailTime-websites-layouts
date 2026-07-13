document.addEventListener('DOMContentLoaded', () => {
  const hero = document.querySelector('.rt-hero');
  const video = hero?.querySelector('[data-hero-video]');
  const logo = hero?.querySelector('.rt-intro-logo');
  const copy = hero?.querySelector('.rt-hero-copy');
  const nav = document.querySelector('.rt-nav');
  const target = document.querySelector('#content-start');
  if (!hero) return;

  const introClipStart = .30;
  const introSpeedMultiplier = 1.15;

  const restoreAnchor = () => {
    if (!location.hash) return;
    const anchor = document.querySelector(location.hash);
    anchor?.scrollIntoView({ block: 'start' });
  };

  if (video?.dataset.heroPlayback === 'intro-once') {
    let completed = false;
    let retryArmed = false;
    const blockedKeys = ['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', ' ', 'Home', 'End'];
    const startButton = hero.querySelector('[data-intro-start]');

    video.muted = true;
    video.defaultMuted = true;
    video.loop = false;
    video.autoplay = true;
    video.playsInline = true;
    video.preload = 'auto';
    video.removeAttribute('loop');

    scrollTo({ top: 0, left: 0, behavior: 'auto' });
    hero.classList.add('is-video-intro-playing');
    document.documentElement.classList.add('rt-intro-playing');
    document.body.classList.add('rt-intro-playing');

    const preventTraversal = event => {
      if (event.cancelable) event.preventDefault();
    };
    const preventTraversalKey = event => {
      if (blockedKeys.includes(event.key) && event.cancelable) event.preventDefault();
    };
    const lockPage = () => {
      addEventListener('wheel', preventTraversal, { passive: false });
      addEventListener('touchmove', preventTraversal, { passive: false });
      addEventListener('keydown', preventTraversalKey);
    };
    const unlockPage = () => {
      document.documentElement.classList.remove('rt-intro-playing');
      document.body.classList.remove('rt-intro-playing');
      removeEventListener('wheel', preventTraversal);
      removeEventListener('touchmove', preventTraversal);
      removeEventListener('keydown', preventTraversalKey);
    };
    lockPage();

    const revealAtVideoEnd = () => {
      if (completed) return;
      completed = true;
      hero.classList.remove('is-video-intro-playing', 'is-video-autoplay-blocked');
      hero.classList.add('is-video-intro-complete', 'is-video-logo-visible');
      logo?.classList.add('is-visible');
      nav?.classList.add('is-visible');
      unlockPage();
      dispatchEvent(new Event('resize'));
      if (location.hash) setTimeout(restoreAnchor, 1000);
    };

    const playIntroVideo = () => {
      const attempt = video.play();
      if (!attempt?.catch) return;
      attempt.then(() => {
        hero.classList.remove('is-video-autoplay-blocked');
      }).catch(() => {
        hero.classList.add('is-video-autoplay-blocked');
        if (retryArmed) return;
        retryArmed = true;
        addEventListener('pointerdown', playIntroVideo, { once: true, passive: true });
        addEventListener('keydown', playIntroVideo, { once: true });
      });
    };

    startButton?.addEventListener('click', playIntroVideo);

    video.addEventListener('ended', revealAtVideoEnd, { once: true });
    video.addEventListener('error', revealAtVideoEnd, { once: true });

    if (video.ended) revealAtVideoEnd();
    else if (video.readyState >= 2) playIntroVideo();
    else video.addEventListener('canplay', playIntroVideo, { once: true });

    const resumeIntroVideo = () => {
      if (!completed && !document.hidden && video.paused) playIntroVideo();
    };
    document.addEventListener('visibilitychange', resumeIntroVideo);
    addEventListener('pagehide', () => {
      document.removeEventListener('visibilitychange', resumeIntroVideo);
      startButton?.removeEventListener('click', playIntroVideo);
      unlockPage();
    }, { once: true });
    return;
  }

  const usesInertialScrollVideo = ['theme-1', 'theme-3', 'theme-5'].some(theme => document.body.classList.contains(theme));

  if (usesInertialScrollVideo && video && window.RailTimeScrollVideo?.createScrollScrub) {
    let touchY = 0;
    let finished = false;
    let finishTimer = 0;

    hero.classList.add('is-video-ready', 'is-scroll-scrub');
    hero.style.setProperty('--rt-video-progress', '0');
    video.removeAttribute('autoplay');
    video.removeAttribute('loop');
    video.muted = true;
    video.playsInline = true;
    video.preload = 'auto';
    video.pause();
    video.load();
    scrollTo({ top: 0, left: 0, behavior: 'auto' });
    document.documentElement.classList.add('rt-intro-playing');
    document.body.classList.add('rt-intro-playing');

    const renderProgress = progress => {
      hero.style.setProperty('--rt-video-progress', progress.toFixed(4));
      hero.classList.toggle('is-video-scroll-engaged', progress > .04);
      hero.classList.toggle('is-video-logo-visible', progress >= .62);
      logo?.classList.toggle('is-visible', progress >= .62);
      hero.classList.toggle('is-video-near-end', progress >= .86);
    };

    const removeInput = () => {
      removeEventListener('wheel', onWheel);
      removeEventListener('touchstart', onTouchStart);
      removeEventListener('touchmove', onTouchMove);
      removeEventListener('keydown', onKeyDown);
      removeEventListener('railtime:preview-scroll', onPreviewScroll);
      removeEventListener('railtime:preview-sync', onPreviewSync);
    };

    const unlock = () => {
      document.body.classList.remove('rt-intro-playing');
      document.documentElement.classList.remove('rt-intro-playing');
      hero.classList.remove('is-video-finishing');
      hero.classList.add('is-video-complete');
      nav?.classList.add('is-visible');
      dispatchEvent(new Event('resize'));
    };

    const finish = () => {
      if (finished) return;
      finished = true;
      removeInput();
      hero.style.setProperty('--rt-video-progress', '1');
      hero.classList.remove('is-video-ready');
      hero.classList.add('is-video-scroll-engaged', 'is-video-logo-visible', 'is-video-finishing');
      logo?.classList.add('is-visible');
      copy?.classList.remove('is-visible', 'is-interactive');
      finishTimer = window.setTimeout(unlock, 1160);
    };

    const scrubber = window.RailTimeScrollVideo.createScrollScrub(video, {
      scrollDistance: 4200,
      minPlaybackRate: .65,
      maxPlaybackRate: 1,
      mediaStartProgress: introClipStart,
      speedMultiplier: introSpeedMultiplier,
      maxLeadSeconds: .55,
      idleGraceMs: 140,
      coastSeconds: .22,
      rateSmoothingMs: 120,
      eventCap: 600,
      onProgress: renderProgress,
      onComplete: finish
    });

    function onWheel(event) {
      if (finished) return;
      if (event.cancelable) event.preventDefault();
      const modeFactor = event.deltaMode === 1 ? 16 : event.deltaMode === 2 ? innerHeight : 1;
      scrubber.addDelta(event.deltaY * modeFactor);
    }
    function onTouchStart(event) { touchY = event.touches[0]?.clientY || 0; }
    function onTouchMove(event) {
      if (finished) return;
      const nextY = event.touches[0]?.clientY || touchY;
      if (event.cancelable) event.preventDefault();
      scrubber.addDelta((touchY - nextY) * 2.1);
      touchY = nextY;
    }
    function onKeyDown(event) {
      if (finished) return;
      const deltas = { ArrowDown: 120, PageDown: 520, ' ': 420, ArrowUp: -120, PageUp: -520 };
      if (event.key === 'End' || event.key === 'Home') {
        if (event.cancelable) event.preventDefault();
        scrubber.setProgress(event.key === 'End' ? 1 : 0);
        return;
      }
      if (!(event.key in deltas)) return;
      if (event.cancelable) event.preventDefault();
      scrubber.addDelta(deltas[event.key]);
    }
    function onPreviewScroll(event) {
      if (finished) return;
      const delta = Number(event.detail?.deltaY || 0);
      if (Number.isFinite(delta) && delta) scrubber.addDelta(delta);
    }
    function onPreviewSync() {
      if (finished) return;
      scrubber.jumpTo(0);
      renderProgress(0);
    }

    addEventListener('wheel', onWheel, { passive: false });
    addEventListener('touchstart', onTouchStart, { passive: true });
    addEventListener('touchmove', onTouchMove, { passive: false });
    addEventListener('keydown', onKeyDown);
    addEventListener('railtime:preview-scroll', onPreviewScroll);
    addEventListener('railtime:preview-sync', onPreviewSync);

    if (location.hash || matchMedia('(prefers-reduced-motion: reduce)').matches) {
      finished = true;
      removeInput();
      scrubber.jumpTo(1);
      hero.style.setProperty('--rt-video-progress', '1');
      hero.classList.remove('is-video-ready', 'is-video-finishing');
      hero.classList.add('is-video-scroll-engaged', 'is-video-complete', 'is-video-logo-visible');
      logo?.classList.add('is-visible');
      copy?.classList.remove('is-visible', 'is-interactive');
      nav?.classList.add('is-visible');
      document.body.classList.remove('rt-intro-playing');
      document.documentElement.classList.remove('rt-intro-playing');
      dispatchEvent(new Event('resize'));
      if (location.hash) setTimeout(restoreAnchor, 80);
    }

    addEventListener('pagehide', event => {
      if (event.persisted) return;
      clearTimeout(finishTimer);
      removeInput();
      scrubber.destroy();
    });
    return;
  }

  if (usesInertialScrollVideo && video && window.RailTimeScrollVideo?.createOneShot) {
    let started = false;
    let attempting = false;
    let finished = false;
    let collapseTimer = 0;
    let touchStartY = 0;

    hero.classList.add('is-video-ready');
    hero.style.setProperty('--rt-video-progress', '0');
    video.removeAttribute('autoplay');
    video.removeAttribute('loop');
    video.muted = true;
    video.playsInline = true;
    video.preload = 'metadata';
    video.pause();
    video.load();

    const blockedKeys = ['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', ' ', 'Home', 'End'];
    const preventPageTraversal = event => {
      if (event.cancelable) event.preventDefault();
    };
    const preventTraversalKey = event => {
      if (blockedKeys.includes(event.key) && event.cancelable) event.preventDefault();
    };
    const lockPage = () => {
      document.documentElement.classList.add('rt-intro-playing');
      document.body.classList.add('rt-intro-playing');
      addEventListener('wheel', preventPageTraversal, { passive: false });
      addEventListener('touchmove', preventPageTraversal, { passive: false });
      addEventListener('keydown', preventTraversalKey);
    };
    const unlockPage = () => {
      document.body.classList.remove('rt-intro-playing');
      document.documentElement.classList.remove('rt-intro-playing');
      removeEventListener('wheel', preventPageTraversal);
      removeEventListener('touchmove', preventPageTraversal);
      removeEventListener('keydown', preventTraversalKey);
    };

    const finishIntro = (immediate = false) => {
      if (finished) return;
      finished = true;
      attempting = false;
      hero.style.setProperty('--rt-video-progress', '1');
      hero.classList.add('is-video-logo-visible');
      logo?.classList.add('is-visible');
      copy?.classList.remove('is-visible', 'is-interactive');
      nav?.classList.add('is-visible');

      if (immediate) {
        hero.classList.remove('is-video-playing', 'is-video-finishing');
        hero.classList.add('is-video-complete');
        unlockPage();
        dispatchEvent(new Event('resize'));
        return;
      }

      hero.classList.remove('is-video-playing');
      hero.classList.add('is-video-finishing');
      collapseTimer = window.setTimeout(() => {
        hero.classList.remove('is-video-finishing');
        hero.classList.add('is-video-complete');
        unlockPage();
        scrollTo({ top: 0, left: 0, behavior: 'auto' });
        dispatchEvent(new Event('resize'));
      }, 1150);
    };

    const player = window.RailTimeScrollVideo.createOneShot(video, {
      playbackRate: introSpeedMultiplier,
      mediaStartProgress: introClipStart,
      onProgress: progress => {
        hero.style.setProperty('--rt-video-progress', progress.toFixed(4));
        if (progress >= .58) {
          hero.classList.add('is-video-logo-visible');
          logo?.classList.add('is-visible');
        }
        if (progress >= .82) hero.classList.add('is-video-near-end');
      },
      onComplete: () => finishIntro(false)
    });

    const removeStartListeners = () => {
      removeEventListener('wheel', onWheel);
      removeEventListener('touchstart', onTouchStart);
      removeEventListener('touchmove', onTouchMove);
      removeEventListener('keydown', onKeyDown);
      removeEventListener('scroll', onScroll);
    };

    const startIntro = async () => {
      if (started || attempting || finished) return;
      if (matchMedia('(prefers-reduced-motion: reduce)').matches) {
        removeStartListeners();
        finishIntro(true);
        return;
      }
      attempting = true;
      scrollTo({ top: 0, left: 0, behavior: 'auto' });
      lockPage();
      hero.classList.remove('is-video-ready');
      hero.classList.add('is-video-playing');
      const playing = await player.start();
      if (!playing) {
        attempting = false;
        hero.classList.remove('is-video-playing');
        hero.classList.add('is-video-ready');
        unlockPage();
        return;
      }
      started = true;
      attempting = false;
      removeStartListeners();
    };

    const onWheel = event => {
      if (event.deltaY > 8) startIntro();
    };
    const onTouchStart = event => {
      touchStartY = event.touches[0]?.clientY || 0;
    };
    const onTouchMove = event => {
      const currentY = event.touches[0]?.clientY || touchStartY;
      if (touchStartY - currentY > 10) startIntro();
    };
    const onKeyDown = event => {
      if (['ArrowDown', 'PageDown', ' ', 'End'].includes(event.key)) startIntro();
    };
    const onScroll = () => {
      if (scrollY > 12) startIntro();
    };

    addEventListener('wheel', onWheel, { passive: true });
    addEventListener('touchstart', onTouchStart, { passive: true });
    addEventListener('touchmove', onTouchMove, { passive: true });
    addEventListener('keydown', onKeyDown);
    addEventListener('scroll', onScroll, { passive: true });

    if (location.hash) {
      removeStartListeners();
      finishIntro(true);
      setTimeout(restoreAnchor, 80);
    }

    addEventListener('pagehide', () => {
      clearTimeout(collapseTimer);
      player.destroy();
    }, { once: true });
    return;
  }

  let revealed = false;
  const reveal = () => {
    if (revealed) return;
    revealed = true;
    logo?.classList.add('is-visible');
    setTimeout(() => {
      logo?.classList.add('is-docked');
      copy?.classList.add('is-visible', 'is-interactive');
    }, 850);
    setTimeout(() => target?.scrollIntoView({ behavior: 'smooth', block: 'start' }), 3000);
  };
  video?.addEventListener('ended', reveal, { once: true });
  setTimeout(reveal, 7200);
  const sync = () => nav?.classList.toggle('is-visible', scrollY > innerHeight * .28);
  addEventListener('scroll', sync, { passive: true });
  sync();
  setTimeout(restoreAnchor, 320);
});
