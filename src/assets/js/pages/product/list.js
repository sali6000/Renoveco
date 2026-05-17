// pages/product-list.js

import initFilterPanel from '@js/components/filterPanel';
import initFilterSearchBar from '@js/components/filterSearchbar';
import initFilterDrawer from '@js/components/filterDrawer';

// Fonction d'initialisation pour la page de liste de produits
// Elle est appelée dans app.js en fonction de la page courante
export default function () {
    initFilterPanel();
    initFilterSearchBar();
    initFilterDrawer();
}
