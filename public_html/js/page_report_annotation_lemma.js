/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */
$(document).ready(function(){

	splitSentences();
	
	setupAnnotationTypeTree(function(ann_layers, ann_subsets, ann_types){
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
				$(sentences[sentence_index]).append(annotation_table);
			});
			
			$(".tip").tooltip();			
		};
			
		var error = function(){};			
		var complete = function(){};
		var loaderElement = $(this).parent().parent().prev().find("th").first();
						
		doAjax("annotations_lemmas_get", params, success, error, complete, loaderElement);		
	});
	setupAnnotationTypeApply();
	
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
		
});

function gotoNext(input){
	var inputs = $('input.lemma_text');
	try{
		inputs.eq( inputs.index(input)+ 1 ).focus();
	}
	catch(e){
		
	}
}

function saveAnnotationLemma(lemmaInput){
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
