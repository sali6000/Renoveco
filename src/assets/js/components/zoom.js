// modules/zoom.js

export default function initZoom() {
    const mainImage = document.getElementById('mainImage');
    if (!mainImage) return; // Sécurité si pas d'image principale

    const thumbnails = document.querySelectorAll('.thumbnail');

    let isZoomed = false;
    let isDragging = false;
    let startX, startY, initialX = 0, initialY = 0;
    let scale = 1;
    const scaleStep = 0.3;
    const maxScale = 10;
    const minScale = 1;

    mainImage.addEventListener('wheel', function(event) {
        event.preventDefault();
        if (event.deltaY < 0) {
            scale = Math.min(scale + scaleStep, maxScale);
        } else {
            scale = Math.max(scale - scaleStep, minScale);
        }
        if (isDragging) {
            mainImage.style.transform = `scale(${scale}) translate(${initialX}px, ${initialY}px)`;
        }
    });

    mainImage.addEventListener('click', function() {
        if (isZoomed) {
            mainImage.classList.remove('zoomed');
            mainImage.style.transform = '';
            isZoomed = false;
            isDragging = false;
            scale = 1;
            initialX = 0;
            initialY = 0;
        } else {
            mainImage.classList.add('zoomed');
            isZoomed = true;
            isDragging = true;
        }
    });

    mainImage.addEventListener('mousedown', function(e) {
        if (!isDragging) {
            isDragging = true;
            startX = e.pageX - initialX;
            startY = e.pageY - initialY;
            mainImage.classList.add('grabbing');
        }
    });

    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            initialX = e.pageX - startX;
            initialY = e.pageY - startY;
            mainImage.style.transform = `scale(${scale}) translate(${initialX}px, ${initialY}px)`;
        }
    });

    document.addEventListener('mouseup', function() {
        if (isDragging) {
            mainImage.classList.remove('grabbing');
            mainImage.classList.add('grab');
        }
    });

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            mainImage.src = thumbnail.src;
            mainImage.classList.remove('zoomed');
            mainImage.style.transform = '';
            isZoomed = false;
            isDragging = false;
        });
    });
}
