export default function initFilter() {
    const checkboxes = document.querySelectorAll('.filters input[type="checkbox"]');
    const radios = document.querySelectorAll('.filters input[type="radio"]');

    [...checkboxes, ...radios].forEach(input => {
        input.addEventListener('change', filterProducts);
    });

    function filterProducts() {
        // --- Catégories cochées ---
        const categories = Array.from(document.querySelectorAll('input[name="category"]:checked'))
            .map(cb => cb.value);

        // --- Marques cochées ---
        const brands = Array.from(document.querySelectorAll('input[name="brand"]:checked'))
            .map(cb => cb.value);

        // --- Prix sélectionné (radio => 1 seul choix) ---
        const priceRange = document.querySelector('input[name="price"]:checked')?.value || null;

        document.querySelectorAll('.products__product').forEach(product => {
            const productCategory = product.dataset.category;
            const productBrand = product.dataset.brand;
            const productPrice = parseFloat(product.dataset.price);

            // --- Match catégorie (si filtre actif) ---
            const matchCategory = categories.length === 0 || categories.includes(productCategory);

            // --- Match marque (si filtre actif) ---
            const matchBrand = brands.length === 0 || brands.includes(productBrand);

            // --- Match prix ---
            let matchPrice = true;
            if (priceRange) {
                const [min, max] = priceRange.split('-').map(Number);
                matchPrice = productPrice >= min && productPrice <= max;
            }

            // --- Final ---
            if (matchCategory && matchBrand && matchPrice) {
                product.style.display = ''; // ou product.style.visibility = 'visible';
            } else {
                product.style.display = 'none'; // ou product.style.visibility = 'hidden';
            }
        });
    }
}
