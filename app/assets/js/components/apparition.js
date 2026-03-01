// modules/apparition.js

export default function initApparition() {
    const boxes = document.querySelectorAll('.slide');

    if (boxes.length === 0) return; // sécurité : aucun élément, on ne fait rien

    function checkBoxes() {
        const triggerBottom = window.innerHeight * 0.8; // 4/5

        boxes.forEach((box) => {
            const boxTop = box.getBoundingClientRect().top;
            if (boxTop < triggerBottom) {
                box.classList.add('show');
            } else {
                box.classList.remove('show');
            }
        });
    }

    window.addEventListener('scroll', checkBoxes);
    checkBoxes(); // Exécution initiale
}
