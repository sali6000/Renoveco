const elementAChanger = document.querySelector('.full-navbar');
const element2AChanger = document.querySelectorAll('.link');
const element3AChanger = document.querySelector('.full-navbar .logo-texte');
const element4AChanger = document.querySelector('.contact');
const element5AChanger = document.querySelector('#searchInputIndex');
const element6AChanger = document.querySelector('#searchTexte');
const element7AChanger = document.querySelector('.navbar');




elementAChanger.classList.add('full-navbar-show');
element3AChanger.classList.add('logo-texte-show');
element4AChanger.classList.add('contact-show');
element5AChanger.classList.add('searchInput');
element6AChanger.classList.add('searchInput');
element7AChanger.classList.add('navbarTopHide');


for (let i = 0; i < element2AChanger.length; i++) {
    element2AChanger[i].classList.add('link-show');
}


window.addEventListener("scroll", function () {
    if (window.scrollY == 0) {
        elementAChanger.classList.add('full-navbar-show');
        element3AChanger.classList.add('logo-texte-show');
        element4AChanger.classList.add('contact-show');
        element5AChanger.classList.add('searchInput');
        element6AChanger.classList.add('searchInput');
        element7AChanger.classList.add('navbarTopHide');



        for (let i = 0; i < element2AChanger.length; i++) {
            element2AChanger[i].classList.add('link-show');
        }
    } else {
        elementAChanger.classList.remove('full-navbar-show');
        element3AChanger.classList.remove('logo-texte-show');
        element4AChanger.classList.remove('contact-show');
        element5AChanger.classList.remove('searchInput');
        element6AChanger.classList.remove('searchInput');
        element7AChanger.classList.remove('navbarTopHide');


        for (let i = 0; i < element2AChanger.length; i++) {
            element2AChanger[i].classList.remove('link-show');
        }
    }
});