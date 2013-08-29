/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$.each($("#content *"), function(index, value){
		$(value).after('<span style="display:none">&nbsp;</span>');
	});
	
	$("#takipiwsProcess").click(function(){
		
		var button = this;
		
		$(button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
		$(button).attr("disabled", "disabled");
		
		var params = {
			report_id: $("#report_id").val()
		};
		
		var success = function(data){
			$("#messageBox").html("<div class='info'>Tokenization successfully completed. Reload page to see result.</div>");
		};
		
		var complete = function(){
			$(button).removeAttr("disabled");
			$(".ajax_indicator").remove();
		};
		
		
		doAjax("report_tokenization_process", params, success, null, complete);
	});
	
});