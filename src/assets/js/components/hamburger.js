export default function initMenuHamburger() {
    const btn = document.querySelector('[data-hamburger]');
    const nav = document.querySelector('[data-hamburger-nav]');
    const overlay = document.querySelector('[data-hamburger-overlay]');
    const closeBtn = document.querySelector('[data-hamburger-close]');

    if (!btn || !nav) return;

    function open() {
        nav.classList.add('c-hamburger-nav--open');
        overlay.classList.add('c-hamburger-overlay--visible');
        btn.classList.add('c-hamburger--open');
        btn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden'; // bloque le scroll
    }

    function close() {
        nav.classList.remove('c-hamburger-nav--open');
        overlay.classList.remove('c-hamburger-overlay--visible');
        btn.classList.remove('c-hamburger--open');
        btn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    btn.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', close);

    // Ferme avec Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') close();
    });

    // Accordéon services
    document.querySelectorAll('[data-accordion]').forEach(accordionBtn => {
        accordionBtn.addEventListener('click', () => {
            const item = accordionBtn.closest('.c-hamburger-nav__item--accordion');
            item.classList.toggle('is-open');
            const expanded = item.classList.contains('is-open');
            accordionBtn.setAttribute('aria-expanded', expanded);
        });
    });
}