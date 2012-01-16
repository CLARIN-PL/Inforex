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
	
	$("#dialog-form-create-word input[name=wordname]").focus();	
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
							getWords();
							ajaxstatus("Dodano słowo " + wordname, "success");
						}else{
							$("#create-word-form-error").html(data['error']);
						}
					},
			error: function(request, textStatus, errorThrown){	
						dialog_error("<b>HTML result:</b><br/>" + request.responseText);		
					},
			dataType:"json"						
	});
}

function getWords(){
	$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "sens_edit_get_words" 
					},						
			success: function(data){
						var html = "";
						var i = 1;
						for (a in data){
							html += "<tr class='sensName' id=" + data[a]['id'] + " >";
							html += "<td>" + i + "</td>";
							html += "<td class='sens_name'>" + data[a]['annotation_type'] + "</td>";
							html += "</tr>";
							i = i + 1;
						}
						$("#sensTableItems").html(html);
					},
			error: function(request, textStatus, errorThrown){	
						dialog_error("<b>HTML result:</b><br/>" + request.responseText);		
					},
			dataType:"json"						
	});
}

function deleteWordDialog(name){
	$("body").append(''+
			'<div id="dialog-form-delete-word" title="Delete word" style="">'+
			'	<div id="wordname" style="float: left; text-align: right;margin-bottom: 5px; line-height: 1em">Delete word '+ name +	'?</div>'+
			'   <br><span style="color: red; margin-left: 70px" id="delete-word-form-error"></span>'+	
			'</div>');
	$("#dialog-form-delete-word").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Yes': function() {
				deleteWord($(this),name);
			},
			'No': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-form-delete-word").remove();
		}
	});	
}

function deleteWord(dialog,name){
	$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "sens_edit_delete_word", 
						name: name
					},						
			success: function(data){
						if (data['success']){
							dialog.dialog('close');
							$("#dialog-form-create-word").remove();
							$("#sensDescriptionContainer").hide();
							getWords();
							ajaxstatus("Usunięto słowo " + name, "success");
						}else{
							$("#delete-word-form-error").html(data['error']);
						}
					},
			error: function(request, textStatus, errorThrown){	
						dialog_error("<b>HTML result:</b><br/>" + request.responseText);		
					},
			dataType:"json"						
	});
}

function createSensDialog(name){
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
	
	$("#dialog-form-create-word input[name=wordname]").focus();	
}


$(function(){
	
	$("span.sensCreate").click(function(){
		createWordDialog();
		return false;
	});
	
	$("span.sensDelete").click(function(){
		var name = $(this).attr('id');
		deleteWordDialog(name);
		$(this).hide();
		return false;
	});
	
	$("span.sensDescriptionCreate").click(function(){
		var name = $(this).attr('id');
		createSensDialog(name);
		$(this).hide();
		return false;
	});
	
	$("tr.sensName").live({
		click: function(){
			var button = this;
			$(button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
			$(button).attr("disabled", "disabled");
			if (! $(this).hasClass("selected")){
				$("tr.sensName").removeClass("selected");
				$(this).addClass("selected");	
			}
			var this_sens_id = $(this).attr('id');
			var this_sens_name = $(this).find('td.sens_name').text();
			$(".sensDelete").show();
			$(".sensDelete").attr("id",this_sens_name);
			$(".sensDescriptionCreate").attr("id",this_sens_name);
			//ajaxstatus("Ładuję słowo: " + this_sens_name, "loading");
			$.ajax({
				type: 	'POST',
				url: 	"index.php",
				data:	{ 	
						ajax: "sens_edit_get_sens",
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
		}	
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