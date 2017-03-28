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
    $(".scrollingWrapper .scrollingAccordion").each(function(){
        if ( $(this).css("display") != "none" ){
            defaultVisible.push(this);
        }
        $(this).hide();
    });
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
    $(".scrollingWrapper .scrollingAccordion").hide();

    // Oblicz wysokości
    var windowHeight = $(window).height();
    var boilerplatesHeight = $("#page").outerHeight(true);

    $(".scrollingWrapper").each(function(){
        var scrollingCount = $(this).find(".scrolling").size();
        var scrollingWrapperHeight = $(this).outerHeight(true);
        $(this).find(".scrolling").css("height", ((windowHeight - boilerplatesHeight - scrollingWrapperHeight - 15))/scrollingCount + "px");

        $(this).find(".scrollingAccordion").each(function(){
            var scrollingAccordionHeight = scrollingWrapperHeight + $(this).actual( 'outerHeight', { includeMargin : true });;
            $(this).find(".scrolling").css("height", (windowHeight - boilerplatesHeight - scrollingAccordionHeight - 15) + "px");
        })
    })

    // Pokaż elementy
    $.each(defaultVisible, function(index, item){
       $(item).show();
    });
}