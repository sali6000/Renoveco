// app/assets/js/app.js

// Modules globaux
import { initLazyLoad } from '@js/components/lazyload';
import initReveal from '@js/components/reveal';

// Modules spécifiques à la "current_page" appellée
async function initApp() {
  const page = document.body.dataset.currentPage;
  const pagePath = page.split('-').join('/'); // "admin/category/index"

  // ✅ Appliquer la fonction dès que le DOM est prêt
  initReveal();

  if (page) {
    try {
      const module = await import(`@js/pages/${pagePath}.js`);// product-list => @js/pages/product/list.js
      if (module.default) module.default();
    } catch (e) {
      console.warn(`Aucun module JS pour la page "${page}"`);
    }
  }
  // ✅ Appliquer la fonction dès que le DOM est prêt
  initReveal();
  // Pour les pages sans module spécifique
  initLazyLoad();
}

// Exécute initApp lorsque le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", initApp);
