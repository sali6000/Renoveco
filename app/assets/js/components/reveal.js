export default function initReveal() {

    const elements = document.querySelectorAll(".reveal, .reveal-infiny");

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            const isInfinite = entry.target.classList.contains("reveal-infiny");

            if (entry.isIntersecting) {

                // Gestion du delay pour les éléments "normaux" uniquement
                if (!isInfinite) {
                    const siblings = [...entry.target.parentElement.querySelectorAll(".reveal")];
                    const index = siblings.indexOf(entry.target);
                    entry.target.style.transitionDelay = `${index * 0.05}s`;
                }

                entry.target.classList.add("visible");

                // Si ce n'est pas un infini, on arrête l'observation
                if (!isInfinite) obs.unobserve(entry.target);

            } else if (isInfinite) {
                // Pour les éléments infinies, on retire la classe visible quand on sort
                entry.target.classList.remove("visible");
            }
        });
    }, { threshold: 0.2 });

    elements.forEach(el => observer.observe(el));
}
