$.live("a.toggle").click(function(){
	var selector = $(this).attr("label");
	$(selector).toggle("slow");
	return false;
});

$.live("a.toggle_simple").click(function(){
	var selector = $(this).attr("label");
	$(selector).toggle();
	return false;
});

$(function(){
	$("a.toggle_cookie").click(function(){
		var selector = $(this).attr("label");
		if ($(selector).is(":visible"))
			$.cookie(selector, "hide");
		else
			$.cookie(selector, "show");
		$(selector).toggle();
		return false;
	});
	$("a.toggle_cookie").each(function(index){
		var selector = $(this).attr("label");
		if ($.cookie(selector) == "hide")
			$(selector).hide();
		else
			$(selector).show();
	});
});
