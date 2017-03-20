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
	$('.scrolling').hide();
    $('#col-content').hide();
	$('#col-config').hide();
    $('#col-flags').hide();
    var windowHeight = $(window).height();
    var boilerplatesHeight = $("#page").outerHeight(true);

    var colContentBoilerplatesheight = $("#col-content").outerHeight(true);
    $("#leftContent").css("height", (windowHeight - boilerplatesHeight - colContentBoilerplatesheight - 5) + "px");
    $("#rightContent").css("height", (windowHeight - boilerplatesHeight - colContentBoilerplatesheight - 5) + "px");

    var colConfigBoilerplatesheight = $("#col-config").outerHeight(true);
    $("#annotation_layers").css("height", (windowHeight - boilerplatesHeight - colConfigBoilerplatesheight - 5) + "px");
    $("#annotationList").css("height", (windowHeight - boilerplatesHeight - colConfigBoilerplatesheight - 5) + "px");
    $("#relationList").css("height", (windowHeight - boilerplatesHeight - colConfigBoilerplatesheight - 5) + "px");

    var colFlagBoilerplatesheight = $("#col-flags").outerHeight(true);
    $("#flagList").css("height", (windowHeight - boilerplatesHeight - colFlagBoilerplatesheight - 5) + "px");


	$('.scrolling').show();
    $('#col-content').show();
    $('#col-config').show();
    $('#col-flags').show();
}