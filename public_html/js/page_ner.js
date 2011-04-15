$(function(){
	
	$("#ner-process").click(function(){

		var text = $.trim($("#ner-text").val());
		
		if ( text == "" )
			dialog_error("Enter text to analyze");
		else{
			$("#ner-process").attr("disabled", "disabled");
			$("#ner-html").css("color", "grey");
			$("#ner-annotations").css("color", "grey");
			$("#ner-process").after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
			$.ajax({
				type: 	'POST',
				url: 	"index.php",
				data:	{ 	
							ajax: "ner_process", 
							text: text 
						},
				success:function(data){
							if ( data.success ){
								$("#ner-html").html(data.html);								
								$("#ner-html").css("color", "black");
								$("#ner-annotations").html(data.annotations);
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