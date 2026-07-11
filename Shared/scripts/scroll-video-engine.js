(function (global) {
  'use strict';

  const clamp = value => Math.min(1, Math.max(0, value));

  function createOneShot(video, options) {
    const settings = Object.assign({
      playbackRate: 1.15,
      fallbackMs: 9000,
      onProgress: null,
      onStateChange: null,
      onComplete: null
    }, options || {});

    let state = 'idle';
    let frameRequest = 0;
    let animationFrame = 0;
    let fallbackTimer = 0;
    let destroyed = false;
    let completionSent = false;

    video.muted = true;
    video.loop = false;
    video.playsInline = true;
    video.preload = 'metadata';
    video.dataset.scrollVideoEngine = 'shared-one-shot-v3';
    video.dataset.videoState = state;

    const setState = nextState => {
      if (state === nextState) return;
      state = nextState;
      video.dataset.videoState = state;
      settings.onStateChange?.(state);
    };

    const cancelMonitor = () => {
      if (frameRequest && video.cancelVideoFrameCallback) video.cancelVideoFrameCallback(frameRequest);
      if (animationFrame) cancelAnimationFrame(animationFrame);
      frameRequest = 0;
      animationFrame = 0;
    };

    const reportProgress = mediaTime => {
      const duration = Number.isFinite(video.duration) ? video.duration : 0;
      const current = Number.isFinite(mediaTime) ? mediaTime : video.currentTime;
      const progress = duration > 0 ? clamp(current / duration) : 0;
      video.dataset.videoProgress = progress.toFixed(4);
      settings.onProgress?.(progress);
      return progress;
    };

    const monitor = (_now, metadata) => {
      frameRequest = 0;
      animationFrame = 0;
      if (destroyed || state !== 'playing') return;
      reportProgress(metadata?.mediaTime);
      scheduleMonitor();
    };

    const scheduleMonitor = () => {
      if (destroyed || state !== 'playing' || frameRequest || animationFrame) return;
      if (video.requestVideoFrameCallback) frameRequest = video.requestVideoFrameCallback(monitor);
      else animationFrame = requestAnimationFrame(monitor);
    };

    const complete = reason => {
      if (destroyed || completionSent) return;
      completionSent = true;
      clearTimeout(fallbackTimer);
      cancelMonitor();
      video.pause();
      reportProgress(video.duration || video.currentTime);
      video.dataset.videoCompletion = reason || 'ended';
      setState('complete');
      settings.onComplete?.(reason || 'ended');
    };

    const armFallback = () => {
      clearTimeout(fallbackTimer);
      const duration = Number.isFinite(video.duration) && video.duration > 0
        ? Math.ceil(((video.duration - video.currentTime) / settings.playbackRate) * 1000 + 1400)
        : settings.fallbackMs;
      fallbackTimer = global.setTimeout(() => {
        const mediaDuration = Number.isFinite(video.duration) ? video.duration : 0;
        const trulyFinished = video.ended || (mediaDuration > 0 && video.currentTime >= mediaDuration - .12);
        if (trulyFinished) {
          complete('fallback-end');
          return;
        }
        video.play().catch(() => {});
        armFallback();
      }, Math.max(2500, duration));
    };

    const start = async () => {
      if (destroyed || state !== 'idle') return state === 'playing';
      video.preload = 'auto';
      video.playbackRate = settings.playbackRate;
      video.defaultPlaybackRate = settings.playbackRate;
      setState('playing');
      armFallback();
      scheduleMonitor();
      try {
        await video.play();
        return true;
      } catch (_) {
        clearTimeout(fallbackTimer);
        cancelMonitor();
        setState('idle');
        return false;
      }
    };

    const destroy = () => {
      destroyed = true;
      clearTimeout(fallbackTimer);
      cancelMonitor();
      video.removeEventListener('ended', onEnded);
      video.removeEventListener('error', onError);
    };

    const onEnded = () => complete('ended');
    const onError = () => complete('media-error');
    video.addEventListener('ended', onEnded);
    video.addEventListener('error', onError);
    video.addEventListener('loadedmetadata', () => {
      video.dataset.videoDuration = Number(video.duration || 0).toFixed(3);
      if (state === 'playing') armFallback();
    });

    reportProgress(0);
    return {
      start,
      complete,
      destroy,
      get state() { return state; }
    };
  }

  function createScrollScrub(video, options) {
    const settings = Object.assign({
      maxMediaRate: 1,
      minMediaRate: .3,
      coastMs: 420,
      acceleration: 7.5,
      braking: 5.2,
      inputDecay: 3.6,
      seekInterval: 26,
      onProgress: null,
      onComplete: null
    }, options || {});

    let duration = 0;
    let progress = 0;
    let renderedProgress = 0;
    let velocity = 0;
    let driveVelocity = 0;
    let direction = 0;
    let lastInputAt = -Infinity;
    let lastFrameAt = 0;
    let automaticTarget = null;
    let frame = 0;
    let destroyed = false;
    let completed = false;
    let completionPending = false;
    let completionTimer = 0;
    let lastSeekAt = 0;

    video.muted = true;
    video.loop = false;
    video.playsInline = true;
    video.preload = 'auto';
    video.autoplay = false;
    video.pause();
    video.dataset.scrollVideoEngine = 'shared-inertial-scrub-v2';
    video.dataset.videoState = 'scrubbing';

    const syncDuration = () => {
      if (Number.isFinite(video.duration) && video.duration > 0) {
        duration = video.duration;
        video.dataset.videoDuration = duration.toFixed(3);
      }
    };

    const report = () => {
      const mediaRate = velocity * (duration || 0);
      video.dataset.videoProgress = renderedProgress.toFixed(4);
      video.dataset.videoPlaybackRate = mediaRate.toFixed(3);
      settings.onProgress?.(renderedProgress, progress, mediaRate);
    };

    const completeAtRenderedEnd = () => {
      if (completionPending || completed || destroyed) return;
      completionPending = true;
      velocity = 0;
      driveVelocity = 0;
      automaticTarget = null;
      progress = 1;
      renderedProgress = 1;
      const endTime = Math.max(0, duration - .035);
      video.currentTime = endTime;
      report();

      let frameCallback = 0;
      const done = () => {
        if (completed || destroyed) return;
        completed = true;
        completionPending = false;
        clearTimeout(completionTimer);
        if (frameCallback && video.cancelVideoFrameCallback) video.cancelVideoFrameCallback(frameCallback);
        video.pause();
        video.dataset.videoProgress = '1.0000';
        video.dataset.videoPlaybackRate = '0.000';
        video.dataset.videoState = 'complete';
        settings.onProgress?.(1, 1, 0);
        settings.onComplete?.();
      };

      if (video.requestVideoFrameCallback) frameCallback = video.requestVideoFrameCallback(done);
      else video.addEventListener('seeked', done, { once: true });
      completionTimer = global.setTimeout(done, 360);
    };

    const draw = now => {
      frame = 0;
      if (destroyed || !duration) return;
      if (!lastFrameAt) lastFrameAt = now;
      const dt = Math.min(.05, Math.max(.001, (now - lastFrameAt) / 1000));
      lastFrameAt = now;
      const maxProgressRate = settings.maxMediaRate / duration;
      const minProgressRate = settings.minMediaRate / duration;

      if (automaticTarget !== null) {
        direction = Math.sign(automaticTarget - progress);
        driveVelocity = direction * maxProgressRate;
      } else {
        const inputAge = now - lastInputAt;
        driveVelocity *= Math.exp(-settings.inputDecay * dt);
        if (inputAge < settings.coastMs && direction && Math.abs(driveVelocity) < minProgressRate) {
          driveVelocity = direction * minProgressRate;
        }
      }

      driveVelocity = Math.max(-maxProgressRate, Math.min(maxProgressRate, driveVelocity));
      const accelerating = Math.abs(driveVelocity) > Math.abs(velocity) || Math.sign(driveVelocity) !== Math.sign(velocity);
      const response = 1 - Math.exp(-(accelerating ? settings.acceleration : settings.braking) * dt);
      velocity += (driveVelocity - velocity) * response;
      velocity = Math.max(-maxProgressRate, Math.min(maxProgressRate, velocity));

      if (automaticTarget === null && now - lastInputAt >= settings.coastMs && Math.abs(driveVelocity) < .00015 && Math.abs(velocity) < .00015) {
        driveVelocity = 0;
        velocity = 0;
      }

      progress = clamp(progress + velocity * dt);
      renderedProgress = progress;
      if (automaticTarget !== null && ((direction > 0 && progress >= automaticTarget) || (direction < 0 && progress <= automaticTarget))) {
        progress = automaticTarget;
        renderedProgress = automaticTarget;
        automaticTarget = null;
        driveVelocity = 0;
        if (progress < 1) velocity = 0;
      }
      if (progress <= 0 && velocity < 0) {
        progress = renderedProgress = 0;
        velocity = driveVelocity = 0;
      }

      const nextTime = Math.min(Math.max(0, duration - .035), duration * renderedProgress);
      if (!video.seeking && now - lastSeekAt >= settings.seekInterval && Math.abs(video.currentTime - nextTime) > .01) {
        lastSeekAt = now;
        video.currentTime = nextTime;
      }
      report();

      if (renderedProgress >= .9995 && direction >= 0) {
        completeAtRenderedEnd();
        return;
      }
      const moving = automaticTarget !== null || Math.abs(velocity) > .00015 || Math.abs(driveVelocity) > .00015 || now - lastInputAt < settings.coastMs;
      if (moving) frame = requestAnimationFrame(draw);
    };

    const requestDraw = () => {
      if (!frame && !destroyed) frame = requestAnimationFrame(draw);
    };

    const addDelta = pixels => {
      if (destroyed || completed || !Number.isFinite(pixels)) return progress;
      const limited = Math.max(-180, Math.min(180, pixels));
      if (!limited) return progress;
      const nextDirection = Math.sign(limited);
      const mediaDuration = duration || 6;
      const maxProgressRate = settings.maxMediaRate / mediaDuration;
      const minProgressRate = settings.minMediaRate / mediaDuration;
      const strength = Math.min(1, Math.abs(limited) / 120);
      const requestedRate = minProgressRate + (maxProgressRate - minProgressRate) * strength;
      if (direction && nextDirection !== direction) {
        driveVelocity *= .25;
        velocity *= .7;
      }
      direction = nextDirection;
      automaticTarget = null;
      driveVelocity = Math.max(-maxProgressRate, Math.min(maxProgressRate, driveVelocity + direction * requestedRate * .72));
      lastInputAt = global.performance?.now?.() ?? Date.now();
      requestDraw();
      return progress;
    };

    const setProgress = value => {
      if (destroyed || completed) return progress;
      automaticTarget = clamp(value);
      direction = Math.sign(automaticTarget - progress);
      lastInputAt = global.performance?.now?.() ?? Date.now();
      requestDraw();
      return progress;
    };

    const jumpTo = value => {
      if (destroyed) return progress;
      progress = renderedProgress = clamp(value);
      automaticTarget = null;
      velocity = driveVelocity = 0;
      direction = progress >= 1 ? 1 : progress <= 0 ? -1 : direction;
      if (duration) video.currentTime = Math.min(Math.max(0, duration - .035), duration * progress);
      report();
      return progress;
    };

    const onMetadata = () => { syncDuration(); requestDraw(); };

    const destroy = () => {
      destroyed = true;
      if (frame) cancelAnimationFrame(frame);
      clearTimeout(completionTimer);
      video.removeEventListener('loadedmetadata', onMetadata);
    };

    video.addEventListener('loadedmetadata', onMetadata);
    syncDuration();
    requestDraw();

    return {
      addDelta,
      setProgress,
      jumpTo,
      destroy,
      get progress() { return progress; },
      get renderedProgress() { return renderedProgress; },
      get velocity() { return velocity * (duration || 0); },
      get complete() { return completed; }
    };
  }

  function createInertialScrollScrub(video, options) {
    const settings = Object.assign({
      scrollDistance: 4200,
      minPlaybackRate: .65,
      maxPlaybackRate: 1,
      maxLeadSeconds: .55,
      idleGraceMs: 140,
      coastSeconds: .22,
      rateSmoothingMs: 120,
      eventCap: 600,
      metadataTimeoutMs: 8000,
      onProgress: null,
      onComplete: null
    }, options || {});

    let duration = 0;
    let intentProgress = 0;
    let targetProgress = 0;
    let mediaRate = 0;
    let direction = 0;
    let lastInputAt = -Infinity;
    let lastFrameAt = 0;
    let frame = 0;
    let monitorVideoFrame = 0;
    let playPending = false;
    let playGeneration = 0;
    let nativeBlocked = false;
    let completionArmed = false;
    let completionPending = false;
    let completed = false;
    let destroyed = false;
    let coastApplied = false;
    let finalFrameCallback = 0;
    let finalTimer = 0;
    let idleTimer = 0;
    let targetTimer = 0;
    let watchdogTimer = 0;
    let pendingJump = null;

    video.muted = true;
    video.loop = false;
    video.autoplay = false;
    video.playsInline = true;
    video.preload = 'auto';
    video.pause();
    video.dataset.scrollVideoEngine = 'shared-inertial-native-v3';
    video.dataset.videoState = 'scrubbing';
    video.dataset.videoPlaybackRate = '0.000';

    const nowTime = () => global.performance?.now?.() ?? Date.now();
    const playhead = () => duration > 0 ? clamp(video.currentTime / duration) : 0;
    const report = () => {
      const progress = playhead();
      video.dataset.videoProgress = progress.toFixed(4);
      video.dataset.videoPlaybackRate = (video.paused ? 0 : video.playbackRate).toFixed(3);
      video.dataset.videoIntent = intentProgress.toFixed(4);
      video.dataset.videoTarget = targetProgress.toFixed(4);
      video.dataset.videoInternalRate = mediaRate.toFixed(3);
      settings.onProgress?.(progress, intentProgress, video.paused ? 0 : video.playbackRate);
    };
    const requestDraw = () => {
      if (frame || monitorVideoFrame || destroyed || completed) return;
      if (!video.paused && video.requestVideoFrameCallback) {
        monitorVideoFrame = video.requestVideoFrameCallback(now => { monitorVideoFrame = 0; draw(now); });
      } else {
        frame = requestAnimationFrame(draw);
      }
    };
    const ensurePlaying = rate => {
      video.playbackRate = Math.max(settings.minPlaybackRate, Math.min(settings.maxPlaybackRate, rate));
      if (!video.paused || playPending || nativeBlocked) return;
      const generation = ++playGeneration;
      playPending = true;
      video.play().then(() => {
        if (generation !== playGeneration || destroyed) return;
        playPending = false;
        requestDraw();
      }).catch(error => {
        if (generation !== playGeneration || destroyed) return;
        playPending = false;
        if (error?.name !== 'AbortError') nativeBlocked = true;
        if (nativeBlocked) video.pause();
        requestDraw();
      });
    };
    const stopNative = () => {
      clearTimeout(targetTimer);
      targetTimer = 0;
      if (monitorVideoFrame && video.cancelVideoFrameCallback) {
        video.cancelVideoFrameCallback(monitorVideoFrame);
        monitorVideoFrame = 0;
      }
      if (!video.paused) video.pause();
      playGeneration += 1;
      playPending = false;
      video.dataset.videoPlaybackRate = '0.000';
    };

    const settleAtTarget = () => {
      targetTimer = 0;
      if (destroyed || completed || completionArmed || direction <= 0 || !duration) return;
      const current = playhead();
      const remainingSeconds = Math.max(0, (targetProgress - current) * duration);
      if (remainingSeconds > .018) {
        scheduleTargetStop();
        return;
      }
      const targetTime = Math.max(0, Math.min(duration - .001, targetProgress * duration));
      stopNative();
      mediaRate = 0;
      if (Math.abs(video.currentTime - targetTime) > .025) video.currentTime = targetTime;
      intentProgress = targetProgress = playhead();
      report();
    };

    const scheduleTargetStop = () => {
      if (targetTimer || destroyed || completed || completionArmed || direction <= 0 || !duration) return;
      const remainingSeconds = Math.max(0, (targetProgress - playhead()) * duration);
      if (remainingSeconds <= .018) {
        settleAtTarget();
        return;
      }
      // Check at the earliest possible arrival. This prevents background-tab or
      // throttled video-frame callbacks from letting native playback overshoot.
      const delay = Math.max(24, (remainingSeconds / settings.maxPlaybackRate) * 1000 - 8);
      targetTimer = global.setTimeout(settleAtTarget, delay);
    };

    const applyCoast = () => {
      if (destroyed || completed || completionArmed || coastApplied || !direction || !duration) return;
      const current = playhead();
      targetProgress = clamp(current + direction * settings.coastSeconds / duration);
      intentProgress = targetProgress;
      coastApplied = true;
      clearTimeout(targetTimer);
      targetTimer = 0;
      scheduleTargetStop();
      requestDraw();
    };

    const armIdleCoast = () => {
      clearTimeout(idleTimer);
      idleTimer = global.setTimeout(applyCoast, settings.idleGraceMs);
    };

    const complete = reason => {
      if (completed || destroyed) return;
      completed = true;
      completionPending = false;
      clearTimeout(finalTimer);
      clearTimeout(idleTimer);
      clearInterval(watchdogTimer);
      if (finalFrameCallback && video.cancelVideoFrameCallback) video.cancelVideoFrameCallback(finalFrameCallback);
      stopNative();
      mediaRate = 0;
      intentProgress = targetProgress = 1;
      video.dataset.videoProgress = '1.0000';
      video.dataset.videoInternalRate = '0.000';
      video.dataset.videoState = 'complete';
      video.dataset.videoCompletion = reason;
      settings.onProgress?.(1, 1, 0);
      settings.onComplete?.(reason);
    };

    // Native playback normally raises `ended` with currentTime === duration.
    // The 2 ms fallback only covers engines that clamp immediately before it;
    // it is deliberately much smaller than a rendered video frame.
    const isAtActualEnd = () => video.ended || (duration > 0 && video.currentTime >= duration - .002);

    const confirmFinalFrame = reason => {
      if (completionPending || completed || destroyed) return;
      completionPending = true;
      const done = () => {
        finalFrameCallback = 0;
        clearTimeout(finalTimer);
        if (!isAtActualEnd()) {
          completionPending = false;
          ensurePlaying(settings.maxPlaybackRate);
          requestDraw();
          return;
        }
        complete(reason);
      };
      if (isAtActualEnd()) {
        done();
        return;
      }
      if (video.requestVideoFrameCallback) finalFrameCallback = video.requestVideoFrameCallback(done);
      else video.addEventListener('timeupdate', done, { once: true });
      finalTimer = global.setTimeout(done, 420);
    };

    const draw = now => {
      frame = 0;
      if (destroyed || completed || !duration) return;
      if (!lastFrameAt) lastFrameAt = now;
      const dt = Math.min(.05, Math.max(.001, (now - lastFrameAt) / 1000));
      lastFrameAt = now;
      let current = playhead();
      const halfFrame = Math.max(.0015, (1 / 30) / duration);

      if (!completionArmed && !coastApplied && now - lastInputAt >= settings.idleGraceMs && direction) applyCoast();

      if (completionArmed) targetProgress = 1;
      const gap = targetProgress - current;
      let desiredRate = 0;
      if (completionArmed) desiredRate = settings.maxPlaybackRate;
      else if (Math.abs(gap) > halfFrame) {
        const magnitude = Math.min(1, Math.abs(gap) * duration / settings.maxLeadSeconds);
        desiredRate = Math.sign(gap) * (settings.minPlaybackRate + (settings.maxPlaybackRate - settings.minPlaybackRate) * magnitude);
      }

      const response = 1 - Math.exp(-dt / Math.max(.02, settings.rateSmoothingMs / 1000));
      mediaRate += (desiredRate - mediaRate) * response;
      mediaRate = Math.max(-settings.maxPlaybackRate, Math.min(settings.maxPlaybackRate, mediaRate));

      if (completionArmed || mediaRate > .04) {
        if (nativeBlocked) {
          stopNative();
          video.currentTime = Math.min(duration - .001, video.currentTime + Math.max(0, mediaRate) * dt);
        } else {
          ensurePlaying(Math.max(settings.minPlaybackRate, mediaRate));
          scheduleTargetStop();
        }
      } else if (mediaRate < -.04) {
        stopNative();
        video.currentTime = Math.max(0, video.currentTime + mediaRate * dt);
      } else {
        stopNative();
        mediaRate = 0;
      }

      current = playhead();
      if (!completionArmed && ((direction >= 0 && current >= targetProgress - halfFrame) || (direction < 0 && current <= targetProgress + halfFrame))) {
        stopNative();
        mediaRate = 0;
        targetProgress = intentProgress = current;
      }

      report();
      if (completionArmed && isAtActualEnd()) {
        confirmFinalFrame(video.ended ? 'ended' : 'final-frame');
        return;
      }

      const active = completionArmed || Math.abs(mediaRate) > .001 || Math.abs(targetProgress - current) > halfFrame || now - lastInputAt < settings.idleGraceMs;
      if (active) requestDraw();
    };

    const addDelta = pixels => {
      if (destroyed || completed || completionArmed || !Number.isFinite(pixels) || !pixels) return intentProgress;
      const limited = Math.max(-settings.eventCap, Math.min(settings.eventCap, pixels));
      const nextDirection = Math.sign(limited);
      const current = playhead();
      if (nextDirection !== direction) {
        stopNative();
        mediaRate = 0;
        intentProgress = current;
      } else {
        intentProgress = nextDirection > 0 ? Math.max(intentProgress, current) : Math.min(intentProgress, current);
      }
      direction = nextDirection;
      intentProgress = clamp(intentProgress + limited / settings.scrollDistance);
      if (direction > 0 && intentProgress >= .997) {
        intentProgress = targetProgress = 1;
        completionArmed = true;
      } else {
        const maxLead = settings.maxLeadSeconds / Math.max(duration || 6, .1);
        targetProgress = clamp(intentProgress);
        targetProgress = direction > 0 ? Math.min(targetProgress, current + maxLead) : Math.max(targetProgress, current - maxLead);
      }
      lastInputAt = nowTime();
      coastApplied = false;
      clearTimeout(targetTimer);
      targetTimer = 0;
      armIdleCoast();
      requestDraw();
      return intentProgress;
    };

    const setProgress = value => {
      if (destroyed || completed) return intentProgress;
      const next = clamp(value);
      direction = Math.sign(next - playhead()) || direction || 1;
      intentProgress = targetProgress = next;
      completionArmed = next >= 1;
      lastInputAt = nowTime();
      coastApplied = completionArmed;
      clearTimeout(targetTimer);
      targetTimer = 0;
      if (!completionArmed) armIdleCoast();
      else clearTimeout(idleTimer);
      requestDraw();
      return intentProgress;
    };

    const jumpTo = value => {
      if (destroyed) return intentProgress;
      const next = clamp(value);
      clearTimeout(idleTimer);
      clearTimeout(targetTimer);
      idleTimer = targetTimer = 0;
      stopNative();
      intentProgress = targetProgress = next;
      pendingJump = duration ? null : next;
      if (duration) video.currentTime = next >= 1 ? Math.max(0, duration - .001) : duration * next;
      video.dataset.videoProgress = next.toFixed(4);
      video.dataset.videoState = next >= 1 ? 'complete' : 'scrubbing';
      return next;
    };

    const onMetadata = () => {
      duration = Number.isFinite(video.duration) ? video.duration : 0;
      video.dataset.videoDuration = duration.toFixed(3);
      if (duration && pendingJump !== null) {
        video.currentTime = pendingJump >= 1 ? Math.max(0, duration - .001) : duration * pendingJump;
        pendingJump = null;
      }
      requestDraw();
    };
    const onEnded = () => confirmFinalFrame('ended');
    const onError = () => complete('media-error');
    const metadataTimer = global.setTimeout(() => { if (!duration) complete('metadata-timeout'); }, settings.metadataTimeoutMs);
    video.addEventListener('loadedmetadata', onMetadata);
    video.addEventListener('ended', onEnded);
    video.addEventListener('error', onError);
    onMetadata();
    watchdogTimer = global.setInterval(() => {
      if (destroyed || completed || !duration) return;
      report();
      if (completionArmed && isAtActualEnd()) {
        confirmFinalFrame(video.ended ? 'ended' : 'final-frame');
        return;
      }
      if (!completionArmed && direction > 0 && !video.paused) {
        const tolerance = Math.max(.0015, (1 / 30) / duration);
        if (playhead() >= targetProgress - tolerance) settleAtTarget();
      }
    }, 60);

    const destroy = () => {
      destroyed = true;
      if (frame) cancelAnimationFrame(frame);
      if (monitorVideoFrame && video.cancelVideoFrameCallback) video.cancelVideoFrameCallback(monitorVideoFrame);
      clearTimeout(metadataTimer);
      clearTimeout(finalTimer);
      clearTimeout(idleTimer);
      clearTimeout(targetTimer);
      clearInterval(watchdogTimer);
      stopNative();
      video.removeEventListener('loadedmetadata', onMetadata);
      video.removeEventListener('ended', onEnded);
      video.removeEventListener('error', onError);
    };

    return {
      addDelta, setProgress, jumpTo, destroy,
      get progress() { return intentProgress; },
      get renderedProgress() { return playhead(); },
      get velocity() { return video.paused ? 0 : video.playbackRate; },
      get complete() { return completed; }
    };
  }

  global.RailTimeScrollVideo = { createOneShot, createScrollScrub: createInertialScrollScrub };
})(window);
