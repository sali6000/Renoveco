export default function initOnTop() {
    const header = document.querySelector('.section-navbar');

    if (!header) return;

    // On ne fait le scroll que sur la page Home
    if (document.body.dataset.currentPage === 'home') {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    } else {
        // Sur les autres pages, navbar-contact est déjà visible, rien à faire
        header.classList.add('scrolled'); // optionnel si tu veux que le header soit fixe dès le départ
    }
}
