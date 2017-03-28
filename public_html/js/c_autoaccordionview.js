/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
    $("#accordion .panel-title a").click(function(){
        var id = $(this).attr("aria-controls");
        $.cookie("accordion_active", id);
    });
});
