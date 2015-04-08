/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
	fit_report_annotator_to_screen();
	setup_accordion();
	$(window).resize(function() {
		fit_report_annotator_to_screen();
	});
});

/**
 * TODO to chyba nie jest miejsce na ten kawałek kodu
 * @return
 */
function setup_accordion(){
	var $panelAccordion = $("#rightPanelAccordion");
	$panelAccordion.children("h3").removeAttr("class").removeAttr("role").removeAttr("tabindex").removeAttr('aria-expanded').children("span").remove();
	$panelAccordion.children("div").css({
		'padding-top':'',
		'padding-bottom':'',
		'display':'block'
	}).removeAttr('class').removeAttr("role");
	$panelAccordion.accordion({ 
		autoHeight: false,
		clearStyle : true,
		animated : false,
		active : ($.cookie("accordionActive") ? "#"+$.cookie("accordionActive") : 0),
		changestart: function(event, ui) {
			panelId = $(ui.newHeader).attr("id");
			$.cookie("accordionActive",panelId);
		}
	});	
}

/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function fit_report_annotator_to_screen(){
	var other_content_height = $("#main_menu").outerHeight();
	other_content_height += $("#sub_menu").outerHeight();
	if ($("#tabs .ui-state-error").outerHeight()){
		other_content_height += $("#tabs .ui-state-error").outerHeight() + 20;
	}
	other_content_height += $("#tabs .pagging").outerHeight();
	other_content_height += $("#exceptions").outerHeight(true);
	other_content_height += $("#tabs ul.ui-tabs-nav").outerHeight();
	other_content_height += $("#footer").outerHeight();
	other_content_height += 10;

	var panel_height = $(window).height() - other_content_height;
	$("div.content").css("overflow", "auto");
	$(".annotations").css("height", panel_height -215 + "px");
	$("#leftContent").css("height", panel_height -70 + "px");
	$("#rightContent").css("height", panel_height -70 + "px");
	$(".relationsContainer").css("height", panel_height -275 + "px");
	$("#eventList").css("height", panel_height -235 + "px");
	$("#annotation_layers").css("height", panel_height -215 + "px");
	
	
	/** Zmniejsz wysokość panelu z listą anotacji o nagłówek do filtrowania stanu anotacji */
	var boxAnn = $("#annotationList");
	boxAnn.css("height", boxAnn.outerHeight() - boxAnn.prev().outerHeight() + "px" );
}