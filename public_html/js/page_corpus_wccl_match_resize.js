$(function(){	
	resize_view();
	$(window).resize(resize_view);
});

function resize_view(){
	var height = $(window).height() - 240;
	$("#wccl_rules .CodeMirror").css("height", (height - $("#toolbox_wrapper").outerHeight()-10) + "px");
	_editor.refresh();
	$("#items").css("height", height - $("#summary").outerHeight() + "px");
};