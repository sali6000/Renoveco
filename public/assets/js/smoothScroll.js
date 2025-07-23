let sliders_texte = document.getElementsByClassName('slider_texte_position');



window.addEventListener('scroll', function () {
    var value = window.scrollY;

    for (let i = 0; i < sliders_texte.length; i++) {
        sliders_texte[i].style.marginBottom = value * 0.1 + '%';
    }
});