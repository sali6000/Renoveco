// modules/smoothScroll.js

export default function initSmoothScroll() {
    const slidersTexte = document.getElementsByClassName('slides__slide__texts');

    if (slidersTexte.length === 0) return; // Sécurité : rien à faire si aucun élément

    window.addEventListener('scroll', () => {
        const value = window.scrollY;

        for (let i = 0; i < slidersTexte.length; i++) {
            slidersTexte[i].style.marginBottom = (value * 0.1) + '%';
        }
    });
}
