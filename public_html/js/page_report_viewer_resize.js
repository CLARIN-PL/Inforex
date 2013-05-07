/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
	fit_transcriber_to_screen();	
	
	$(window).resize(function(){
		fit_transcriber_to_screen();
	});	
});

/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function fit_transcriber_to_screen(){	
	$('.scrolling-pane').hide();
	var other_content_height = $("#page").outerHeight() + 40;
	var panel_height = $(window).height() - other_content_height;
	$("#edit_content .CodeMirror-wrapping").css("height", panel_height + "px");
	$("#leftContent").css("height", panel_height + "px");
	$('.scrolling-pane').show();
}