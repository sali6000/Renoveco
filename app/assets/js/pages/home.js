// pages/home.js
import initOnTop from '@js/components/onTop';
import initSlider from '@js/components/slider';
import initReveal from '@js/components/reveal';

// Fonction d'initialisation pour la page d'accueil
// Elle est appelée dans app.js en fonction de la page courante
export default function () {
    initOnTop();    // ← exécute le code
    initSlider();        // ← exécute le code
    initReveal();      // ← exécute le code pour fixer la navbar
}
