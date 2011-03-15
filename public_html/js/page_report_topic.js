$(function(){
	$("ul.topics a").click(function(){
		$("ul.topics a").removeClass("marked");
		
		var topic_id = $(this).attr("id").replace("topic_", "");
		var report_id = $("#report_id").attr("value");
		var item = $(this);

		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_update_topic", 
						report_id: report_id, 
						topic_id: topic_id
					},
			success:function(data){
						if (data['success']){
							item.addClass("marked");							
						}else if(data['error_code'] == 'ERROR_AUTHORIZATION'){
							// Okno dialogowe do zalogowania się użytkownika
							loginForm(false, function(success){ 
								if (success){
									save_content_ajax();
								}else{
									alert('Wystąpił problem z autoryzacją. Zmiany nie zostały zapisane.');								
									$("#save").removeAttr("disabled");
								}
							});
						}else{
							alert('Wystąpił nieznany błąd.');
						}
					},
			error: function(request, textStatus, errorThrown){
						$("#save").removeAttr("disabled");
					},
			dataType:"json"
		});		
				
		return false;
	});
});

