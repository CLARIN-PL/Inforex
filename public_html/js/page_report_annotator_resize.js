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

	var panel_height = $(window).height() - other_content_height;
	$("#content").css("overflow", "auto");
	$(".annotations").css("height", panel_height -250 + "px");
	$("#content").css("height", panel_height -70 + "px");
	$(".relationsContainer").css("height", panel_height -380 + "px");
	//$("#cell_annotation_edit").css("height",panel_height -660 + "px")
	
	$("#rightPanel").accordion({ 
		autoHeight: false,
		clearStyle : true
		}
	);
}