export default function initMenuHamburger() {
    const menu = document.getElementById("mobile-menu");
    const hamburger = document.getElementById("menu-hamburger");
    const btn = document.getElementById("hamburger-btn");

    btn.addEventListener("click", () => {
        menu.classList.toggle("open");
        hamburger.classList.toggle("open");

        const isOpen = menu.classList.contains("open");
        btn.setAttribute("aria-expanded", isOpen);
    });
}