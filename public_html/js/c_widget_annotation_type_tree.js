/**
* Zestaw metod do obsługi drzewa z typami anotacji.
* Obsługuje zwijanie, rozwijanie i zapisywanie wybranych typów w ciasteczkach. 
*/

var url = $.url(window.location.href);
var corpus_id = url.param("corpus");
var subpage = url.param("subpage");

if(subpage === "relation_agreement"){
    var cookieLayersName = "_relations_annotation_lemma_layers";
    var cookieSubsetsName = "_relations_annotation_lemma_subsets";
    var cookieTypesName = "_relations_annotation_lemma_types";
} else{
     cookieLayersName = "_annotation_lemma_layers";
     cookieSubsetsName = "_annotation_lemma_subsets";
     cookieTypesName = "_annotation_lemma_types";
}

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 */
function setupAnnotationTypeTree(){
	$(".toggleLayer").click(function(){
		if ($(this).hasClass("ui-icon-circlesmall-plus")){
			$(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
			$(this).parents(".layerRow").nextUntil(".layerRow",".sublayerRow").show();	
		} 
		else {
			$(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
			$(this).parents(".layerRow").nextUntil(".layerRow").hide();	
		}
	});
	
	$.each($(".toggleLayer").parents(".layerRow"), function(index, elem){
		if (!$(elem).nextUntil(".layerRow").length){
			$(elem).find(".toggleLayer").removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-close").css("opacity","0.5").unbind("click");//.removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
		};
	});
	
	$(".toggleSubLayer").click(function(){
		if ($(this).hasClass("ui-icon-circlesmall-plus")){
			$(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
			$(this).parents(".sublayerRow").nextUntil(".sublayerRow, .layerRow").show();	
		} 
		else {
			$(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
			$(this).parents(".sublayerRow").nextUntil(".sublayerRow, .layerRow").hide();	
		}
	});

	$("input[type=checkbox].group_cb").click(function(){
		var checked = $(this).is(":checked");
		$(this).closest("tr").nextUntil(".layerRow").each(function(){
            $(this).find("input[type=checkbox]").prop("checked", checked);
		});
	});

    $("input[type=checkbox].subset_cb").click(function(){
        var checked = $(this).is(":checked");
        $(this).closest("tr").nextUntil(".layerRow, .sublayerRow").each(function(){
            $(this).find("input[type=checkbox]").prop("checked", checked);
        });
    });

	var ann_layers = $.cookie(corpus_id + cookieLayersName);
	ann_layers = ann_layers == null ? [] : ann_layers.split(",");
	
	var ann_subsets = $.cookie(corpus_id + cookieSubsetsName);
	ann_subsets = ann_subsets == null ? [] : ann_subsets.split(",");
	
	var ann_types = $.cookie(corpus_id + cookieTypesName);
	ann_types = ann_types == null ? [] : ann_types.split(",");

	if(ann_layers){
		$.each(ann_layers, function(i,e){
			var checkbox = $("input[name=layerId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true);
		});
	}

	if(ann_subsets){
		$.each(ann_subsets, function(i,e){
			var checkbox = $("input[name=subsetId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true)
			//unfoldLayer(checkbox);
		});
	}

	if(ann_types){
		$.each(ann_types, function(i,e){
			var checkbox = $("input[name=typeId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true)
			//var subset_cb = unfoldSubset(checkbox)
			//unfoldLayer(subset_cb);
		});
	}
	
	annotationTypeTreeUpdateCounts();
}

/**
 * Store the current selection of annotation types to the cookie.
 * The list of selected annotation types is stored as a variable named [CORPUS_ID]_annotation_lemma_types,
 * where [CORPUS_ID] is the identifier of the current corpus.
 *
 * @param on_apply Feedback function called after storing the selection to the cookie.
 * 					Signature: on_apply(ann_layers, ann_subsets, ann_types)
 */
function applyAnnotationTypeTree(on_apply){
	
	/* Zapisz zaznaczone warstwy do ciasteczka */
	var ann_layers = new Array();
	$("input[type=checkbox].group_cb").each(function(i,checkbox){
		if($(checkbox).prop("checked")){
			ann_layers.push($(checkbox).prop("name").split("-")[1]);
		}				
	});		
	$.cookie(corpus_id + cookieLayersName, ann_layers);

	/* Zapisz zaznaczone zbiory do ciasteczka */
	var ann_subsets = new Array();
	$("input[type=checkbox].subset_cb").each(function(i,checkbox){
		if($(checkbox).prop("checked")){
			ann_subsets.push($(checkbox).prop("name").split("-")[1]);
		}				
	});		
	$.cookie(corpus_id + cookieSubsetsName, ann_subsets);

	/* Zapisz zaznaczone typy anotacji do ciasteczka */
	var ann_types = new Array();
	$("input[type=checkbox].type_cb").each(function(i,checkbox){
		if($(checkbox).prop("checked")){
			ann_types.push($(checkbox).prop("name").split("-")[1]);
		}				
	});		
	$.cookie(corpus_id + cookieTypesName, ann_types);

	on_apply(ann_layers, ann_subsets, ann_types);
};		

/**
 * 
 * @param checkbox
 * @returns
 */
function unfoldLayer(checkbox){
	if(!checkbox) return;
	var parent = $(checkbox).parents("tr:first");
	var unfoldBtn = $(parent).prev("tr.layerRow").find(".toggleLayer")
	if($(unfoldBtn).hasClass("ui-icon-circlesmall-plus") ){
		$(unfoldBtn).click()
	}
}

/**
 * 
 * @param checkbox
 * @returns
 */
function unfoldSubset(checkbox){
	if(!checkbox) return;
	var parent = $(checkbox).parents("tr:first");
	var unfoldBtn = $(parent).prev("tr.sublayerRow").find(".toggleSubLayer")
	if($(unfoldBtn).hasClass("ui-icon-circlesmall-plus") ){
		$(unfoldBtn).click()
	}
	return $(parent).prev("tr.sublayerRow").find("input[type='checkbox']");
}

/**
 * Uaktualnia liczności zaznaczonych anotacji dla warst i grup.
 * @returns
 */
function annotationTypeTreeUpdateCounts(){
	var lastLayerCount = 0;
	var lastLayerSelected = 0;
	var lastLayer = null;
	var lastSubsetCount = 0;
	var lastSubsetSelected = 0;
	var lastSubset = null;
	$("#annotation_layers tr").each(function(){
		if ( $(this).hasClass("layerRow") ){
			lastLayerCount = 0;
			lastLayerSelected = 0;
			lastLayer = $(this).find("span.count");
		}
		else if ( $(this).hasClass("sublayerRow") ){
			lastSubsetCount = 0;
			lastSubsetSelected = 0;
			lastSubset = $(this).find("span.count");
		}
		else if ( $(this).hasClass("typelayerRow") ){
			lastLayerCount += 1;
			lastSubsetCount += 1;
			if ( $(this).find("input[type=checkbox]").attr("checked") ){
				lastLayerSelected += 1;
				lastSubsetSelected += 1
			}
			if ( lastLayer != null ){
				lastLayer.html((lastLayerSelected > 0 ? "<b>" + lastLayerSelected + "</b>" : "0") + " (" + lastLayerCount + ")");
			}
			if ( lastSubset != null ){
				lastSubset.html((lastSubsetSelected > 0 ? "<b>" + lastSubsetSelected + "</b>" : "0") + " (" + lastSubsetCount + ")");
			}
		}
	});	
}