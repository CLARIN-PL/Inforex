/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){
    assign_click_legend();
	assign_annotation_triggers();
	assign_more_less();
	setupAnnotationTypeTree();
	setupUserSelectionAB("annotations");
    showContent();
    $("#apply").click(function(){
		applyAnnotationTypeTree(function(ann_layers, ann_subsets, ann_types){});
	});
});

/**
 * Removes the loading wheel and shows the table content.
 */
function showContent(){
    $(".annotation_loading_wheel").hide();
    $("#agreement").show();
    $(".errors_button").prop("disabled", false);
    $(".submit_button").prop("disabled", false);
    autoreizeFitToScreen();
}

/**
 * Przypisuje obsługę kliknięcia w pozycje legendy.
 **/
function assign_click_legend(){
	$(".legend a").click(function(){
		var cl = $(this).parent().attr('class');
		$("#agreement table tbody tr").each(function(){
			if ( cl!="all" && $(this).children("td." + cl).size() == 0 ){
				$(this).hide();
			}
			else{
				$(this).show();
			}
		});
	});
}

/**
 * 
 * @returns
 */
function assign_annotation_triggers(){
	$("#agreement table tr").mouseenter(function(){
		highlight_text(
				parseInt($(this).children("td.from").text()),
				parseInt($(this).children("td.to").text()), "highlight");
	});
	$("#agreement table tr").click(function(){
		$("#agreement table tr.selected").removeClass("selected");
		$(this).addClass("selected");
		highlight_text(
				parseInt($(this).children("td.from").text()),
				parseInt($(this).children("td.to").text()), "selected");
	});
}

/**
 * 
 * @returns
 */
function assign_more_less(){
	$(".agreement_actions li").hide();
	$(".agreement_actions li input[type=radio]").hide();
    $(".agreement_actions li .annotation_checkbox").hide();
    $(".agreement_actions li input:checked").parent("li").show();
	$(".agreement_actions .toggle a").click(function(event){
		event.stopPropagation();
		var mode = $(this).text();		
		$(this).text(mode == "more" ? "less" : "more");
		var td = $(this).closest("td");
		if ( mode == "more" ){
			td.find("li").show();
			td.find("li input[type=radio]").show();
            td.find("li .annotation_checkbox").show();
        }
		else{
			td.find("li").hide();
			td.find("li input[type=radio]").hide();
            td.find("li .annotation_checkbox").hide();
            td.find("li input:checked").parent("li").show();
		}
	});
}

/**
 * 
 * @param begin
 * @param end
 */
function highlight_text(begin, end, cl){
	$("#content span." + cl).removeClass(cl);
	for ( i=begin; i<=end; i++){
		$("#content span.token"+i).addClass(cl);
	}
}
