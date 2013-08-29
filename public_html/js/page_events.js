/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	
	$("#events-process").click(function(){
		var text = $.trim($("#events-text").val());
		
		if ( text == "" )
			dialog_error("Podaj treść raportu");
		else{
			$("#events-process").attr("disabled", "disabled");
			$("#events-html").css("color", "grey");
			
			var success = function(data){
				$("#events-html").html(data.html);
				$("#events-struct").html(data.struct);
				$("#events-html").css("color", "black");
			};
			
			var complete = function(){
				$("#events-process").removeAttr("disabled");
			};
			
			doAjax("events_process", {text:text}, success, null, error);
		}
		
	});
	
});