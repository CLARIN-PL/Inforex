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

var page = url.param("page");

if(page === "relation_agreement_check"){
   cookieLayersName = "_ann_set_relation_agreement_check";
   cookieSubsetsName = "_ann_subset_relation_agreement_check";
   cookieTypesName = "_ann_type_relation_agreement_check";
}

var annotationTypeTreeSelection = {
	layers: {},
	subsets: {},
	types: {}
};

function readAnnotationTypeTreeCookie(name){
	var raw = $.cookie(corpus_id + name);
	if (raw === null || raw === "") {
		return [];
	}
	return $.grep(raw.split(","), function(value){
		return value !== "";
	});
}

function refreshAnnotationTypeTreeSelection(){
	annotationTypeTreeSelection.layers = {};
	annotationTypeTreeSelection.subsets = {};
	annotationTypeTreeSelection.types = {};

	$.each(readAnnotationTypeTreeCookie(cookieLayersName), function(i, id){
		annotationTypeTreeSelection.layers[String(parseInt(id, 10))] = true;
	});
	$.each(readAnnotationTypeTreeCookie(cookieSubsetsName), function(i, id){
		annotationTypeTreeSelection.subsets[String(parseInt(id, 10))] = true;
	});
	$.each(readAnnotationTypeTreeCookie(cookieTypesName), function(i, id){
		annotationTypeTreeSelection.types[String(parseInt(id, 10))] = true;
	});
}

function annotationTypeTreeSelectionToArray(selectionType){
	var ids = [];
	$.each(annotationTypeTreeSelection[selectionType], function(id, isSelected){
		if (isSelected) {
			ids.push(id);
		}
	});
	return ids;
}

function getAnnotationTypeTreeSubsetTypeIds(setId, subsetId){
	var ids = [];
	var setData = typeof getAnnotationTypeTreeSetData === "function" ? getAnnotationTypeTreeSetData(setId) : null;
	var subsetData = setData && setData[subsetId] ? setData[subsetId] : null;

	if (!subsetData) {
		return ids;
	}

	$.each(subsetData, function(typeId, typeName){
		if (typeId !== "name" && typeName !== "...") {
			ids.push(String(typeId));
		}
	});
	return ids;
}

function getAnnotationTypeTreeSetTypeIds(setId){
	var ids = [];
	var setData = typeof getAnnotationTypeTreeSetData === "function" ? getAnnotationTypeTreeSetData(setId) : null;

	if (!setData) {
		return ids;
	}

	$.each(setData, function(subsetId, subsetData){
		if (subsetId === "name") {
			return;
		}
		ids = ids.concat(getAnnotationTypeTreeSubsetTypeIds(setId, subsetId));
	});
	return ids;
}

function setAnnotationTypeTreeSelectionValues(selectionType, ids, checked){
	$.each(ids, function(i, id){
		var key = String(id);
		if (checked) {
			annotationTypeTreeSelection[selectionType][key] = true;
		} else {
			delete annotationTypeTreeSelection[selectionType][key];
		}
	});
}

/**
 * Ustawia zdarzenia zwijania, rozwijania i klikania w checkboxy.
 */
function setupAnnotationTypeTree(){
	var tree = $("#annotation_layers");
	refreshAnnotationTypeTreeSelection();

	tree.off("click.annotationTypeTree", ".toggleLayer").on("click.annotationTypeTree", ".toggleLayer", function(){
		var layerRow = $(this).parents(".layerRow");
		if ($(this).hasClass("ui-icon-circlesmall-plus")){
			if (layerRow.attr("data-children-loaded") !== "1"
				&& typeof renderAnnotationTypeTreeSetChildren === "function"){
				renderAnnotationTypeTreeSetChildren(layerRow.get(0));
			}
			$(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
			layerRow.nextUntil(".layerRow").show();
		}
		else {
			$(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
			layerRow.nextUntil(".layerRow").hide();
		}
	});
	
	tree.off("click.annotationTypeTree", ".toggleSubLayer").on("click.annotationTypeTree", ".toggleSubLayer", function(){
		var subsetRow = $(this).parents(".sublayerRow");
		if ($(this).hasClass("ui-icon-circlesmall-plus")){
			if (subsetRow.attr("data-children-loaded") !== "1"
				&& typeof renderAnnotationTypeTreeSubsetChildren === "function"){
				renderAnnotationTypeTreeSubsetChildren(subsetRow.get(0));
			}
			$(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
			subsetRow.nextUntil(".sublayerRow, .layerRow").show();
		}
		else {
			$(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
			subsetRow.nextUntil(".sublayerRow, .layerRow").hide();
		}
	});

	tree.off("click.annotationTypeTree", "input[type=checkbox].group_cb").on("click.annotationTypeTree", "input[type=checkbox].group_cb", function(){
		var checked = $(this).is(":checked");
		var setId = $(this).prop("name").split("-")[1];
		if ($(this).closest("tr").attr("data-children-loaded") !== "1"
			&& typeof renderAnnotationTypeTreeSetChildren === "function"){
			renderAnnotationTypeTreeSetChildren($(this).closest("tr").get(0));
		}
		$(this).closest("tr").nextUntil(".layerRow").each(function(){
            $(this).find("input[type=checkbox]").prop("checked", checked);
		});
		setAnnotationTypeTreeSelectionValues("layers", [setId], checked);
		setAnnotationTypeTreeSelectionValues("subsets", $.map($(this).closest("tr").nextUntil(".layerRow", ".sublayerRow"), function(row){
			return $(row).attr("subsetid");
		}), checked);
		setAnnotationTypeTreeSelectionValues("types", getAnnotationTypeTreeSetTypeIds(setId), checked);
		annotationTypeTreeUpdateCounts();
	});

    tree.off("click.annotationTypeTree", "input[type=checkbox].subset_cb").on("click.annotationTypeTree", "input[type=checkbox].subset_cb", function(){
        var checked = $(this).is(":checked");
        var subsetId = $(this).prop("name").split("-")[1];
        var subsetRow = $(this).closest("tr");
        var setId = subsetRow.prevAll(".layerRow:first").attr("setid");
        if (subsetRow.attr("data-children-loaded") !== "1"
            && typeof renderAnnotationTypeTreeSubsetChildren === "function"){
            renderAnnotationTypeTreeSubsetChildren(subsetRow.get(0));
        }
        $(this).closest("tr").nextUntil(".layerRow, .sublayerRow").each(function(){
            $(this).find("input[type=checkbox]").prop("checked", checked);
        });
        setAnnotationTypeTreeSelectionValues("subsets", [subsetId], checked);
        setAnnotationTypeTreeSelectionValues("types", getAnnotationTypeTreeSubsetTypeIds(setId, subsetId), checked);
        annotationTypeTreeUpdateCounts();
    });

	tree.off("click.annotationTypeTree", "input[type=checkbox].type_cb").on("click.annotationTypeTree", "input[type=checkbox].type_cb", function(){
		var checked = $(this).is(":checked");
		var typeId = $(this).prop("name").split("-")[1];
		setAnnotationTypeTreeSelectionValues("types", [typeId], checked);
		annotationTypeTreeUpdateCounts();
	});

	$.each(annotationTypeTreeSelection.layers, function(id, isSelected){
		var checkbox = $("input[name=layerId-"+id+"]");
		$(checkbox).prop("checked", isSelected);
	});
	
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
	var ann_layers = annotationTypeTreeSelectionToArray("layers");
	$.cookie(corpus_id + cookieLayersName, ann_layers);

	var ann_subsets = annotationTypeTreeSelectionToArray("subsets");
	$.cookie(corpus_id + cookieSubsetsName, ann_subsets);

	var ann_types = annotationTypeTreeSelectionToArray("types");
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
			if ( $(this).find("input[type=checkbox]").prop("checked") ){
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
