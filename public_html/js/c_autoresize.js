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
    // Ukryj elementy zapamiętując, które były domyślnie widoczne
    var defaultVisible = [];
    $(".scrollingWrapper .scrolling").each(function(){
        if ( $(this).css("display") != "none" ){
            defaultVisible.push(this);
        }
        $(this).hide();
    });
    $(".scrollingWrapper").each(function(){
        if ( $(this).css("display") != "none" ){
            defaultVisible.push(this);
        }
        $(this).hide();
    });
    $(".scrollingWrapper .scrollingFix").hide();

    // Oblicz wysokości
    var windowHeight = $(window).height();
    var boilerplatesHeight = $("#page").outerHeight(true);
    $(".scrollingWrapper .scrollingFix").show();

    $(".scrollingWrapper").each(function(){
        var scrollingCount = $(this).find(".scrolling").size();
        console.log(scrollingCount);
        var scrollingWrapperHeight = $(this).outerHeight(true);
        $(this).find(".scrollingFix").each(function(){
            scrollingWrapperHeight += $(this).outerHeight();
        });
        $(this).find(".scrolling").css("height", ((windowHeight - boilerplatesHeight - scrollingWrapperHeight - 5))/scrollingCount + "px");
        $(this).find(".scrollingAccordion .scrolling").css("height", (windowHeight - boilerplatesHeight - scrollingWrapperHeight - 5) + "px");
    })

    // Pokaż elementy
    $.each(defaultVisible, function(index, item){
       $(item).show();
    });
}