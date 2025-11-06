// app/assets/js/app.js

// Modules globaux
import '@js/components/header';
import { initLazyLoad } from '@js/components/lazyload';
import autoResizeText from '@js/utilities/autoResizeText';

// Modules spécifiques à la "current_page" appellée
async function initApp() {
  const page = document.body.dataset.currentPage;

  // ✅ Appliquer la fonction dès que le DOM est prêt
  autoResizeText();

  if (page) {
    try {
      const module = await import(`@js/pages/${page}`);
      if (module.default) module.default();
      // ✅ Appliquer la fonction dès que le DOM est prêt
      initLazyLoad();
      autoResizeText();
    } catch (e) {
      console.warn(`Aucun module JS pour la page "${page}"`);
    }
  }
  // Pour les pages sans module spécifique
  initLazyLoad();
}

// Exécute initApp lorsque le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", initApp);
