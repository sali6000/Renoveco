/*document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInputIndex");
    const searchResults = document.getElementById("searchResults");
    searchInput.addEventListener("input", () => {
        const query = searchInput.value.toLowerCase();
        searchResults.innerHTML = "";
        if (query) {
            const filteredData = windows.searchbarData.filter((item) => {
                return Object.values(item).some((value) => {
                    return typeof value === "string" && value.toLowerCase().includes(query);
                });
            });
            filteredData.forEach((item) => {
                const li = document.createElement("li");
                const a = document.createElement("a");
                a.href = "#";
                a.textContent = `${item.name} - ${item.type_fr}`;
                li.appendChild(a);
                searchResults.appendChild(li);
            });
        }
    });
});
*/