/*
Show Ajax status
text: show text in Ajax status element
option: loading, success, error
*/
function ajaxstatus($text,$option){
	$(".ajax_status_text").show();
	$(".ajax_status_text").removeClass("loading").removeClass("success").removeClass("error").addClass($option);
	$(".ajax_status_text").html($text);
	$(".ajax_status_text").delay(1500).hide("slow");
}

function createWordDialog(){
	$("body").append(''+
			'<div id="dialog-form-create-word" title="Create new word" style="">'+
			'	<form>'+
			'	<fieldset style="border-width: 0px">'+
			'		<label for="wordname" style="float: left; width: 60px; text-align: right;margin-bottom: 5px; line-height: 1em">Word:</label>'+
			'		<input type="text" name="wordname" id="wordname" class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px; background: #eee" />'+
			'	</fieldset>'+
			'	</form>'+
			'   <span style="color: red; margin-left: 70px" id="create-word-form-error"></span>'+	
			'</div>');

	$("#dialog-form-create-word").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Create': function() {
				createWord($(this));
			},
			'Cancel': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-form-create-word").remove();
		}
	});	
	
	$("#dialog-form-login input[name=username]").focus();	
}

function createWord(dialog){

	var wordname = $("#wordname").val();

	$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "sens_edit_add_word", 
						wordname: wordname
					},						
			success: function(data){
						if (data['success']){
							dialog.dialog('destroy');
							$("#dialog-form-create-word").remove();
							ajaxstatus("Dodano słowo " + wordname, "success");
						}else{
							var errorMsg = "Wprowadź słowo";
							$("#create-word-form-error").html(errorMsg);
						}
					},
			error: function(request, textStatus, errorThrown){	
						$("#create-word-form-error").html(request.responseText);		
					},
			dataType:"json"						
	});
}


$(function(){
	
	$("span.sensCreate").click(function(){
		createWordDialog();
		return false;
	});
	
	
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
							html += "<div class='sensItem'><div class='sensItemDescription'><b>" + data[a]['value'] + ":</b> " + data[a]['description'];
							html += "<br><span class='sensItemEdit' id=data[a]['value']>[edytuj opis]</span></div>";
							html += "<div class='sensItemEditForm' id=" + data[a]['value'] + " style='display:none'><div><b>Edycja " + data[a]['value'] + "</b></div>";
														
							html += "<form>";
							html += "<label for='lemat'><b>Lemat:</b></label> <input class='input' type='text' size='50' name='sensNameEdit' value=" + this_sens_name + " /><br />";
  							html += "<label for='opis'><b>Opis:</b></label> <textarea class='input' cols='48' rows='10' name='sensDescriptionEdit'>" + data[a]['description'] + "</textarea><br />"
  							
  							html += "<button type='button' name='zapisz'>Zapisz</button>";
  							html += "<button type='button' name='anuluj'>Anuluj</button>";
							html += "</form> ";
							
							html += "</div><br></div>";
							if(a < data_length){
								html += "<hr width='85%'/>";
							}														
						}
						$("#sensDescriptionContainer").show();
						$("#sensDescriptionList").html(html);
						$(button).removeAttr("disabled");
						$(".ajax_indicator").remove();			
						ajaxstatus("Załadowano słowo: " + this_sens_name, "success");					
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
			$(this).parent().parent().find('div.sensItemEditForm').show("slow");
			$(this).parent().hide("slow");
//			var newElem = $(this).parent().clone().attr('id', 'input');
 //           newElem.children(':first').attr('id', 'name').attr('name', 'name');
  //          $('#input').after(newElem);
			
		}
	});
			
});