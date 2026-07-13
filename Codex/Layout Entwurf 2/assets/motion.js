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
  RailTimeMotion.reveal('.sc-motion-left',{axis:'x',distance:-64,duration:360,hook:.87,alternate:false});
  RailTimeMotion.reveal('.sc-motion-right',{axis:'x',distance:64,duration:360,hook:.87,alternate:false});
});
