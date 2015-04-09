/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
	fit_panel_to_screen();	
	$(window).resize(function() {
		fit_panel_to_screen();
	});	
});

/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function fit_panel_to_screen(){
	var other_content_height = $("#main_menu").outerHeight(true);
	other_content_height += $("#sub_menu").outerHeight(true);
	if ($("#page_content .ui-state-error").outerHeight())
		other_content_height += $("#page_content .ui-state-error").outerHeight() + 15;
	other_content_height += $("#page_content .pagging").outerHeight();
	other_content_height += $("#page_content ul.ui-tabs-nav").outerHeight();
	other_content_height += $("#footer").outerHeight();
	
	var panel_height = $(window).height() - other_content_height;
	$("#content").css("height", panel_height - 120 + "px");
	
	var list_height = panel_height - 140;
	list_height -= $("#annotation_sets").outerHeight(true);
	$("#annotationList").css("height", list_height + "px");
}