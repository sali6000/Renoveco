// lazyload.js
let lazyObserver;

export function initLazyLoad() {
    // Crée l'observer une seule fois
    if (!lazyObserver) {
        lazyObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    const element = entry.target;

                    // Effet cascade
                    setTimeout(() => {
                        requestAnimationFrame(() => {
                            element.src = element.dataset.src;
                            element.classList.add("loaded");
                        });
                    }, index * 100);

                }
            });
        }, { threshold: 0.2 });
    }

    // Sélectionne uniquement les éléments non encore observés
    const lazyElements = document.querySelectorAll(".lazy-content:not(.loaded)");

    lazyElements.forEach((el) => lazyObserver.observe(el));
}
