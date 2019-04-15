/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */
$(document).ready(function(){

	setupAnnotationTypeTree();
	setupAnnotationMode();

    $("#apply").click(function(e){
    	// Store the selection of annotation types, sets and subsets to the cookie
        applyAnnotationTypeTree(function(ann_layers, ann_subsets, ann_types){});
    });

    $("div.content.annotations span").each(function(){
		if ( !$(this).hasClass("token") ){
			var id = $(this).attr("id").replace("an", "");
			var orth = $(this).text();
            var classes = $(this).attr("class");
			var lemma = $(this).attr("lemma");
            var html = "<tr>";
            html += "<td><span class='" + classes + "'>"+orth+"</span></td>";
            html += "<td><input class='lemma_text tip form-control input-sm' type='text' name='"+id+"' value='"+(lemma?lemma:"")+"' lemma='" + lemma + "' ></td>";
            html += "<td class='lemma_status'></td>";
            html += "<td class='lemma_actions'>" +
                '<a href="#" class="lemma_copy"><i class="fa fa-clone" aria-hidden="true"></i></a>' +
                '<a href="#" class="lemma_clear"><i class="fa fa-trash" aria-hidden="true"></i></a>' +
                "</td>";
            html += "</tr>";
            $("#annotationLemmas tbody").append(html);
		}
	});

    // Update lemmas actions
    $("#annotationLemmas .lemma_text").each(function(key, input){
    	updateStatus(input);
	});

	// Copy button click
	$(".lemma_copy").on('click', function(){
		var lemmaInput = $(this).closest("tr").find("input").get(0);
		var text = $(this).closest("tr").find("span:first").text();
		$(lemmaInput).val(text);
		saveAnnotationLemma(lemmaInput);
        $(lemmaInput).focus();
	});

    // Clear button click
    $(".lemma_clear").on('click', function(){
        var lemmaInput = $(this).closest("tr").find("input").get(0);
		$(lemmaInput).val("");
        saveAnnotationLemma(lemmaInput);
        $(lemmaInput).focus();
    });

	// Input field on events
	$("input.lemma_text").on({
		"change": function(){ updateStatus(this);},
		"blur"	: function(){ updateStatus(this);},
        "keydown" : function(e){
            if ( e.ctrlKey && e.which == 32 ) {
                $(this).closest("tr").find(".lemma_copy").click();
            }
        },
		"keyup"	: function(e){
            if(e.which == 13) {
                saveAnnotationLemma($(this));
                gotoNext($(this));
            } else if (e.which == 40 ){
                gotoNext($(this));
            } else if (e.which == 38 ){
                gotoPrev($(this));
            } else if ( !(e.ctrlKey && e.which == 32) ) {
                updateStatus($(this));
            }
        },
		"focus": function(){
            $(this).select();
            var id = $(this).attr("name");
            $("span.selected").removeClass("selected");
            $("#an" + id).addClass("selected");
        }
	});

    $("#annotationLemmas .lemma_text:first").focus();
});

/**
 * Move the focus from the given input to the following input field.
 * @param input A reference to an input.lemma_text element.
 */
function gotoNext(input){
	var inputs = $('#annotationLemmas input.lemma_text');
	var currentIndex = inputs.index(input);
	if ( currentIndex + 1 < inputs.size() ){
		inputs.eq(currentIndex + 1).focus();
	}
}

/**
 * Move the focus from the given input to the preceding input field.
 * @param input A reference to an input.lemma_text element.
 */
function gotoPrev(input){
    var inputs = $('#annotationLemmas input.lemma_text');
    var currentIndex = inputs.index(input);
    if ( currentIndex > 0 ){
        inputs.eq( currentIndex - 1 ).focus();
	}
}

/**
 * Save the lemma for given input.
 * @param input A reference to an input.lemma_text element.
 */
function saveAnnotationLemma(input){
    var lemma = $(input).attr("lemma");
    var currentInput = $(input).val();
	if ( lemma != currentInput ) {
        var text = $(input).val();
        var params = {
            annotation_id: $(input).attr("name"),
            annotation_lemma_text: text
        };
        var success = function (data) {
            setStatus(input, "saved", "#00aa33");
            $(input).attr("lemma", text);
        };
        var loaderElement = $(this).parent();
        doAjax("annotation_lemma_save", params, success, null, null, loaderElement);
    }
}

/**
 * Update status for the given input.
 * @param input A reference to an input.lemma_text element.
 */
function updateStatus(input){
	var lemma = $(input).attr("lemma");
	var currentInput = $(input).val();
	var status = $(input).closest("tr").find(".lemma_status").text();
	if ( lemma == currentInput ){
	    if ( status != "saved" ) {
            setStatus(input, "no change", "#999");
        }
	} else {
        setStatus(input, "changed", "#aa0000");
	}
}

/**
 * Set status for the given input.
 * @param input A reference to an input.lemma_text element.
 * @param status Name of the new status.
 * @param color Text color for the status.
 */
function setStatus(input, status, color){
	$(input).closest("tr").find(".lemma_status").html("<em style='color:"+color+"'>"+status+"<em>");
}
