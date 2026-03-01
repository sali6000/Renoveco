// modules/deroulement.js
import $ from 'jquery';

export default function initDeroulement() {
    $(document).ready(function () { 
        // Toggle au clic sur les éléments .parent
        $(".parent").click(function () {
            $(this).find(".hidden-content").slideToggle("slow");
        });

        // Affiche par défaut le contenu des deux premiers boutons
        $(".title-button").eq(0).next(".hidden-content").show();
        $(".title-button").eq(1).next(".hidden-content").show();

        // Empêche la propagation du clic depuis une miniature
        $(".hidden-content .thumbnail").click(function (event) {
            event.stopPropagation();
        });
    });
}
