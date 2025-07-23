let compteur = 0;
let timer, elements, slides, slideWitdh, speed, transition;
var myVideo = document.getElementById('myVideo');

window.onload = () => {

    const diapo = document.querySelector(".diaporama");
    // On récupère le data-speed
    speed = diapo.dataset.speed;
    transition = diapo.dataset.transition;
    elements = document.querySelector(".elements");
    let firstImage = elements.firstElementChild.cloneNode(true);
    elements.appendChild(firstImage);
    slides = Array.from(elements.children);
    timer = setInterval(slideNext, 5000);

    function slideNext() {
        slideWidth = diapo.getBoundingClientRect().width;
        // On incrémente le compteur
        compteur++;
        elements.style.transition = transition + "ms linear";

        let decal = -slideWidth * compteur;
        elements.style.transform = `translateX(${decal}px)`;

        // On attend la fin de la transition et on "rembobine" de façon cachée
        setTimeout(function () {
            if (compteur >= slides.length - 1) {
                compteur = 0;
                elements.style.transition = "unset";
                elements.style.transform = "translateX(0)";
            }
        }, transition);
    }
}
