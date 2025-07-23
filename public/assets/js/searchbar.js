$(document).ready(function() {
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        var hasResults = false;

        $("#itemList li").each(function() {
            var text = $(this).text().toLowerCase();

            if (text.indexOf(value) > -1 && value.length > 0) {
                $(this).show();
                hasResults = true;
            } else {
                if(value.length <= 0)
                    {
                        hasResults = true;
                    }
                $(this).hide();
            }
        });

        $("#noResults").toggle(!hasResults);
    });
});