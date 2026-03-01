// pages/product-list.js

import initFilter from '@js/components/filter';
import initSearchBar from '@js/components/searchbar';

// Fonction d'initialisation pour la page de liste de produits
// Elle est appelée dans app.js en fonction de la page courante
export default function () {
    initFilter();        // ← exécute le code
    initSearchBar();    // ← exécute le code
}
