/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var url = $.url(window.location.href);
var corpus_id = url.param("corpus");

var cookieLayersName = "_annotation_lemma_layers"
var cookieSubsetsName = "_annotation_lemma_subsets"
var cookieTypesName = "_annotation_lemma_types"


$(document).ready(function(){

	splitSentences();

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
	
	// APPLY BUTTON
	$("#applyLayer").click(function(){
		
		var ann_layers = new Array();
		$("input[type=checkbox].group_cb").each(function(i,checkbox){
			if($(checkbox).attr("checked")){
				ann_layers.push($(checkbox).attr("name").split("-")[1]);
			}
				
		});
		
		$.cookie(corpus_id + cookieLayersName, ann_layers);

		var ann_subsets = new Array();
		$("input[type=checkbox].subset_cb").each(function(i,checkbox){
			if($(checkbox).attr("checked")){
				ann_subsets.push($(checkbox).attr("name").split("-")[1]);
			}
				
		});
		
		$.cookie(corpus_id + cookieSubsetsName, ann_subsets);


		var ann_types = new Array();
		$("input[type=checkbox].type_cb").each(function(i,checkbox){
			if($(checkbox).attr("checked")){
				ann_types.push($(checkbox).attr("name").split("-")[1]);
			}
				
		});
		
		$.cookie(corpus_id + cookieTypesName, ann_types);

		var params = {
			report_id: $.url(window.location.href).param('id'),
			annotation_types: ann_types
		};
		
		var success = function(data){
			var sentences = $("sentence");
			$.each(sentences, function(i,sentence){
				$(sentence).find("table").remove();
			});
			
			$.each(data, function(sentence_index, sentence_annotations){
				var annotation_table = "<table style='width:100%'><tr><td colspan=3></td><td>Status</td></tr>";
				$.each(sentence_annotations, function(ann_index, annotation){
					annotation_table += getAnnotationRow(annotation);
				});
				annotation_table += "</table>";
				//if(annotation_table != "<table></table>"){
				$(sentences[sentence_index]).append(annotation_table);
				//}
			});
			
			$(".tip").tooltip();
			
		};
		
		var error = function(){
			
		};
		
		var complete = function(){
			
		};
		
		var loaderElement = $(this).parent().parent().prev().find("th").first();
		
		
		doAjax("annotation_lemma_get", params, success, error, complete, loaderElement);
	});
	
	// COPY BUTTON
	$("input[type=button].lemma_copy").live('click', function(){
		var lemmaInput = $(this).parent().prev().find("input").get(0);
		// COPY
		var text = $(this).parent().prev().prev().find("span:first").html()
		$(lemmaInput).val(text);
		// SAVE
		saveAnnotationLemma(lemmaInput);
	});
	
	// FIELD CHANGE
	$("input.lemma_text").live("change",function(){
		$(this).removeClass("saved");
		setStatus($(this),"changed","#aa0000");
	});
	
	// FIELD UNFOCUS
	$("input.lemma_text").live("blur",function(){
		if(!$(this).hasClass("saved")){
			$(this).addClass("saved");
			saveAnnotationLemma($(this));
		}
	});
	
	// ENTER KEYPRESS
	$("input.lemma_text").live("keyup",function(e){
		if(e.which == 13) {
			if(!$(this).hasClass("saved")){
				$(this).addClass("saved");
				saveAnnotationLemma($(this));
				gotoNext($(this));
			}
	    }
		else if(e.which != 9){
			$(this).removeClass("saved");
			setStatus($(this), "changed", "#aa0000");
		}
	});
	
	
	
	// CLEAR BUTTON
	$("input[type=button].lemma_clear").live('click', function(){
		
		var lemmaInput = $(this).parent().prev().find("input").get(0);
		
		var params = {
			annotation_id: $(lemmaInput).attr("name")
		};
		
		var success = function(data){
			$(lemmaInput).val("");
			setStatus(lemmaInput,"deleted","#3300aa");
		};
		
		var loaderElement = $(this).parent();
		
		doAjax("annotation_lemma_delete", params, success, null, null,  loaderElement);
	});
	

	loadAnnotationLayers();
	
});

function unfoldLayer(checkbox){
	if(!checkbox) return;
	var parent = $(checkbox).parents("tr:first");
	var unfoldBtn = $(parent).prev("tr.layerRow").find(".toggleLayer")
	if($(unfoldBtn).hasClass("ui-icon-circlesmall-plus")  /*&& !$(unfoldBtn).hasClass("complete_selection")*/){
		$(unfoldBtn).click()
	}
}

function unfoldSubset(checkbox){
	if(!checkbox) return;
	var parent = $(checkbox).parents("tr:first");
	var unfoldBtn = $(parent).prev("tr.sublayerRow").find(".toggleSubLayer")
	if($(unfoldBtn).hasClass("ui-icon-circlesmall-plus") /*&& !$(unfoldBtn).hasClass("complete_selection")*/){
		$(unfoldBtn).click()
	}

	return $(parent).prev("tr.sublayerRow").find("input[type='checkbox']");
}

// function markFolding(checkbox){
// 	if(!checkbox) return;
// 	var parent = $(checkbox).parents("tr:first");
// 	var unfoldBtn = $(parent).find(".toggleSubLayer, .toggleLayer");
// 	$(unfoldBtn).addClass("complete_selection");
// }


function loadAnnotationLayers(){
	var ann_layers = $.cookie(corpus_id + cookieLayersName).split(",");
	var ann_subsets = $.cookie(corpus_id + cookieSubsetsName).split(",");
	var ann_types = $.cookie(corpus_id + cookieTypesName).split(",");

	console.log(ann_layers);
	console.log(ann_subsets);
	console.log(ann_types);

	if(ann_layers){
		$.each(ann_layers, function(i,e){
			var checkbox = $("input[name=layerId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true);
			//markFolding(checkbox);
		});
	}

	if(ann_subsets){
		$.each(ann_subsets, function(i,e){
			var checkbox = $("input[name=subsetId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true)
			console.log(parseInt(e))
			//markFolding(checkbox);
			unfoldLayer(checkbox);
		});
	}

	if(ann_types){
		$.each(ann_types, function(i,e){
			var checkbox = $("input[name=typeId-"+parseInt(e)+"]");
			$(checkbox).attr("checked", true)
			var subset_cb = unfoldSubset(checkbox)
			unfoldLayer(subset_cb);
		});
	}

	$("#applyLayer").click();
}

function gotoNext(input){
	var inputs = $('input.lemma_text');
	//console.log(inputs);
	try{
		inputs.eq( inputs.index(input)+ 1 ).focus();
	}
	catch(e){
		
	}
}

function saveAnnotationLemma(lemmaInput){
	//var lemmaInput = $(this).parent().prev().find("input").get(0);
	$(lemmaInput).addClass("saved");
	
	var text = $(lemmaInput).val();
	if(!text) return;
	
	var params = {
		annotation_id: $(lemmaInput).attr("name"),
		annotation_lemma_text: text 
	};
	
	var success = function(data){
		setStatus(lemmaInput,"saved","#00aa33");
	};
	
	var loaderElement = $(this).parent();
	
	doAjax("annotation_lemma_save", params, success, null, null,  loaderElement);
}

function setStatus(input, status, color){
	if(!color) color = '#000000';
	$($(input).parent().next().next()).html("<span style='color:"+color+";'>"+status+"<span>");
}

function getAnnotationRow(annotation){
	var row = "<tr>";
	row += "<td style='width:35%;text-align:right;'><span style='"+annotation.css+"'>"+annotation.text+"</span></td>";
	row += "<td style='width:50%'><input class='lemma_text tip' type='text' style='width:100%;' name='"+annotation.id+"' value='"+(annotation.lemma?annotation.lemma:"")+"' ></td>";
	row += "<td><input type='button' class='lemma_copy tip' value='=' title='Copy lemma'/><input type='button' value='X' class='lemma_clear tip' style='color:#FF0000' title='Clear lemma'/></td>";
	row += "<td class='lemma_status'></td></tr>";
	//row += "<tr><td></td><td></td></tr>";
	return row;
}

function splitSentences(){
	if($("sentence").length){
		$("sentence").before('<div class="eosSpan"><hr/></div>');
		$("sentence").addClass("lemmatize");
	}
	else{
		$("span.token.eos").each(function(){
			var $this = $(this);
			while ( $this.get(0) == $this.parent().children().last().get(0)
					&& !$this.parent().hasClass("contentBox") ){
		    	$this = $this.parent();
			}
			$this.after('<div class="eosSpan"><hr/></div>');
		});
	}
}
