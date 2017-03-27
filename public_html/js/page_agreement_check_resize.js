/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
	//fit_to_screen();
	$(window).resize(function() {
		//fit_to_screen();
	});
});


/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function fit_to_screen(){
	$("#agreement_details").hide();
	$("#agreement_summary").hide();
	$("#rightPanelAccordion").hide();
	var html_height = $("body").outerHeight();
	var window_height = $(window).height();
	
	var height = window_height - html_height - 20;
	if ( height < 0 ){
		height = 200;
	}
	
	$("#agreement_details").css("height", height + "px");
	$("#agreement_summary").css("height", height + "px");	
	$("#agreement_details").show();
	$("#agreement_summary").show();
	
	$("#rightPanelAccordion").show();
	$("#rightPanelAccordion .ui-accordion-content").hide();
	var header_height = $("#rightPanelAccordion").outerHeight(true);	
	$("#rightPanelAccordion .ui-accordion-content").css("height", (height-header_height) + "px");	
	$("#rightPanelAccordion .ui-accordion-content").show();
}