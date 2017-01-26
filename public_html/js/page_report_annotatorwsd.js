/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

// Globalne zmienne
var current_annotation_id = null;
var wsd_loading = false;
// ----------------

$(function(){

	$("#content span").click(function(){
		if ( !wsd_loading ){
			$("#content span.selected").removeClass("selected");
			var id = $(this).attr("id").substr(2);
			$(this).addClass("selected");
			wsd_load_panel(id);
		}
	});

	$("#wsd_senses a").live("click", function(){
		$("#content span.selected").removeClass("selected");
		$("#wsd_senses").html("<img src='gfx/ajax.gif'/> zapisuje ...");
		var value = $(this).text();		
		var annotation_id = current_annotation_id;
		current_annotation_id = null;
		
		var params = {
			annotation_id: annotation_id,
			value: value
		};
		
		var success = function(data){
			$("#wsd_senses").html("Zapisano");
		    wsd_loading = false;
		};
		
		var error = function(){
			$("#wsd_senses").html("Nie zapisano");
			wsd_loading = false;
		}
		
		doAjax("report_update_annotation_wsd", params, success, error);
	});
	
	wsd_mark_selected_words();
	wsd_edit_default();
	
});


/**
 * Wczytuje panel do wyboru sensu jednostki. 
 * annotation_id -- identyfikator jednostki
 */
function wsd_load_panel(annotation_id){
	
	wsd_loading = true;
	current_annotation_id = annotation_id;
	$("#wsd_senses").html("<img src='gfx/ajax.gif'/> wczytuje dane ...");
	
	var params = {
		annotation_id: annotation_id	
	};
	
	var success = function(data){
		var html = "";
		for (a in data.values){
			v = data.values[a];
			if ( v.value == data.value )
				html += "<li><a href='#' style='color: navy' class='hightlighted'>" + v.value + "</a><br/>";
			else
				html += "<li><a href='#' style='color: navy'>" + v.value + "</a><br/>";
			html += "<small>" + v.description + "</small></li>";
		}
		$("#wsd_senses").html("<ul>"+html+"</ul>");
		    wsd_loading = false;
	};
	
	var error = function(){
		wsd_loading = false;
	};
	
	doAjax("report_get_annotation_wsd", params, success, error);		
}

/**
 * Zaznacz wybrane słowa.
 * Podświetlone zostają wszystkie słowa, których klasa jest równa wartości ukrytego pola "wsd_word".
 */
function wsd_mark_selected_words(){
	var wsd_selected = $("input[name=wsd_word]").attr("value");
	if (wsd_selected)
		$("."+wsd_selected).addClass("marked");	
}

/**
 * Ustawia edycję wskazanego słowa.
 */
function wsd_edit_default(){
	var wsd_selected = $("input[name=wsd_edit]").attr("value");
	$("#an"+wsd_selected).click();		
}