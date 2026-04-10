// src/assets/js/app.js

// Modules globaux
import initReveal from '@js/components/reveal';
import initHamburger from '@js/pages/shared/header';

// Modules spécifiques à la "current_page" appellée
async function initApp() {
  const page = document.body.dataset.currentPage;
  const pagePath = page.split('-').join('/'); // "admin/category/index"

  // ✅ Appliquer la fonction dès que le DOM est prêt
  initReveal();
  initHamburger();
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
}

// Exécute initApp lorsque le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", initApp);
