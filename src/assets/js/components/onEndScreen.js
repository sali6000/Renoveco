export default function initOnEndScreen() {

    const buttons = document.querySelectorAll(".ctaButton");

    function updateButtonColor() {

        const shouldBeDark = window.scrollY >= window.innerHeight;

        buttons.forEach(btn => {
            btn.classList.toggle("dark-mode", shouldBeDark);
            btn.classList.toggle("light-mode", !shouldBeDark);
        });
    }

    // ✅ état initial au chargement
    updateButtonColor();

    // ✅ mise à jour lors du scroll
    window.addEventListener("scroll", updateButtonColor);
}