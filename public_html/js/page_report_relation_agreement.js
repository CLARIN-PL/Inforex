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
    setupRelationTypeTree();
	setupUserSelectionAB('relations');
	
	$("#apply").click(function(){
		applyRelationTypeTree(function(rel_layers, rel_types){});
        applyAnnotationTypeTree(function(ann_layers, ann_subsets, ann_types){});
    });
});

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
	$("#agreement table tbody tr").mouseenter(function(){
	    var ranges = getRange(this);
		highlight_text(ranges.source_from, ranges.source_to, ranges.target_from, ranges.target_to, 'highlight');
	});
	$("#agreement table tbody tr").click(function(){
		$("#agreement table tr.selected").removeClass("selected");
		$(this).addClass("selected");
        var ranges = getRange(this);
        highlight_text(ranges.source_from, ranges.source_to, ranges.target_from, ranges.target_to, 'selected');

    });
}

function getRange(tr){
    var ranges = ($(tr).attr('class')).split("/");
    var ranges_source = ranges[0].split("_");
    var ranges_target = ranges[1].split("_");

    var range_array = {
        source_from: ranges_source[0],
        source_to: ranges_source[1],
        target_from: ranges_target[0],
        target_to: ranges_target[1],
    };

    return range_array;
}

/**
 * 
 * @returns
 */
function assign_more_less(){
	$(".agreement_actions li").hide();
	$(".agreement_actions li input[type=radio]").hide();
	$(".agreement_actions li input:checked").parent("li").show();
	$(".agreement_actions .toggle a").click(function(event){
		event.stopPropagation();
		var mode = $(this).text();		
		$(this).text(mode == "more" ? "less" : "more");
		var td = $(this).closest("td");
		if ( mode == "more" ){
			td.find("li").show();
			td.find("li input[type=radio]").show();
		}
		else{
			td.find("li").hide();
			td.find("li input[type=radio]").hide();
			td.find("li input:checked").parent("li").show();			
		}
	});
}

/**
 *
 * @param source_begin
 * @param source_end
 * @param target_begin
 * @param target_end
 * @param cl - type of selection, either "highlight" or "selected"
 */
function highlight_text(source_begin, source_end, target_begin, target_end, cl){
	$("#content span." + cl).removeClass(cl);
	for(var i = parseInt(source_begin); i<=parseInt(source_end); i++){
		$("#content span.token"+i).addClass(cl);
	}

    for(var i = parseInt(target_begin); i<=parseInt(target_end); i++){
        $("#content span.token"+i).addClass(cl);
    }
}
