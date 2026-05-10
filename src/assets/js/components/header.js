export default function initNew() {

    // Remplace les classes scrolled par les modificateurs BEM
    const navbar = document.querySelector('[data-navbar]');
    const isHome = document.body.dataset.currentPage === 'home-index';

    if (isHome) {
        navbar.classList.add('shared-navbar--transparent');

        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.replace('shared-navbar--transparent', 'shared-navbar--scrolled');
            } else {
                navbar.classList.replace('shared-navbar--scrolled', 'shared-navbar--transparent');
            }
        });
    } else {
        navbar.classList.add('shared-navbar--solid');
    }

    // Dropdown accessibilité clavier
    document.querySelectorAll('.shared-navbar__link--dropdown').forEach(btn => {
        btn.addEventListener('click', () => {
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', !expanded);
        });
    });
}