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
	var other_content_height = $("#main_menu").outerHeight();
	other_content_height += $("#sub_menu").outerHeight();
	other_content_height += $("#page_content .pagging").outerHeight();
	other_content_height += $("#page_content h1").outerHeight();
	other_content_height += $(".ui-state-highlight").outerHeight();
	other_content_height += $("h3").outerHeight();
	other_content_height += $("#footer").outerHeight();
	other_content_height += $("#sample").outerHeight();
	other_content_height += $("#ner-process").outerHeight();
	other_content_height += 90;

	var panel_height = $(window).height() - other_content_height;
	
	//$("#list_of_topics").css("height", panel_height + "px");
	$("#ner-text").css("height", panel_height + "px");
	$("#ner-html").css("height", panel_height + "px");
	$("#ner-annotations").css("height", panel_height + "px");
}