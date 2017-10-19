/**
* Zestaw metod do obsługi drzewa z typami relacji.
* Obsługuje zwijanie, rozwijanie i zapisywanie wybranych typów w ciasteczkach.
*/

var cookieRelationLayersName = "_relation_lemma_layers";
var cookieRelationTypesName = "_relation_lemma_types";
var url = $.url(window.location.href);
var corpus_id = url.param("corpus");

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 */
function setupRelationTypeTree(){
	$(".relationToggleLayer").click(function(){
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

	$("input[type=checkbox].relation_group_cb").click(function(){
		var checked = $(this).is(":checked");
		$(this).closest("tr").nextUntil(".relationLayerRow").each(function(){
            $(this).find("input[type=checkbox]").prop("checked", checked);
		});
	});

    $("input[type=checkbox].relation_type_cb").click(function(){
        var checked = $(this).is(":checked");
        var checked_siblings = 0;
        var relation_set_id = $(this).closest("tr").attr("relation_set_id");
        var layer_checkbox = $("#relation_set_"+relation_set_id).find("input[type=checkbox]");

        $("#set_"+relation_set_id).nextUntil(".relationLayerRow").each(function(){
            if($(this).find("input[type=checkbox]").is(":checked")){
                checked_siblings += 1;
            }
        });

        if(checked_siblings > 0 || checked){
            layer_checkbox.prop("checked", true);
        } else{
            layer_checkbox.prop("checked", false);
        }
    });

	var rel_layers = $.cookie(corpus_id + cookieRelationLayersName);
	rel_layers = rel_layers === null ? [] : rel_layers.split(",");

	var rel_types = $.cookie(corpus_id + cookieRelationTypesName);
	rel_types = rel_types === null ? [] : rel_types.split(",");

	if(rel_layers){
		$.each(rel_layers, function(i,e){
			var checkbox = $("input[name=relationLayerId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true);
		});
	}

	if(rel_types){
		$.each(rel_types, function(i,e){
			var checkbox = $("input[name=relationTypeId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true)
			//var subset_cb = unfoldSubset(checkbox)
			//unfoldLayer(subset_cb);
		});
	}

	//relationTypeTreeUpdateCounts();
}

/**
 * Store the current selection of relation types to the cookie.
 * The list of selected relation types is stored as a variable named [CORPUS_ID]_relation_lemma_types,
 * where [CORPUS_ID] is the identifier of the current corpus.
 *
 * @param on_apply Feedback function called after storing the selection to the cookie.
 * 					Signature: on_apply(ann_layers, ann_types)
 */
function applyRelationTypeTree(on_apply){

	/* Zapisz zaznaczone warstwy do ciasteczka */
	var rel_layers = new Array();
	$("input[type=checkbox].relation_group_cb").each(function(i,checkbox){
		if($(checkbox).prop("checked")){
			rel_layers.push($(checkbox).prop("name").split("-")[1]);
		}
	});
	$.cookie(corpus_id + cookieRelationLayersName, rel_layers);

	/* Zapisz zaznaczone typy anotacji do ciasteczka */
	var rel_types = new Array();
	$("input[type=checkbox].relation_type_cb").each(function(i,checkbox){
		if($(checkbox).prop("checked")){
			rel_types.push($(checkbox).prop("name").split("-")[1]);
		}
	});
	$.cookie(corpus_id + cookieRelationTypesName, rel_types);

	console.log("On apply rel types");
	console.log(rel_types);

	on_apply(rel_layers, rel_types);
}