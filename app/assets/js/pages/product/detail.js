// pages/product-detail.js

import initDeroulement from '@js/components/deroulement';
import initZoom from '@js/components/zoom';

// Fonction d'initialisation pour la page de détail du produit
// Elle est appelée dans app.js en fonction de la page courante
export default function () {
    initDeroulement();        // ← exécute le code
    initZoom();    // ← exécute le code
}
