$(document).ready(function() {
    function applyInitialFilter() {
        var path = window.location.pathname.split('/');
        var initialCategory = path[path.length - 1];

        if (initialCategory && initialCategory !== 'list') {
            $('.filter[value="' + initialCategory + '"]').prop('checked', true);
            $('.item').hide();
            $('.item[data-category="' + initialCategory + '"]').show();
        } else {
            $('.item').show(); // Affiche tous les éléments si aucune catégorie spécifique n'est dans l'URL
        }
    }

    applyInitialFilter();

    $('.filter').on('change', function() {
        var selectedFilters = $('.filter:checked').map(function() {
            return $(this).val();
        }).get();

        $('.item').hide();
        if (selectedFilters.length > 0) {
            selectedFilters.forEach(function(filter) {
                $('.item[data-category="' + filter + '"]').show();
            });
        } else {
            $('.item').show();
        }
    });
});
