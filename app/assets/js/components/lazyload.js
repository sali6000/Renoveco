export function initLazyLoad() {
    const lazyElements = document.querySelectorAll(".lazy-content");

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                const element = entry.target;

                // Effet cascade → chaque élément attend un peu plus
                setTimeout(() => {
                    if (element.tagName === "IMG" || element.tagName === "IFRAME") {
                        element.src = element.dataset.src;
                    } else {
                        fetch(element.dataset.src)
                            .then((response) => response.text())
                            .then((data) => (element.innerHTML = data));
                    }

                    element.classList.add("loaded");
                    observer.unobserve(element);
                }, index * 100); // 0ms, 100ms, 200ms, ...
            }
        });
    }, { threshold: 0.2 });

    lazyElements.forEach((element) => {
        observer.observe(element);
    });
};
