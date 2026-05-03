export default function initMenuHamburger() {
    const btn = document.querySelector('[data-hamburger]');
    const nav = document.querySelector('[data-mobile-nav]');
    const overlay = document.querySelector('[data-mobile-overlay]');
    const closeBtn = document.querySelector('[data-mobile-close]');

    if (!btn || !nav) return;

    function open() {
        nav.classList.add('mobile-nav--open');
        overlay.classList.add('mobile-overlay--visible');
        btn.classList.add('hamburger--open');
        btn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden'; // bloque le scroll
    }

    function close() {
        nav.classList.remove('mobile-nav--open');
        overlay.classList.remove('mobile-overlay--visible');
        btn.classList.remove('hamburger--open');
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
            const item = accordionBtn.closest('.mobile-nav__item--accordion');
            item.classList.toggle('is-open');
            const expanded = item.classList.contains('is-open');
            accordionBtn.setAttribute('aria-expanded', expanded);
        });
    });
}