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
	$(".panel-results > div.panel-body").hide();
	var panel_height = $(window).height() - $("body").outerHeight(true) -5;
	$(".panel-results > div.panel-body").css("height", panel_height + "px");
	$(".panel-results > div.panel-body").show();	
}