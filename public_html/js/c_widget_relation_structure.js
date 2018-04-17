/**
 * Zestaw metod do obsługi drzewa z typami relacji.
 * Obsługuje zwijanie, rozwijanie typów.
 */
var url = $.url(window.location.href);
var corpus_id = url.param("corpus");

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 */
function setupRelationTree(){
    $("#newExportForm").on("click", ".relationToggleLayer", function(){
        if ($(this).hasClass("ui-icon-circlesmall-plus")){
            $(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
            $(this).parents(".relationLayerRow").nextUntil(".relationLayerRow",".relationSublayerRow").show();
        }
        else {
            $(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
            $(this).parents(".relationLayerRow").nextUntil(".relationLayerRow").hide();
        }
    });

    $.each($(".relationToggleLayer").parents(".relationLayerRow"), function(index, elem){
        if (!$(elem).nextUntil(".relationLayerRow").length){
            $(elem).find(".relationToggleLayer").removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-close").css("opacity","0.5").unbind("click");
        }
    });
}
