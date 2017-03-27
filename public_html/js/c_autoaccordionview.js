/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
    //setupAccordionView();
});

/**
 * Setup the mechanism for saving the active accordion panel after relading the page.
 */
function setupAccordionView(){
    var accordionId = $.cookie("accordion_active");
    if ( accordionId ) {
        var accordionPanel = $("#accordion #" + accordionId);
        if (accordionPanel) {
            console.log($(accordionPanel).next().is(":visible"));
            if (!$(accordionPanel).next().is(":visible")) {
                $('#accordion .in').collapse('toggle');
                $(accordionPanel).next().show();
            }
        }
    }

    $("#accordion .panel-title a").click(function(){
        var id = $(this).closest(".panel-heading").attr("id");
        $.cookie("accordion_active", id);
        console.log(id);
    });
}