
// JavaScript Document
function isDevice() {
    return ((/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase())))
}

function initZoom(width, height) {
    $.removeData('#zoom_10', 'elevateZoom');
    $('.zoomContainer').remove();
    $('.zoomWindowContainer').remove();
    $("#zoom_10").elevateZoom({
        responsive: true,
        tint: true,
        tintColour: '#E84C3C',
        tintOpacity: 0.5,
        easing: true,
        borderSize: 0,
        lensSize: 100,
        constrainType: "height",
        loadingIcon: "https://icodefy.com/Tools/iZoom/images/loading.GIF",
        containLensZoom: false,
        zoomWindowPosition: 1,
        zoomWindowOffetx: 20,
        zoomWindowWidth: width,
        zoomWindowHeight: height,
        gallery: 'gallery_pdp',
        galleryActiveClass: "active",
        zoomWindowFadeIn: 500,
        zoomWindowFadeOut: 500,
        lensFadeIn: 500,
        lensFadeOut: 500,
        cursor: "https://icodefy.com/Tools/iZoom/images/zoom-out.png",
    });
}

$(document).ready(function() {
    /* init vertical carousel if thumb image length greater that 4 */
    if ($("#gallery_pdp a").length > 4) {
        $("#gallery_pdp a").css("margin", "0");
        $("#gallery_pdp").rcarousel({
            orientation: "vertical",
            visible: 4,
            width: 105,
            height: 70,
            margin: 5,
            step: 1,
            speed: 500,
        });
        $("#ui-carousel-prev").show();
        $("#ui-carousel-next").show();
    }
    /* Init Product zoom */
    initZoom(500, 475);

    $("#ui-carousel-prev").click(function() {
        initZoom(500, 475);
    });

    $("#ui-carousel-next").click(function() {
        initZoom(500, 475);
    });

    // $(".zoomContainer").width($("#zoom_10").width());

    // $("body").delegate(".fancybox-inner .mega_enl", "click", function() {
    //     $(this).html("");
    //     $(this).hide();
    // });
			// $('#gallery_pdp img').click((e) => {
			// 	console.log(e)
			// })

});

$(window).resize(function() {
    var docWidth = $(document).width();
    if (docWidth > 769) {
        initZoom(500, 475);
    } else {
        $.removeData('#zoom_10', 'elevateZoom');
        $('.zoomContainer').remove();
        $('.zoomWindowContainer').remove();
        $("#zoom_10").elevateZoom({
            responsive: true,
            tint: false,
            tintColour: '#3c3c3c',
            tintOpacity: 0.5,
            easing: true,
            borderSize: 0,
            loadingIcon: "https://icodefy.com/Tools/iZoom/images/loading.GIF",
            zoomWindowPosition: "productInfoContainer",
            zoomWindowWidth: 330,
            gallery: 'gallery_pdp',
            galleryActiveClass: "active",
            zoomWindowFadeIn: 500,
            zoomWindowFadeOut: 500,
            lensFadeIn: 500,
            lensFadeOut: 500,
            cursor: "https://icodefy.com/Tools/iZoom/images/zoom-out.png",
        });
				
    }
})

$(document).ready(function() {
 $("#zoom_10").fancybox();
});