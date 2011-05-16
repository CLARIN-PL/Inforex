$(function(){
	
	$("#takipiwsProcess").click(function(){
		var text = $.trim($("#content").text());

		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_tokenization_process", 
						text: text,
						report_id : $("#report_id").val()
					},
			success:function(data){
						if ( data.success ){
							$("#messageBox").text("Tokenization successfully completed");
						}
						else
							$("#messageBox").text("Tokenization failed. "+data.error);
					},
			error: function(request, textStatus, errorThrown){
						$("#messageBox").text("Tokenization failed.");
					},
			dataType:"json"
		});		
		
		
	});
	
});