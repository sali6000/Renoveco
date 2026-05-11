export default function initHero() {
    const isDesktop = window.matchMedia('(min-width: 768px)').matches;

    if (isDesktop) {
        const video = document.getElementById('hero-video');
        if (video && video.dataset.src) {
            const source = document.createElement('source');
            source.src = video.dataset.src;
            source.type = video.dataset.type || 'video/webm';
            video.appendChild(source);
            video.load();
            video.play().catch(() => { });
        }
    }
}