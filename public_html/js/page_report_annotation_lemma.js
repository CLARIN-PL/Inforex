/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

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
		
		var ann_types = new Array();
		$("input[type=checkbox].type_cb").each(function(i,checkbox){
			if($(checkbox).attr("checked")){
				ann_types.push($(checkbox).attr("name").split("-")[1]);
			}
				
		});
		
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
				var annotation_table = "<table style='width:100%'>";
				$.each(sentence_annotations, function(ann_index, annotation){
					annotation_table += getAnnotationRow(annotation);
				});
				annotation_table += "</table>";
				//if(annotation_table != "<table></table>"){
				$(sentences[sentence_index]).append(annotation_table);
				//}
			});
			
			
			
		};
		
		var error = function(){
			
		};
		
		var complete = function(){
			
		};
		
		var loaderElement = $(this).parent().parent().prev().find("th").first();
		
		
		doAjax("annotation_lemma_get", params, success, error, complete, loaderElement);
	});
	
	// SAVE BUTTON
	$("input[type=button].lemma_save").live('click', function(){
		if(!$(lemmaInput).hasClass("saved")){
			saveAnnotationLemma(lemmaInput);
		}
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
	
	
	
	// DELETE BUTTON
	$("input[type=button].lemma_delete").live('click', function(){
		
		var lemmaInput = $(this).parent().prev().find("input").get(0);
		
		var params = {
			annotation_lemma_id: $(lemmaInput).attr("name")
		};
		
		var success = function(data){
			$(lemmaInput).val("");
		};
		
		var loaderElement = $(this).parent();
		
		doAjax("annotation_lemma_delete", params, success, null, null,  loaderElement);
	});
	
});

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
		annotation_lemma_id: $(lemmaInput).attr("name"),
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
	$($(input).parent().parent().next().find("td").get(1)).html("Status: <span style='color:"+color+";'>"+status+"<span>");
}

function getAnnotationRow(annotation){
	var row = "<tr>";
	row += "<td style='width:35%;text-align:right;'><span style='"+annotation.css+"'>"+annotation.text+"</span></td>";
	row += "<td style='width:50%'><input class='lemma_text' type='text' style='width:100%;' name='"+annotation.id+"' value='"+(annotation.lemma?annotation.lemma:"")+"'/></td>";
	row += "<td><input type='button' class='lemma_save' value='='/><input type='button' value='X' class='lemma_delete' style='color:#FF0000' /></td>";
	row += "</td></tr>";
	row += "<tr><td></td><td class='lemma_status'></td><td></td></tr>";
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
