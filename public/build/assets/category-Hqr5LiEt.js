var e=class{static async fetch(e,t={}){try{let n=await fetch(e,t),r=await n.text(),i;try{i=JSON.parse(r)}catch{i={success:!1,message:r,data:null}}return!n.ok||i.success===!1?{success:!1,message:i.message||`Erreur HTTP ${n.status}`,data:null}:{success:i.success??!0,message:i.message??null,data:i.data??i}}catch(t){return console.error(`[ApiHelper] ${e} →`,t),{success:!1,message:`Erreur réseau ou serveur.`,data:null}}}static async post(e,t={},n={}){return await this.fetch(e,{method:`POST`,headers:{"Content-Type":`application/json`,...n},body:JSON.stringify(t)})}static async get(e,t={}){return await this.fetch(e,{headers:t})}};async function t(){let e=await(await fetch(`admin/category/getCategoriesJson`)).json(),t=document.querySelector(`#category-table tbody`);t.innerHTML=``,e.forEach(e=>{let r=document.createElement(`tr`);r.innerHTML=`
                <td>${e.id}</td>
                <td><input type="text" value="${e.name}" readonly></td>
                <td><input type="text" value="${e.slug}" readonly></td>
                <td><input type="text" value="${e.description}" readonly></td>
                <td><input type="text" value="${e.parent_id}" readonly></td>
                <td>
                    <button class="btn-edit">Modifier</button>
                    <button class="btn-cancelOrDelete">Supprimer</button>
                </td>
            `,t.appendChild(r),r.querySelector(`.btn-edit`).addEventListener(`click`,()=>n(r)),r.querySelector(`.btn-cancelOrDelete`).addEventListener(`click`,()=>i(e.id,r))});let a=document.createElement(`tr`);a.innerHTML=`
            <td>--</td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><button class="btn-add">Ajouter</button></td>
        `,t.appendChild(a),a.querySelector(`.btn-add`).addEventListener(`click`,()=>r(a))}function n(e){let t=e.querySelector(`.btn-edit`),n=e.querySelector(`.btn-cancelOrDelete`),r=e.querySelectorAll(`input`);if(!r[0].hasAttribute(`readonly`))r.forEach(e=>e.readOnly=!0),t.textContent=`Modifier`,n.textContent=`Supprimer`;else if(r.forEach(e=>e.readOnly=!1),t.textContent=`Valider`,n.textContent=`Annuler`,!e.dataset.initialValues){let t=Array.from(r).map(e=>e.value);e.dataset.initialValues=JSON.stringify(t)}}async function r(n){let r=n.querySelectorAll(`input`),i={name:r[0].value,slug:r[1].value,description:r[2].value,parent_id:r[3].value},a=await e.fetch(`admin/category/addCategoryJson`,{method:`POST`,headers:{"Content-Type":`application/json`},body:JSON.stringify(i)});a.success?t():(alert(`⚠️ `+(a.message??`Une erreur est survenue.`)),console.warn(`[addCategory]`,a))}async function i(t,n){let r=n.querySelector(`.btn-cancelOrDelete`);if(r.textContent===`Supprimer`){if(!confirm(`Confirmer la suppression ?`))return;await e.fetch(`admin/category/deleteCategory`,{method:`POST`,headers:{"Content-Type":`application/json`},body:JSON.stringify({id:t})}),n.remove()}else if(r.textContent===`Annuler`){let e=n.querySelectorAll(`input`),t=JSON.parse(n.dataset.initialValues);e.forEach((e,n)=>e.value=t[n]),e.forEach(e=>e.readOnly=!0),n.querySelector(`.btn-edit`).textContent=`Modifier`,r.textContent=`Supprimer`}}function a(){t()}export{a as default};