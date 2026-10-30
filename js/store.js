(()=>{
'use strict';
const cacheKey='ms_store_bag_v1';
const drawer=document.querySelector('.cart-drawer');if(!drawer)return;
const backdrop=document.querySelector('.cart-backdrop');
const form=document.getElementById('checkout-form'),items=document.querySelector('[data-cart-items]'),total=document.querySelector('[data-cart-total]'),feedback=form.querySelector('.cart-feedback');
const format=n=>new Intl.NumberFormat('pt-BR',{style:'currency',currency:'BRL'}).format(n);
const escape=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const catalog=new Map([...document.querySelectorAll('.add-to-cart')].map(el=>[Number(el.dataset.id),{name:el.dataset.name,price:Number(el.dataset.price),stock:Number(el.dataset.stock)}]));
let cart={};
try{const x=JSON.parse(localStorage.getItem(cacheKey)||'{}');if(x&&typeof x==='object'&&!Array.isArray(x))Object.entries(x).forEach(([id,q])=>{const p=catalog.get(Number(id));if(p&&Number.isInteger(q)&&q>0)cart[id]=Math.min(q,p.stock,10);});}catch(e){}
let previousFocus=null;
const save=()=>{try{localStorage.setItem(cacheKey,JSON.stringify(cart));}catch(e){}};
const paint=()=>{
 let subtotal=0,n=0;
 const parts=Object.entries(cart).flatMap(([id,q])=>{const p=catalog.get(Number(id));if(!p||q<=0)return[];subtotal+=q*p.price;n+=q;
 return ['<div class="cart-item"><div><strong>'+escape(p.name)+'</strong><small>'+format(p.price)+' · estoque '+p.stock+'</small></div><div class="cart-qty"><button type="button" data-id="'+id+'" data-delta="-1" aria-label="Diminuir quantidade">−</button><span>'+q+'</span><button type="button" data-id="'+id+'" data-delta="1" aria-label="Aumentar quantidade">+</button></div></div>'];});
 items.innerHTML=parts.join('')||'<div class="cart-empty"><i class="fa-solid fa-bag-shopping"></i><p>Sua sacola está vazia.</p><small>Escolha uma peça no catálogo.</small></div>';
 total.textContent=format(subtotal);document.querySelectorAll('[data-cart-count]').forEach(el=>el.textContent=String(n));
 form.querySelector('button[type=submit]').disabled=!n;save();
};
const open=()=>{previousFocus=document.activeElement;drawer.classList.add('open');drawer.setAttribute('aria-hidden','false');backdrop.hidden=false;document.body.classList.add('cart-open');drawer.querySelector('.cart-close').focus();};
const close=()=>{drawer.classList.remove('open');drawer.setAttribute('aria-hidden','true');backdrop.hidden=true;document.body.classList.remove('cart-open');previousFocus?.focus?.();};
document.querySelectorAll('[data-open-cart]').forEach(b=>b.addEventListener('click',open));
document.querySelectorAll('[data-close-cart]').forEach(b=>b.addEventListener('click',close));
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&drawer.classList.contains('open'))close();});
document.querySelectorAll('.add-to-cart').forEach(b=>b.addEventListener('click',()=>{const id=Number(b.dataset.id),p=catalog.get(id);if(!p)return;cart[id]=Math.min(p.stock,10,(cart[id]||0)+1);feedback.textContent='';paint();open();}));
items.addEventListener('click',e=>{const b=e.target.closest('[data-id]');if(!b)return;const id=Number(b.dataset.id),p=catalog.get(id);if(!p)return;cart[id]=Math.min(10,p.stock,Math.max(0,(cart[id]||0)+Number(b.dataset.delta)));if(!cart[id])delete cart[id];paint();});
form.addEventListener('submit',async e=>{
e.preventDefault();const chosen=Object.entries(cart).map(([id,qtd])=>({id:Number(id),qtd}));if(!chosen.length)return;
const b=form.querySelector('button[type=submit]');b.disabled=true;feedback.textContent='Registrando seu pedido...';
try{const data=new FormData(form);data.append('itens',JSON.stringify(chosen));const response=await fetch('finalizar-pedido.php',{method:'POST',body:data,headers:{Accept:'application/json'}});const result=await response.json();
if(!response.ok||!result.ok)throw Error(result.message||'Não foi possível registrar o pedido.');
cart={};paint();feedback.textContent=result.message;
if(result.whatsapp&&result.whatsapp.startsWith('https://wa.me/'))location.assign(result.whatsapp);
}catch(err){feedback.textContent=err.message||'Verifique sua conexão.';paint();}finally{b.disabled=Object.keys(cart).length===0;}
});paint();
})();