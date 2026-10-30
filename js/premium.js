(() => {
'use strict';
const reduced=matchMedia('(prefers-reduced-motion: reduce)').matches;
const navbar=document.querySelector('.ms-nav'),progress=document.querySelector('.scroll-progress');
const scroll=()=>{navbar?.classList.toggle('nav-scrolled',scrollY>12);if(progress){const total=document.documentElement.scrollHeight-innerHeight;progress.style.transform='scaleX('+(total>0?scrollY/total:0)+')';}};
addEventListener('scroll',scroll,{passive:true});scroll();
const menu=document.querySelector('.nav-mobile-toggle'),nav=document.querySelector('.ms-links');
menu?.addEventListener('click',()=>{const open=nav?.classList.toggle('open');menu.setAttribute('aria-expanded',String(open));menu.setAttribute('aria-label',open?'Fechar menu':'Abrir menu');});
nav?.querySelectorAll('a').forEach(a=>a.addEventListener('click',()=>{nav.classList.remove('open');menu?.setAttribute('aria-expanded','false');}));
const reveal=[...document.querySelectorAll('.reveal')];
if(reduced||!('IntersectionObserver' in window))reveal.forEach(el=>el.classList.add('in-view'));
else{const io=new IntersectionObserver(rows=>rows.forEach(({isIntersecting,target})=>{if(isIntersecting){target.classList.add('in-view');io.unobserve(target);}}),{rootMargin:'0px 0px 50px 0px',threshold:.07});reveal.forEach(el=>io.observe(el));}
if(!reduced&&matchMedia('(pointer:fine)').matches){
document.querySelectorAll('.tilt-card').forEach(el=>{
let frame=0;
el.addEventListener('pointermove',e=>{
 if(frame)cancelAnimationFrame(frame);
 const b=el.getBoundingClientRect(),x=(e.clientX-b.left)/b.width-.5,y=(e.clientY-b.top)/b.height-.5;
 frame=requestAnimationFrame(()=>{el.style.setProperty('--tilt-x',(-y*8).toFixed(2)+'deg');el.style.setProperty('--tilt-y',(x*10).toFixed(2)+'deg');el.style.setProperty('--mouse-x',((x+.5)*100).toFixed(1)+'%');el.style.setProperty('--mouse-y',((y+.5)*100).toFixed(1)+'%');});
 },{passive:true});
 el.addEventListener('pointerleave',()=>{el.style.setProperty('--tilt-x','0deg');el.style.setProperty('--tilt-y','0deg');});
});
}
const els=document.querySelectorAll('[data-count]');
if(els.length&&'IntersectionObserver' in window){
const obs=new IntersectionObserver(rows=>rows.forEach(({isIntersecting,target})=>{
if(!isIntersecting)return;obs.unobserve(target);
const goal=Number(target.dataset.count)||0;if(reduced){target.textContent=String(goal);return;}
const start=performance.now();const animate=t=>{const ratio=Math.min(1,(t-start)/700);target.textContent=String(Math.round(goal*(1-(1-ratio)**3)));if(ratio<1)requestAnimationFrame(animate);};requestAnimationFrame(animate);
}),{threshold:.3});els.forEach(el=>obs.observe(el));
}
})();