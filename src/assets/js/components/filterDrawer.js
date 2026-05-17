export default function initFilterDrawer() {
    const trigger = document.getElementById('filterTrigger');
    const panel = document.getElementById('filterPanel');
    const overlay = document.getElementById('filterOverlay');
    const close = document.getElementById('filterClose');

    if (!trigger || !panel) return;

    const open = () => {
        panel.classList.add('is-open');
        overlay.hidden = false;
        trigger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    };

    const shut = () => {
        panel.classList.remove('is-open');
        overlay.hidden = true;
        trigger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    };

    trigger.addEventListener('click', open);
    close.addEventListener('click', shut);
    overlay.addEventListener('click', shut);

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') shut();
    });
}