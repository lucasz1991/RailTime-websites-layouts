document.addEventListener('DOMContentLoaded',()=>{
  const nav=document.querySelector('.rt-nav');
  const toggle=document.querySelector('.rt-nav__toggle');
  toggle?.addEventListener('click',()=>{const open=nav.classList.toggle('is-menu-open');toggle.setAttribute('aria-expanded',String(open))});
  nav?.querySelectorAll('nav a').forEach(a=>a.addEventListener('click',()=>nav.classList.remove('is-menu-open')));
  if(!document.body.classList.contains('is-home')||!window.RailTimeMotion)return;
  if(document.querySelector('.sa-main')){
    RailTimeMotion.reveal('.sa-motion-up',{distance:34,duration:260,hook:.76,alternate:false});
    RailTimeMotion.reveal('.sa-motion-left',{axis:'x',distance:-42,duration:280,hook:.76,alternate:false});
    RailTimeMotion.reveal('.sa-motion-right',{axis:'x',distance:42,duration:280,hook:.76,alternate:false});
    return;
  }
  RailTimeMotion.reveal('.ig-motion-up',{distance:58,duration:350,hook:.86,alternate:false});
  RailTimeMotion.reveal('.ig-motion-left',{axis:'x',distance:-62,duration:360,hook:.86,alternate:false});
  RailTimeMotion.reveal('.ig-motion-right',{axis:'x',distance:62,duration:360,hook:.86,alternate:false});
});
