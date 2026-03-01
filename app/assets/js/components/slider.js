// slider.js 
export default function initSlider() {

    const slider = document.getElementById("slides-js");
    const sliderSlide = slider.getElementsByClassName("slider-slide");
    const slide__video = slider.querySelectorAll("video");
    let currentIndex = 0;

    if (!slider) return; // ⚠️ Si le slider n'existe pas, on ne fait rien

    // Active la première slide et lance la vidéo si elle existe
    sliderSlide[currentIndex].classList.add("active");
    if (slide__video[currentIndex]) {
        slide__video[currentIndex].play().catch(err =>
            console.warn("Impossible de lire la première vidéo :", err)
        );
    }

    // Timer d'auto-défilement
    let timerId = setInterval(nextSlide, 5000);

    // Boutons next/prev si présents
    const nextBtn = slider.querySelector(".slider--event-next");
    const prevBtn = slider.querySelector(".slider--event-prev");

    if (nextBtn) nextBtn.addEventListener("click", nextSlide);
    if (prevBtn) prevBtn.addEventListener("click", prevSlide);

    // 👀 Debug : logs d'état des vidéos
    slide__video.forEach(video => {
        video.addEventListener("error", () =>
            console.error("Erreur sur", video.currentSrc, video.error)
        );
        video.addEventListener("stalled", () =>
            console.warn("Chargement bloqué :", video.currentSrc)
        );
        video.addEventListener("abort", () =>
            console.warn("Lecture interrompue :", video.currentSrc)
        );
        video.addEventListener("ended", () =>
            console.log("Vidéo terminée :", video.currentSrc)
        );
    });

    function nextSlide() {
        switchSlide("next");
    }

    function prevSlide() {
        switchSlide("prev");
    }

    function resetTimer() {
        clearInterval(timerId);
        timerId = setInterval(nextSlide, 5000);
    }

    function switchSlide(direction) {
        // 🔴 STOP la vidéo en cours au lieu de recharger le fichier
        if (slide__video[currentIndex]) {
            slide__video[currentIndex].pause();
            slide__video[currentIndex].currentTime = 0;
        }

        // Désactive la slide actuelle
        sliderSlide[currentIndex].classList.remove("active");

        // Change d'index
        if (direction === "next") {
            currentIndex = (currentIndex + 1) % sliderSlide.length;
        } else {
            currentIndex = (currentIndex - 1 + sliderSlide.length) % sliderSlide.length;
        }

        // Active la nouvelle slide
        sliderSlide[currentIndex].classList.add("active");

        // 🔵 Lance la vidéo de la nouvelle slide si présente
        if (slide__video[currentIndex]) {
            slide__video[currentIndex].play().catch(err =>
                console.warn("Impossible de lire la vidéo :", slide__video[currentIndex].currentSrc, err)
            );
        }

        resetTimer();
    }

}
