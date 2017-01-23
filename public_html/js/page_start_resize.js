$(function(){
	fit_to_screen();
});

function fit_to_screen(){
	$(".scroll").hide();
	var height = $(window).height() - $("body").outerHeight(true);
	$(".scroll").css("margin", 0);
	$(".scroll").css("overflow", "auto");
	$(".scroll").css("height", height+"px");
	$(".scroll").show();
}
