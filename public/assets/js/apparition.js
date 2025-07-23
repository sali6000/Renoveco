const boxes = document.querySelectorAll('.slide');

window.addEventListener('scroll', checkBoxes);

checkBoxes();

function checkBoxes() {
    const triggerBottom = window.innerHeight / 5 * 4;

    boxes.forEach((test) => {
        const boxTop = test.getBoundingClientRect().top;
        if (boxTop < triggerBottom) {
            test.classList.add('show');
        } else {
            test.classList.remove('show');
        }
    });
}