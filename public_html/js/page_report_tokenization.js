$(function(){
	
	$("#takipiwsProcess").click(function(){
		var text = $.trim($("#content").text());

		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_tokenization_process", 
						text: text
					},
			success:function(data){
						if ( data.success ){
							$("#tmp").text(data.result);
						}
						else
							dialog_error(data.errors);
					},
			error: function(request, textStatus, errorThrown){
					},
			dataType:"json"
		});		
		
		
	});
	
});