/**
* Zestaw metod do obsługi drzewa z typami anotacji.
* Obsługuje zwijanie, rozwijanie i zapisywanie wybranych typów w ciasteczkach. 
*/

var cookieLayersName = "_annotation_lemma_layers"
var cookieSubsetsName = "_annotation_lemma_subsets"
var cookieTypesName = "_annotation_lemma_types"
var url = $.url(window.location.href);
var corpus_id = url.param("corpus");

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
		$(this).parents(".layerRow").nextUntil(".layerRow").find("input[type=checkbox].subset_cb, input[type=checkbox].type_cb").attr("checked", $(this).attr("checked"));
	});
	
	$("input[type=checkbox].subset_cb").click(function(){
		$(this).parents(".sublayerRow").nextUntil(".layerRow, .sublayerRow").find("input[type=checkbox].type_cb").attr("checked", $(this).attr("checked"));
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
}

/**
 * 
 * @param on_apply Funkcja wywoływana w momencie kliknięcia w przycisk Apply. Sygnatura funkcji:
 * 			on_apply(ann_layers, ann_subsets, ann_types)
 * @returns
 */
function applyAnnotationTypeTree(on_apply){
	
	/* Zapisz zaznaczone warstwy do ciasteczka */
	var ann_layers = new Array();
	$("input[type=checkbox].group_cb").each(function(i,checkbox){
		if($(checkbox).attr("checked")){
			ann_layers.push($(checkbox).attr("name").split("-")[1]);
		}				
	});		
	$.cookie(corpus_id + cookieLayersName, ann_layers);

	/* Zapisz zaznaczone zbiory do ciasteczka */
	var ann_subsets = new Array();
	$("input[type=checkbox].subset_cb").each(function(i,checkbox){
		if($(checkbox).attr("checked")){
			ann_subsets.push($(checkbox).attr("name").split("-")[1]);
		}				
	});		
	$.cookie(corpus_id + cookieSubsetsName, ann_subsets);

	/* Zapisz zaznaczone typy anotacji do ciasteczka */
	var ann_types = new Array();
	$("input[type=checkbox].type_cb").each(function(i,checkbox){
		if($(checkbox).attr("checked")){
			ann_types.push($(checkbox).attr("name").split("-")[1]);
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