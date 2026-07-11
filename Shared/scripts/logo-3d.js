import * as THREE from 'https://esm.sh/three@0.161.0';
import { GLTFLoader } from 'https://esm.sh/three@0.161.0/examples/jsm/loaders/GLTFLoader.js';

const hosts = [...document.querySelectorAll('[data-rt-logo-3d]')];
const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

hosts.forEach((host) => {
  const canvas = host.querySelector('canvas');
  if (!canvas) return;

  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({
      canvas,
      antialias: true,
      alpha: true,
      powerPreference: 'high-performance'
    });
  } catch (error) {
    host.classList.add('is-fallback');
    console.warn('Das 3D-Logo konnte nicht initialisiert werden.', error);
    return;
  }

  renderer.setPixelRatio(Math.min(devicePixelRatio || 1, 1.8));
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.18;
  renderer.setClearColor(0x000000, 0);

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(34, 1, .01, 100);
  camera.position.set(.08, .035, 2.35);
  scene.add(new THREE.HemisphereLight(0xf7f9ff, 0x15090d, 1.72));

  const key = new THREE.DirectionalLight(0xffffff, 3.1);
  key.position.set(-1.7, 2.4, 3.5);
  scene.add(key);

  const rim = new THREE.DirectionalLight(0xe4002b, 2.25);
  rim.position.set(2.8, -.5, 1.5);
  scene.add(rim);

  const fill = new THREE.DirectionalLight(0x9aa7b8, 1.05);
  fill.position.set(-2.2, -1.4, 1.4);
  scene.add(fill);

  let logo = null;
  let frame = 0;
  let inView = true;
  let pointerX = 0;
  let pointerY = 0;
  let rotationX = -.025;
  let rotationY = 0;
  const startedAt = performance.now();

  const resize = () => {
    const rect = host.getBoundingClientRect();
    const width = Math.max(1, Math.round(rect.width));
    const height = Math.max(1, Math.round(rect.height));
    camera.aspect = width / height;
    camera.updateProjectionMatrix();
    renderer.setSize(width, height, false);
    if (reducedMotion && logo) renderer.render(scene, camera);
  };

  const stop = () => {
    if (!frame) return;
    cancelAnimationFrame(frame);
    frame = 0;
  };

  const tick = (now) => {
    frame = 0;
    if (!logo || !inView || document.hidden) return;
    const elapsed = (now - startedAt) / 1000;
    const targetY = Math.sin(elapsed * .58) * .19 + pointerX * .24;
    const targetX = -.025 + pointerY * .08;
    rotationY += (targetY - rotationY) * .055;
    rotationX += (targetX - rotationX) * .07;
    logo.rotation.set(rotationX, rotationY, 0);
    logo.position.y = Math.sin(elapsed * .9) * .012;
    renderer.render(scene, camera);
    frame = requestAnimationFrame(tick);
  };

  const start = () => {
    if (!logo || frame || !inView || document.hidden) return;
    if (reducedMotion) {
      logo.rotation.set(-.025, .08, 0);
      renderer.render(scene, camera);
      return;
    }
    frame = requestAnimationFrame(tick);
  };

  host.addEventListener('pointermove', (event) => {
    const rect = host.getBoundingClientRect();
    pointerX = ((event.clientX - rect.left) / rect.width - .5) * 2;
    pointerY = ((event.clientY - rect.top) / rect.height - .5) * 2;
    start();
  }, { passive: true });
  host.addEventListener('pointerleave', () => {
    pointerX = 0;
    pointerY = 0;
  }, { passive: true });

  const resizeObserver = new ResizeObserver(resize);
  resizeObserver.observe(host);
  resize();

  const visibilityObserver = new IntersectionObserver((entries) => {
    inView = entries[0]?.isIntersecting ?? true;
    if (inView) start();
    else stop();
  }, { rootMargin: '120px' });
  visibilityObserver.observe(host);

  const onVisibilityChange = () => {
    if (document.hidden) stop();
    else start();
  };
  document.addEventListener('visibilitychange', onVisibilityChange);

  const modelUrl = host.dataset.modelSrc || '/RailTime/Codex/logo/d1/rt-logo.glb';
  new GLTFLoader().load(modelUrl, ({ scene: model }) => {
    logo = model.getObjectByName('RT_Logo') ?? model;
    scene.add(model);
    host.classList.add('is-ready');
    resize();
    start();
  }, undefined, (error) => {
    host.classList.add('is-fallback');
    console.warn('Das 3D-Logo konnte nicht geladen werden.', error);
  });

  addEventListener('pagehide', () => {
    stop();
    resizeObserver.disconnect();
    visibilityObserver.disconnect();
    document.removeEventListener('visibilitychange', onVisibilityChange);
    renderer.dispose();
  }, { once: true });
});
