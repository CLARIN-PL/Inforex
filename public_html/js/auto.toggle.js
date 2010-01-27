$(function(){
	$("a.toggle").click(function(){
		var selector = $(this).attr("label");
		$(selector).toggle("slow");
		return false;
	});
});

$(function(){
	$("a.toggle_simple").click(function(){
		var selector = $(this).attr("label");
		$(selector).toggle();
		return false;
	});
});