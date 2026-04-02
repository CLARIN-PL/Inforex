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
	$("#flagsContainer").hide();
	$(".CodeMirror-wrapping").css("height", "0px");
    var panel = $("#edit_content_panel");
    var panelTop = panel.offset() ? panel.offset().top : 0;
    var viewportHeight = $(window).height();
    var reservedBottomSpace = 360;
	var panel_height = Math.max(viewportHeight - panelTop - reservedBottomSpace, 280);
    panel_height = Math.min(panel_height, Math.max(Math.floor(viewportHeight * 0.55), 280));
	$(".CodeMirror-wrapping").css("height", panel_height + "px");
	$("#flagsContainer").show();
}
