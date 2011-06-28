$(function(){
	$.each($("#content *"), function(index, value){
		$(value).after('<span style="display:none">&nbsp;</span>');
	});
	
	$("#takipiwsProcess").click(function(){
		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_tokenization_process", 
						report_id : $("#report_id").val()
					},
			success:function(data){
						if ( data.success ){
							$("#messageBox").text("Tokenization successfully completed. Reload page to see result.");
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