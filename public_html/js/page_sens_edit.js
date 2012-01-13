$(function(){
	
	$("tr.sensName").click(function(){
		var button = this;
		$(button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
		$(button).attr("disabled", "disabled");
		if (! $(this).hasClass("selected")){
			$("tr.sensName").removeClass("selected");
			$(this).addClass("selected");	
		}
		$(".sensDelete").show();
		var this_sens_id = $(this).attr('id');
		var this_sens_name = $(this).find('td.sens_name').text();
		//ajaxstatus("Ładuję słowo: " + this_sens_name, "loading");
		$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "sens_edit_get",
						sens_id: this_sens_id
					},
			success:function(data){
						var html = "";
						var data_length = data.length - 1;
						for (a in data){
							html += "<div class='sensItem'><b>" + data[a]['value'] + ":</b> " + data[a]['description'];
							html += "<br><span class='sensItemEdit' id=data[a]['value']>[edytuj opis]</span>";
							html += "</div><br>";
							if(a < data_length){
								html += "<hr width='85%' />";
							}														
						}
						$("#sensDescriptionContainer").show();
						$("#sensDescriptionList").html(html);
						$(button).removeAttr("disabled");
						$(".ajax_indicator").remove();			
						ajaxstatus("Załadowano słowo: " + this_sens_name, "sucess");					
					},
			error: function(request, textStatus, errorThrown){
						$(button).removeAttr("disabled");
						$(".ajax_indicator").remove();										
						ajaxstatus("Błąd ładowania słowa: " + this_sens_name, "error");
					},
			dataType:"json"
		});		
	});
	
	$("span.sensItemEdit").live({
		click: function(){
			//$(this).parent().hide();
//			var newElem = $('#input').clone().attr('id', 'input');
 //           newElem.children(':first').attr('id', 'name').attr('name', 'name');
  //          $('#input').after(newElem);
			
		}
	});
			
});


/*
option: loading, sucess, error
*/
function ajaxstatus($text,$option){
	$(".ajax_status_text").show();
	$(".ajax_status_text").removeClass("loading").removeClass("sucess").removeClass("error").addClass($option);
	$(".ajax_status_text").html($text);
	$(".ajax_status_text").delay(1500).hide("slow");
}