const state = {
    categories: [],
    brands: [],
    priceRange: null,
    query: '',
};

const filterCount = document.getElementById('filterCount');
const items = document.querySelectorAll('.l-grid > li');
const noResults = document.getElementById('searchResults');

export function updateFilter(partial) {
    Object.assign(state, partial);
    applyFilters();
}

function applyFilters() {
    let visibleCount = 0;

    items.forEach(item => {
        const matches = matchesAll(item, state);
        item.hidden = !matches;
        visibleCount += matches ? 1 : 0;
    });

    if (noResults) {
        noResults.hidden = visibleCount !== 0;
    }
}

function matchesAll(item, { categories, brands, priceRange, query }) {
    const { category, brand, price, search } = item.dataset;

    const matchCategory = !categories.length || categories.includes(category);
    const matchBrand = !brands.length || brands.includes(brand);
    const matchPrice = !priceRange || matchesPriceRange(parseFloat(price), priceRange);
    const matchQuery = !query || (search?.toLowerCase().includes(query) ?? false);

    return matchCategory && matchBrand && matchPrice && matchQuery;
}

function matchesPriceRange(price, range) {
    const [min, max] = range.split('-').map(Number);
    return price >= min && price <= max;
}