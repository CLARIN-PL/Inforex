/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
    autoreizeFitToScreen();
    $(window).resize(function(){
        autoreizeFitToScreen();
    });
});

/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function autoreizeFitToScreen(){
    $(".scrollingWrapper").hide();
    $(".scrollingWrapper .scrolling").hide();

    var windowHeight = $(window).height();
    var boilerplatesHeight = $("#page").outerHeight(true);

    $(".scrollingWrapper").each(function(){
        var scrollingCount = $(this).find(".scrolling").size();
        var scrollingWrapperHeight = $(this).outerHeight(true);
        $(this).find(".scrollingFix").each(function(){
            scrollingWrapperHeight += $(this).outerHeight();
        });
        $(this).find(".scrolling").css("height", ((windowHeight - boilerplatesHeight - scrollingWrapperHeight - 5))/scrollingCount + "px");
        $(this).find(".scrolling").css("height", (windowHeight - boilerplatesHeight - scrollingWrapperHeight - 5) + "px");
    })

    $(".scrollingWrapper").show();
    $(".scrollingWrapper .scrolling").show();
}