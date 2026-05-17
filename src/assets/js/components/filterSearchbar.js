import { updateFilter } from './filterEngine.js';

export default function initSearchBar() {
    const searchInput = document.getElementById('searchInput');

    if (!searchInput) return;

    searchInput.addEventListener('input', () => {
        updateFilter({ query: searchInput.value.trim().toLowerCase() });
    });
}