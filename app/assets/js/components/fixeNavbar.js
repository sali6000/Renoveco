// app/assets/js/modules/fixeNavbar.js

// Fonction pour fixer la navbar en haut de la page
// Elle est appelée dans la page d'accueil pour s'assurer que la navbar reste visible
export default function initFixeNavbar() {
    const cell = document.querySelector('.cell.border-bottom');
    const navbar = document.querySelector('.full-navbar');

    if (cell) {
      cell.style.borderBottom = "2px solid white";
    }

    if (navbar) {
      navbar.style.position = "fixed";
    }
  };
