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
		
		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_tokenization_process", 
						report_id : $("#report_id").val()
					},
			success:function(data){
						if ( data.success ){
							$("#messageBox").html("<div class='info'>Tokenization successfully completed. Reload page to see result.</div>");
						}
						else
							$("#messageBox").html("<div class='error'>Tokenization failed. "+data.error+"</div>");
						$(button).removeAttr("disabled");
						$(".ajax_indicator").remove();
					},
			error: function(request, textStatus, errorThrown){
						$("#messageBox").text("Tokenization failed.");
						$(button).removeAttr("disabled");
						$(".ajax_indicator").remove();
					},
			dataType:"json"
		});		
		
		
	});
	
});