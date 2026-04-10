
import ApiHelper from '@js/helpers/ApiHelper';

// Chargement automatique à l'appelle de la page "admin"
export default async function loadCategories() {

    // Récupération des catégories
    const res = await fetch('admin/category/getCategoriesJson');
    const categories = await res.json();

    // Ciblage du tableau des catégories
    const tbody = document.querySelector('#category-table tbody');
    tbody.innerHTML = ''; // reset

    categories.forEach(cat => {

        // Créer une ligne
        const tr = document.createElement('tr');

        // Remplir la ligne avec les colonnes et valeurs suivantes:
        tr.innerHTML = `
                <td>${cat.id}</td>
                <td><input type="text" value="${cat.name}" readonly></td>
                <td><input type="text" value="${cat.slug}" readonly></td>
                <td><input type="text" value="${cat.description}" readonly></td>
                <td><input type="text" value="${cat.parent_id}" readonly></td>
                <td>
                    <button class="btn-edit">Modifier</button>
                    <button class="btn-cancelOrDelete">Supprimer</button>
                </td>
            `;

        // Ajouter la ligne dans le tableau
        tbody.appendChild(tr);

        // Ajouter les écouteurs d'évènements sur les boutons "Modifier" et "Supprimer/Annuler"
        tr.querySelector('.btn-edit').addEventListener('click', () => toggleEdit(tr));
        tr.querySelector('.btn-cancelOrDelete').addEventListener('click', () => cancelEditOrDeleteCategory(cat.id, tr));
    });

    // Ajouter une ligne permettant l'ajout d'une nouvelle catégorie
    const trAdd = document.createElement('tr');
    trAdd.innerHTML = `
            <td>--</td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><button class="btn-add">Ajouter</button></td>
        `;
    tbody.appendChild(trAdd);
    trAdd.querySelector('.btn-add').addEventListener('click', () => addCategory(trAdd));
}

// Réaction du bouton "Modifier" sur la ligne concernée (eventlistener "Click")
function toggleEdit(tr) {

    // Sélection des éléments concernés par le changement
    const editBtn = tr.querySelector('.btn-edit');
    const deleteBtn = tr.querySelector('.btn-cancelOrDelete');
    const inputs = tr.querySelectorAll('input');

    // Si en lecture seule
    const isReadonly = inputs[0].hasAttribute('readonly');

    if (!isReadonly) {
        // Si pas en lecture seul, on désactive le mode modification
        inputs.forEach(i => i.readOnly = true);
        editBtn.textContent = 'Modifier';
        deleteBtn.textContent = 'Supprimer';
    } else {
        // Si en lecture seul, activer le mode modification
        inputs.forEach(i => i.readOnly = false);
        editBtn.textContent = 'Valider';
        deleteBtn.textContent = 'Annuler';

        // Sauvegarder les valeurs initiales dans un data-attribute pour pouvoir les restaurer en cas d'annulation
        if (!tr.dataset.initialValues) {
            const values = Array.from(inputs).map(i => i.value);
            tr.dataset.initialValues = JSON.stringify(values);
        }
    }
}

// Réaction du bouton "Ajouter" sur la ligne concernée (eventlistener "Click")
async function addCategory(tr) {

    // Sélection des éléments concernés par le changement
    const inputs = tr.querySelectorAll('input');
    const data = {
        name: inputs[0].value,
        slug: inputs[1].value,
        description: inputs[2].value,
        parent_id: inputs[3].value
    };

    // Appel API pour ajouter la catégorie
    const response = await ApiHelper.fetch('admin/category/addCategoryJson', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });

    // Si succès, recharger la liste des catégories
    if (response.success) {
        loadCategories();
    } else {
        // Sinon, afficher une alerte d'erreur
        alert('⚠️ ' + (response.message ?? 'Une erreur est survenue.'));
        console.warn('[addCategory]', response);
    }
}

// Réaction du bouton "Annuler/Supprimer" sur l'id et la ligne concernée (eventlistener "Click") 
async function cancelEditOrDeleteCategory(id, tr) {

    // Sélection des éléments concernés par le changement
    const deleteBtn = tr.querySelector('.btn-cancelOrDelete');

    // Si le bouton cliqué s'appelle "Supprimer" alors on procède à la suppression
    if (deleteBtn.textContent === 'Supprimer') {
        if (!confirm('Confirmer la suppression ?')) return;

        await ApiHelper.fetch('admin/category/deleteCategory', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        });
        tr.remove();

        // Sinon, si le bouton cliqué s'appelle "Annuler", on annule les modifications
    } else if (deleteBtn.textContent === 'Annuler') {
        // Restaurer les valeurs initiales
        const inputs = tr.querySelectorAll('input');
        const initialValues = JSON.parse(tr.dataset.initialValues);
        inputs.forEach((i, idx) => i.value = initialValues[idx]);

        // Revenir au mode lecture seule
        inputs.forEach(i => i.readOnly = true);
        tr.querySelector('.btn-edit').textContent = 'Modifier';
        deleteBtn.textContent = 'Supprimer';
    }
}