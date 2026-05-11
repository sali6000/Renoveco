// src/assets/js/app.js

// Modules globaux
import initReveal from '@js/components/reveal';
import initHeader from '@js/pages/shared/header';

// Vite génère le mapping complet à la compilation
const pages = import.meta.glob([
  './pages/**/*.js',
  '!./pages/shared/**'
]);

// Modules spécifiques à la "current_page" appellée
async function initApp() {
  const page = document.body.dataset.currentPage;
  const pagePath = page.split('-').join('/'); // "admin/category/index"
  const key = `./pages/${pagePath}.js`;

  // ✅ Appliquer la fonction dès que le DOM est prêt
  initReveal();
  initHeader();

  if (page && pages[key]) {
    try {
      const module = await pages[key]();
      if (module.default) module.default();
    } catch (e) {
      console.warn(`Erreur dans le module "${page}"`, e);
    }
  }
}

// Exécute initApp lorsque le DOM est complètement chargé
document.addEventListener("DOMContentLoaded", initApp);
