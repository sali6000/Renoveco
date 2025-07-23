$(document).ready(function() {
    $(".parent").click(function() {
        $(this).find(".hidden-content").slideToggle("slow");
    });
             // Make the first .title-button's content visible by default
             $(".title-button").eq(0).next(".hidden-content").show();
             $(".title-button").eq(1).next(".hidden-content").show();

             $(".hidden-content .thumbnail").click(function(event) {
                event.stopPropagation();
            });

});