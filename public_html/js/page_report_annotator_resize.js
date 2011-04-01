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
	if ($("#page_content .ui-state-error").outerHeight())
		other_content_height += $("#page_content .ui-state-error").outerHeight() + 15;
	other_content_height += $("#page_content .pagging").outerHeight();
	other_content_height += $("#page_content ul.ui-tabs-nav").outerHeight();
	other_content_height += $("#footer").outerHeight();

	var panel_height = $(window).height() - other_content_height;
	$("#content").css("overflow", "auto");
	$(".annotations").css("height", panel_height -215 + "px");
	$("#content").css("height", panel_height -70 + "px");
	$(".relationsContainer").css("height", panel_height -255 + "px");
	$("#eventList").css("height", panel_height -235 + "px"); 
	//$("#cell_annotation_edit").css("height",panel_height -660 + "px")
	
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