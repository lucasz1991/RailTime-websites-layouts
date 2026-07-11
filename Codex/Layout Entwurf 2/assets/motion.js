document.addEventListener('DOMContentLoaded',()=>{
  const nav=document.querySelector('.rt-nav');
  const toggle=document.querySelector('.rt-nav__toggle');
  toggle?.addEventListener('click',()=>{
    const open=nav.classList.toggle('is-menu-open');
    toggle.setAttribute('aria-expanded',String(open));
  });
  nav?.querySelectorAll('nav a').forEach(link=>link.addEventListener('click',()=>nav.classList.remove('is-menu-open')));

  if(!document.body.classList.contains('is-home')||!window.RailTimeMotion)return;
  RailTimeMotion.reveal('.sc-motion-rise',{distance:62,duration:360,hook:.87,alternate:false});
  RailTimeMotion.reveal('.sc-motion-left',{axis:'x',distance:64,duration:360,hook:.87,alternate:false});
  document.querySelectorAll('.sc-motion-right').forEach(el=>{
    if(!window.ScrollMagic||matchMedia('(prefers-reduced-motion: reduce)').matches)return;
    const near=document.documentElement.scrollHeight-(el.offsetTop+el.offsetHeight)<innerHeight*1.15;
    const controller=new ScrollMagic.Controller();
    new ScrollMagic.Scene({triggerElement:el,triggerHook:near?.97:.87,duration:near?170:360}).on('progress',event=>{
      const p=1-Math.pow(1-event.progress,3);
      el.style.opacity=String(.12+p*.88);
      el.style.transform=`translate3d(${(1-p)*64}px,0,0)`;
    }).addTo(controller);
  });
});
