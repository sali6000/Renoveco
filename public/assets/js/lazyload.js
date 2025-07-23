document.addEventListener("DOMContentLoaded", function () {
    const lazyElements = document.querySelectorAll(".lazy-content");
    const lazyLoad = (entries, observer) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const element = entry.target;
                if (element.tagName === "IMG" || element.tagName === "IFRAME") {
                    element.src = element.dataset.src;
                } else {
                    fetch(element.dataset.src)
                        .then((response) => response.text())
                        .then((data) => (element.innerHTML = data));
                }
                element.classList.add("loaded");
                observer.unobserve(element);
            }
        });
    };
    const observer = new IntersectionObserver(lazyLoad, { rootMargin: "0px 0px 50px 0px" });
    lazyElements.forEach((element) => {
        observer.observe(element);
    });
});
