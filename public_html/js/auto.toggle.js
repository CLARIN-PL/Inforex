$("a.toggle").live("click", function(){
	var selector = $(this).attr("label");
	$(selector).toggle("slow");
	return false;
});

$("a.toggle_simple").live("click", function(){
	var selector = $(this).attr("label");
    var order_limit_filter = $('#filter_order_and_results_limit');
	$(selector).toggle();
        if ($(selector).is(':visible') && $(selector).attr('need_order_and_results_limit')) {
            if (!$(order_limit_filter).is(':visible')) {
                $('select[name="results_limit"]').val($(selector).find('select[name="results_limit"]').val());
                order_limit_filter.show();
            }
        }
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
