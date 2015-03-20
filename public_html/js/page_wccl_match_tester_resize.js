$(function(){	
	$(window).resize(resize_view);
});


function resize_view(){
	var height = $(window).height() - 180;
	$(".CodeMirror").css("height", (height + 4) + "px");
	_editor.refresh();
	$("#items").css("height", height - $("#summary").outerHeight() + "px");
};