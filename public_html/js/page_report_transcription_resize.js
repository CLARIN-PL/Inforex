/**
 * Skrypt do obsługi dopasowania edytora do wielkości przeglądarki.
 */
$(function(){
	fit_transcriber_to_screen();
	
	$("#transcriber_horizontal").click(function(){
		$("#transcriber").attr("class", "horizontal");
		$.cookie("orientation", "horizontal");
		fit_transcriber_to_screen();		
	});
	$("#transcriber_vertical").click(function(){
		$("#transcriber").attr("class", "vertical");
		$.cookie("orientation", "vertical");
		fit_transcriber_to_screen();		
	});
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
	other_content_height += 30;

	var panel_height = $(window).height() - other_content_height;
	
	// Sprawdź rodzaj ułożenia
	if ($(".horizontal").size()>0){
		panel_height -= 85;
		$("#zoom").css("height", panel_height/2 + "px");
		$("#frame_elements div.elements").css("height", panel_height/2 + "px");
		$(".CodeMirror-wrapping").css("height", panel_height/2 + 6 + "px");
		$("#frame_editor .inner_border").css("height", panel_height/2 + 6 + "px");
	}
	else{
		$("#transcriber").css("height", panel_height + "px");
		$("#zoom").css("height", panel_height - 30 + "px");		
		$("#frame_elements div.elements").css("height", 250 + "px");
		$("#frame_editor .inner_border").css("height", panel_height - 330 + 4 + "px");
		$(".CodeMirror-wrapping").css("height", panel_height - 330 + "px");
	}
}