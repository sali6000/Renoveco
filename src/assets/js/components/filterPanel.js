import { updateFilter } from './filterEngine.js';

export default function initFilter() {
    const panel = document.querySelector('.c-filter-panel');

    if (!panel) return;

    panel.addEventListener('change', () => {
        updateFilter(getActiveFilters(panel));
    });
}

function getActiveFilters(panel) {
    const checked = (name) =>
        Array.from(panel.querySelectorAll(`input[name="${name}"]:checked`))
            .map(el => el.value);

    return {
        categories: checked('category'),
        brands: checked('brand'),
        priceRange: panel.querySelector('input[name="price"]:checked')?.value || null,
    };
}