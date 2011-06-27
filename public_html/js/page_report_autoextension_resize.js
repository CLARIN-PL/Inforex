/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
	fit_panel_to_screen();	
});

/**
 * Funkcja dopasowuje ekran transkprycji do wielkości przeglądarki.
 * @return
 */
function fit_panel_to_screen(){
	var other_content_height = $("#main_menu").outerHeight();
	other_content_height += $("#sub_menu").outerHeight();
	if ($("#page_content .ui-state-error").outerHeight())
		other_content_height += $("#page_content .ui-state-error").outerHeight() + 15;
	other_content_height += $("#page_content .pagging").outerHeight();
	other_content_height += $("#page_content ul.ui-tabs-nav").outerHeight();
	other_content_height += $("#footer").outerHeight();
	
	var panel_height = $(window).height() - other_content_height;
	$("#content").css("height", panel_height -70 + "px");
	$("#annotationList").css("height", panel_height -80 + "px");
}