(() => {
'use strict';
document.querySelectorAll('[data-toggle-password]').forEach(button => {
 const field=document.getElementById(button.dataset.togglePassword);
 if(!field)return;
 button.addEventListener('click',() => {
  const showing=field.type==='password';
  field.type=showing?'text':'password';
  button.setAttribute('aria-pressed',String(showing));
  button.setAttribute('aria-label',showing?'Ocultar senha':'Mostrar senha');
  const icon=button.querySelector('i');
  if(icon)icon.className=showing?'fa-regular fa-eye-slash':'fa-regular fa-eye';
  field.focus({preventScroll:true});
 });
});
document.querySelectorAll('.studio-form').forEach(form=>{
 const submit=form.querySelector('button[type="submit"]');
 form.addEventListener('submit',()=>{
  if(!form.checkValidity()||!submit)return;
  submit.setAttribute('aria-busy','true');
  submit.querySelector('span')?.replaceChildren(document.createTextNode('Verificando acesso...'));
  // Avoid double submissions while auth endpoint responds.
  submit.disabled=true;
 });
});
})();