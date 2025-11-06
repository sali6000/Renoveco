export default function autoResizeText(selector = ".resizeText") {
    document.querySelectorAll(selector).forEach(container => {
        let fontSize = parseFloat(window.getComputedStyle(container).fontSize);

        // Réinitialise d'abord la taille (au cas où tu recharges/redimensionnes)
        container.style.fontSize = "";

        // Vérifie que ça déborde et réduit si nécessaire
        while (container.scrollHeight > container.clientHeight && fontSize > 10) {
            fontSize -= 1;
            container.style.fontSize = fontSize + "px";
        }
    });
}

// Réappliquer quand on redimensionne la fenêtre
window.addEventListener("resize", () => {
    autoResizeText(".resizeText");
});
