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
	var body_height = $("body").height();
	var page_content_height = $("#page_content").height();
	var boilerplate_height = body_height - page_content_height;
	
	var task_progress_height = $("#taskProgress").height();
	var documents_status_height = $("#documents_status").height();
	var task_progress_boilerplate = task_progress_height - documents_status_height;
	
	var height = $(window).height() - boilerplate_height - task_progress_boilerplate - 30;

	$("#documents_status").css("height", height + "px");
}