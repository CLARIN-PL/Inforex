$(function(){
	
	$("#events-process").click(function(){
		var text = $.trim($("#events-text").val());
		
		if ( text == "" )
			dialog_error("Podaj treść raportu");
		else{
			$("#events-process").attr("disabled", "disabled");
			$("#events-html").css("color", "grey");
			$.ajax({
				type: 	'POST',
				url: 	"index.php",
				data:	{ 	
							ajax: "events_process", 
							text: text 
						},
				success:function(data){
							$("#events-html").html(data.html);
							$("#events-struct").html(data.struct);
							$("#events-html").css("color", "black");
							$("#events-process").removeAttr("disabled");
						},
				error: function(request, textStatus, errorThrown){
							$("#events-process").removeAttr("disabled");
						},
				dataType:"json"
			});		
		}
		
	});
	
});