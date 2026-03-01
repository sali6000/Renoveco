// Fonction d'initialisation de la barre de recherche
// Elle est appelée dans app.js en fonction de la page courante
export default function initSearchBar() {
    const searchInput = document.getElementById("searchInput");
    const noResults = document.getElementById("searchResults");
    const productItems = document.querySelectorAll(".products__product");

    if (!searchInput) return;

    searchInput.addEventListener("input", () => {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        productItems.forEach(product => {
            // Recherche du <dt> correspondant à "Nom"
            const nameDt = [...product.querySelectorAll("dt")]
                .find(dt => dt.textContent.trim().toLowerCase().startsWith("nom"));

            // Récupération du texte du <dd> associé
            const name = nameDt
                ? nameDt.nextElementSibling.textContent.trim().toLowerCase()
                : "";

            // Vérifie si le nom contient la requête
            if (name.includes(query)) {
                product.style.display = "";
                visibleCount++;
            } else {
                product.style.display = "none";
            }
        });

        // Affiche ou masque le message "Aucun produit trouvé"
        noResults.style.display = (query && visibleCount === 0) ? "block" : "none";
    });
}
