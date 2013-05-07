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
});

/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function fit_transcriber_to_screen(){
	//var other_content_height = $(document).height() - $(".horizontal").outerHeight();
	var other_content_height = $("#main_menu").outerHeight();
	other_content_height += $("#sub_menu").outerHeight();
	other_content_height += $("#page_content .pagging").outerHeight();
	other_content_height += $("#page_content ul.ui-tabs-nav").outerHeight();
	other_content_height += $("#footer").outerHeight();
	other_content_height += $("#wsd_navigation").outerHeight();
	other_content_height += $("#perspective-foot").outerHeight();

	var panel_height = $(window).height() - other_content_height;
	$("#content").css("overflow", "auto");
	$("#content").css("height", panel_height -70 + "px");
	$(".annotations").css("height", panel_height -70 + "px");
	$("#wsd_senses").css("height", panel_height -70 + "px");
}