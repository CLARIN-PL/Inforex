/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	
	$("#ner-process").click(function(){

		var text = $.trim($("#ner-text").val());
		var wsdl = $.trim($("input[name=wsdl]:checked").val());
		
		if ( text.length > 10000 ){
			alert("The text cannot be processed because is longer than 10k characters.");
			return;
		}
		
		if ( text == "" )
			dialog_error("Enter text to analyze");
		else{
			$("#ner-process").attr("disabled", "disabled");
			$("#ner-html").css("color", "grey");
			$("#ner-annotations").css("color", "grey");
			$("#ner-process").after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
			
			var model = $("#ner-model option:selected").val();
			
			$.ajax({
				type: 	'POST',
				url: 	"index.php",
				data:	{ 	
							ajax: "ner_process", 
							text: text,
							model: model,
							wsdl: wsdl
						},
				success:function(data){
							if ( data.success ){
								$("#ner-html").html(data.html);								
								$("#ner-html").css("color", "black");
								$("#ner-annotations").html(data.annotations);
								$("#ner-duration").html("Processed in " + data.duration);
								$("#ner-annotations").css("color", "black");
							}
							else
								dialog_error(data.errors);
							$("#ner-process").removeAttr("disabled");
							$(".ajax_indicator").remove();
						},
				error: function(request, textStatus, errorThrown){
							$("#ner-process").removeAttr("disabled");
							$(".ajax_indicator").remove();
						},
				dataType:"json"
			});		
		}
		
	});
	
	$("#samples a").click(function(){
		$("#ner-text").val($(this).attr("title"));
	});
});