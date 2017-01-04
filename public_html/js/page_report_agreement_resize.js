/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
	fit_to_screen();
	$(window).resize(function() {
		fit_to_screen();
	});
});


/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function fit_to_screen(){
	$("#agreement").hide();
	$("#content").hide();
	$("#flagsContainer").hide();
	$("#rightPanelAccordion").hide();
	var html_height = $("body").outerHeight();
	var window_height = $(window).height();
	
	var height = window_height - html_height - 20;
	if ( height < 0 ){
		height = 200;
	}
	
	$("#agreement").css("height", height + "px");
	$("#content").css("height", (height+48) + "px");
	$("#annotation_layers").css("height", (height+25) + "px");
	
	$("#agreement").show();
	$("#content").show();
	$("#flagsContainer").show();
	$("#rightPanelAccordion").show();
}