/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	
	$("#ner-process").click(function(){

		var text = $.trim($("#ner-text").val());
		var wsdl = $.trim($("input[name=wsdl]:checked").val());
		
		if ( text.length > 100000 ){
			alert("The text cannot be processed because is longer than 100k characters.");
			return;
		}
		
		if ( text == "" )
			dialog_error("Enter text to analyze");
		else{
			$("#ner-process").attr("disabled", "disabled");
			$("#ner-html").css("color", "grey");
			$("#ner-annotations").css("color", "grey");
			$("#ner-process").before("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
			
			var model = $("#ner-model option:selected").val();
			
			var params = {
				text: text,
				model: model,
				wsdl: wsdl	
			};
			
			var success = function(data){
				$("#ner-html").html(data.html);								
				$("#ner-html").css("color", "black");
				$("#ner-annotations").html(data.annotations);
				$("#ner-duration").html("Processed in " + data.duration);
				$("#ner-annotations").css("color", "black");
			};
			
			var complete = function(){
				$("#ner-process").removeAttr("disabled");
				$(".ajax_indicator").remove();
			};
			
			doAjax("ner_process", params, success, null, complete);
			
					
		}
		
	});
	
	$("#samples a").click(function(){
		$("#ner-text").val($(this).attr("title"));
	});
});