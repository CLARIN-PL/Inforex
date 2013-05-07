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
	var other_content_height = $("body").outerHeight() - $("#ner-text").height()+4;
	var panel_height = $(window).height() - other_content_height;
	
	//$("#list_of_topics").css("height", panel_height + "px");
	$("#ner-text").css("height", panel_height + "px");
	$("#ner-html").css("height", panel_height + "px");
	$("#ner-annotations").css("height", panel_height + "px");
}