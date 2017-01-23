/**
* Zestaw metod do obsługi drzewa z typami anotacji.
* Obsługuje zwijanie, rozwijanie i zapisywanie wybranych typów w ciasteczkach. 
*/

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 */
$(function(){
	$(".toggleLayer").live("click", function(){
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
	
	$("input[type=checkbox].group_cb").click(function(){
		$(this).parents(".layerRow").nextUntil(".layerRow").find("input[type=checkbox].subset_cb, input[type=checkbox].type_cb").attr("checked", $(this).attr("checked"));
	});
	
	$("input[type=checkbox].subset_cb").click(function(){
		$(this).parents(".sublayerRow").nextUntil(".layerRow, .sublayerRow").find("input[type=checkbox].type_cb").attr("checked", $(this).attr("checked"));
	});
	
	annotationTypeTreeUpdateCounts();
});

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