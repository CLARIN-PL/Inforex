/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

/*
Show Ajax status in ajax_status element
text: show text in Ajax status element
option: loading, success, error
*/
function ajaxstatus($text,$option){
	$(".ajax_status_text").show();
	$(".ajax_status_text").removeClass("loading").removeClass("success").removeClass("error").addClass($option);
	$(".ajax_status_text").html($text);
	//$(".ajax_status_text").delay(1500).hide("slow");
}

/*
Pobiera słowa i wyświetla je w tabeli sensTableItems
*/
function getWords(){
	
	var success = function(data){
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
	};
	
	doAjax("sens_edit_get_words", {}, success);
}

/*
Pobiera sensy słów i wyświetla je w tabeli sensDescriptionList
sens_id - id typu sensów
this_sens_name - nazwa słowa bez przedrostka "wsd_"
*/
function getSens(button,sens_id,this_sens_name,show_ajax_status){
	$(button).find("td.sens_name").append("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	$(button).attr("disabled", "disabled");
	
	var success = function(data){
		var html = "";
		var data_length = data.length - 1;
		html += "<div class='sensTableHeader ui-widget ui-widget-header ui-helper-clearfix ui-corner-all'>Senses of lemma " + this_sens_name + "</div>";
		html += "<div class='sensDescriptionContent'>";
		html += "<div id='sensDescriptionList'>";
		for (a in data){
			html += "<div class='sensItem'><div class='sensItemDescription' id=" + data[a]['value'] + "><b>" + data[a]['value'] + ":</b> " + data[a]['description'];
				html += "<br><span class='sensItemEdit' id=" + data[a]['value'] + ">[edit description]</span></div>";
				html += "<div class='sensItemEditForm' id=" + data[a]['value'] + " style='display:none'><div><b>Editing " + data[a]['value'] + "</b></div>";
										
				html += "<form>";
				html += "<label class='input' for='sensNameEdit'><b>Lemma:</b></label> <input class='input' type='text' size='50' name='sensNameEdit' value=" + this_sens_name + " disabled='disabled'/><br />";
					html += "<label class='input' for='sensDescriptionEdit'><b>Description:</b></label> <textarea class='input' cols='48' rows='10' id='edit_text_area' name='sensDescriptionEdit'>" + data[a]['description'] + "</textarea><br />"
					html += "<textarea id='hidden_text_area' style='display:none'>" + data[a]['description'] + "</textarea>";
					
					html += "<button type='button' class='saveSens' name='saveSens'>Save</button>";
					html += "<button type='button' class='discardSens' name='discardSens' title='Without making changes'>Close</button>";
					html += "<button type='button' class='deleteSens' id=" + data[a]['value'] + " name='deleteSens'>Delete</button>";
					html += "<div class='sens_id' id=" + sens_id + "></div><div class='sens_name' id=" + this_sens_name + "></div>";
				html += "</form> ";
				
				html += "</div><br></div>";
			if(a < data_length){
				html += "<hr width='85%'/>";
			}														
		}
		html += "</div></div>";
		html += "<div class='descriptionTableOptions ui-widget ui-widget-content ui-corner-all' element='relation_type' id=" + sens_id + ">";
		html += "<span class='sensDescriptionCreate' id=" + this_sens_name + "><a href='#'>(Add new sense)</a></span>";
		html += "</div>";
		
		$("#sensDescriptionContainer").show();
		$("#sensDescriptionContainer").html(html);							
		$(button).removeAttr("disabled");
		$(".ajax_indicator").remove();
		if(show_ajax_status){
			ajaxstatus("Editing lemma: " + this_sens_name, "success");
		}	
	};
	
	var error = function(){
		ajaxstatus("Błąd ładowania słowa: " + this_sens_name, "error");
	};
	
	doAjax("sens_edit_get_sens", {sens_id: sens_id}, success, error);
	
}	

/***************************************************************/
/********Okna dialogowe*****************************************/
/***************************************************************/

/*
Okno do dodawania słów
*/
function createWordDialog(){
	$("body").append(''+
			'<div id="dialog-form-create-word" title="Add new lemma" style="">'+
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
			'Cancel': function() {
				$(this).dialog('close');
			},
			'Create': function() {
				createWord($(this));
			}
		},
		close: function() {
			$("#dialog-form-create-word").remove();
		}
	});	
	
	$("#dialog-form-create-word input[name=wordname]").keypress(function(e) {
		if(e.which == 13){
			createWord($(this));
			return false;
		}
	});	
		
	$("#dialog-form-create-word input[name=wordname]").focus();	
}

/*
Okno do edycji słów
*/
function editWordDialog(name){
	$("body").append(''+
			'<div id="dialog-form-edit-word" title="Edit lemma ' + name + '" style="">'+
			'	<form>'+
			'	<fieldset style="border-width: 0px">'+
			'		<label for="wordname" style="float: left; width: 60px; text-align: right;margin-bottom: 5px; line-height: 1em">Lemma:</label>'+
			'		<input type="text" name="wordname" id="wordname" value=' + name + ' class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px; background: #eee" />'+
			'	</fieldset>'+
			'	</form>'+
			'   <span style="color: red; margin-left: 70px" id="edit-word-form-error"></span>'+	
			'</div>');
	
	$("#dialog-form-edit-word").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Save': function() {
				editWord($(this),name);
			},
			'Cancel': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-form-edit-word").remove();
		}
	});	
	
	$("#dialog-form-edit-word input[name=wordname]").keypress(function(e) {
		if(e.which == 13){
			editWord($(this),name);
			return false;
		}
	});	
	
	$("#dialog-form-edit-word input[name=wordname]").focus();	
}

/*
Okno do usuwania słów
*/
function deleteWordDialog(name){
	$("body").append(''+
			'<div id="dialog-form-delete-word" title="Delete lemma" style="">'+
			'	<div id="wordname" style="float: left; text-align: right;margin-bottom: 5px; line-height: 1em">Delete lemma '+ name +	'?</div>'+
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

/*
Okno do tworzenia nowych sensów słów
*/
function createSensDialog(name,id){
	$("body").append(''+
			'<div id="dialog-form-create-sens" title="Add new sense" style="">'+
			'	<form>'+
			'	<fieldset style="border-width: 0px">'+
			'		<label for="sensnum" style="float: left; width: 60px; text-align: right;margin-bottom: 5px; line-height: 1em">' + name + '-</label>'+
			'		<input type="text" name="sensnum" id="sensnum" class="text ui-widget-content ui-corner-all" style="margin-bottom: 5px; background: #eee" />'+
			'	</fieldset>'+
			'	</form>'+
			'   <span style="color: red; margin-left: 70px" id="create-sens-form-error"></span>'+	
			'</div>');

	$("#dialog-form-create-sens").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Cancel': function() {
				$(this).dialog('close');
			},
			'Create': function() {
				createSens($(this),name,id);
			}
		},
		close: function() {
			$("#dialog-form-create-sens").remove();
		}
	});	
	
	$("#dialog-form-create-sens input[name=sensnum]").keypress(function(e) {
		if(e.which == 13){
			createSens($(this),name,id);
			return false;
		}
	});
	
	$("#dialog-form-create-sens input[name=sensnum]").focus();	
}

/*
Okno do potwierdzenia anulowania zapisu sensu
*/
function closeSensDialog(button,hidden_textarea_value){
	$("body").append(''+
			'<div id="dialog-discard-sens" title="Discard sense" style="">'+
			'	<fieldset style="border-width: 0px">'+
			'		<label style="float: left; text-align: right;margin-bottom: 5px; line-height: 1em">Discard changes?</label>'+
			'	</fieldset>'+
			'</div>');

	$("#dialog-discard-sens").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Yes': function() {
				$(button).parent().find('#edit_text_area').attr('value',hidden_textarea_value);
				$(button).parent().parent().parent().find('div.sensItemDescription').show("");
				$(button).parent().parent().hide("");
				$(this).dialog('close');
			},
			'No': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-discard-sens").remove();
		}
	});	
}

/*
Okno do usuwania sensów
*/
function deleteSensDialog(name,sens_id,sens_name){
	$("body").append(''+
			'<div id="dialog-form-delete-sens" title="Delete sense" style="">'+
			'	<div id="sensname" style="float: left; text-align: right;margin-bottom: 5px; line-height: 1em">Delete sense '+ name +	'?</div>'+
			'   <br><span style="color: red; margin-left: 70px" id="delete-sens-form-error"></span>'+	
			'</div>');
	$("#dialog-form-delete-sens").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Yes': function() {
				deleteSens($(this),name,sens_id,sens_name);
			},
			'No': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-form-delete-sens").remove();
		}
	});	
}

/***************************************************************/
/********Operacje dla słów**************************************/
/***************************************************************/

/*
Tworzenie słów
*/
function createWord(dialog){

	var wordname = $("#wordname").val();

	var success = function(data){
		dialog.dialog('destroy');
		$("#dialog-form-create-word").remove();
		getWords();
		ajaxstatus("Added lemma: " + wordname, "success");
	};
	
	var error = function(code){
		if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
			$("#create-word-form-error").html("Wystąpił błąd.");
		}
	};
	
	doAjax("sens_edit_add_word", {wordname: wordname}, success, error);
}

/*
Edycja słów
*/
function editWord(dialog,oldwordname){

	var newwordname = $("#wordname").val();
	
	var params = {
		newwordname: newwordname,
		oldwordname: oldwordname
	};
	
	var success = function(data){
		var sens_num = data['sens_num'];
		dialog.dialog('destroy');
		$("#dialog-form-edit-word").remove();
		getWords();
		getSens(dialog,sens_num,newwordname,0);
		ajaxstatus("Edited lemma: " + newwordname, "success");		
		$(".sensEdit").hide();
		$(".sensDelete").hide();
	};
	
	var error = function(code){
		if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
			$("#edit-word-form-error").html("Wystąpił błąd.");
		}
	};
	
	doAjax("sens_edit_update_word", params, success, error);
}

/*
Usuwanie słów
*/
function deleteWord(dialog,name){
	
	var success = function(data){
		dialog.dialog('close');
		$("#dialog-form-delete-word").remove();
		$("#sensDescriptionContainer").hide();
		getWords();
		$(".sensEdit").hide();
		$(".sensDelete").hide();
		ajaxstatus("Deleted lemma: " + name, "success");
	};
	
	var error = function(){
		if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
			$("#delete-word-form-error").html("Wystąpił błąd.");
		}
	};
	
	doAjax("sens_edit_delete_word", {name: name}, success, error);
}

/***************************************************************/
/********Operacje dla sensów************************************/
/***************************************************************/

/*
Tworzenie sensów
*/
function createSens(dialog,sensname,sensid){
	var sensnum = $("#sensnum").val();
	
	var params = {
		sensname: sensname,
		sensid: sensid,
		sensnum: sensnum
	};
	
	var success = function(data){
		dialog.dialog('close');
		$("#dialog-form-create-sens").remove();
		getSens(dialog,sensid,sensname,0);
		ajaxstatus("Added sense: " + sensname + "-" + sensnum, "success");
	};
	
	var error = function(){
		if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
			$("#create-sens-form-error").html("Wystąpił błąd.");
		}
	};
	
	doAjax("sens_edit_add_sens", params ,success, error);
}

/*
Edycja sensów
*/
function updateSens(save_button,name,description,sens_name){
	$(save_button).after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	$(save_button).attr("disabled", "disabled");
	
	var params = {
		name: name,
		description: description,
		sens_name: sens_name
	};
	
	var success = function(data){
		$('button#'+sens_name).parent().find('#hidden_text_area').val(description);
		var html = "";
		html += "<b>" + sens_name + ":</b> ";
		html += description;
		html += "<br><span class='sensItemEdit' id=" + sens_name + ">[edytuj opis]</span>";
		$('.sensItemDescription#'+sens_name).html(html);
		ajaxstatus("Edited sense: " + sens_name, "success");
	};
	
	var error = function(code){
		ajaxstatus("Error sense: " + code, "error");
	};
	
	var complete = function(){
		$(save_button).removeAttr("disabled");
		$(".ajax_indicator").remove();
	};
	
	doAjax("sens_edit_update_sens", params, success, error, complete);
}

/*
Usuwanie sensów
*/
function deleteSens(dialog,name,sens_id,sens_name){

	var success = function(data){
		dialog.dialog('close');
		$("#dialog-form-delete-sens").remove();
		getSens(dialog,sens_id,sens_name,0);
		ajaxstatus("Deleted sense: " + name, "success");
	};
	
	var error = function(){
		if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
			$("#delete-sens-form-error").html("Wystąpił błąd.");
		}
	};
	
	doAjax("sens_edit_delete_sens", {name: name}, success, error, complete);
}

/***************************************************************/
/********Obsługa strony*****************************************/
/***************************************************************/
$(function(){
	
	$("span.sensCreate").click(function(){
		createWordDialog();
		return false;
	});
	
	$("span.sensEdit").click(function(){
		var name = $(this).attr('id');		
		editWordDialog(name);
		return false;
	});
	
	$("span.sensDelete").click(function(){
		var name = $(this).attr('id');
		deleteWordDialog(name);
		return false;
	});
	
	$("span.sensDescriptionCreate").live({
		click: function(){
			var name = $(this).attr('id');
			var id = $(this).parent().attr('id');
			createSensDialog(name,id);		
			return false;
		}
	});
	
	$("tr.sensName").live({
		click: function(){
			if (! $(this).hasClass("selected")){
				$("tr.sensName").removeClass("selected");
				$(this).addClass("selected");	
			}
			var this_sens_id = $(this).attr('id');
			var this_sens_name = $(this).find('td.sens_name').text();
			$(".sensEdit").show();
			$(".sensEdit").attr("id",this_sens_name);
			$(".sensDelete").show();
			$(".sensDelete").attr("id",this_sens_name);			
			//ajaxstatus("Ładuję słowo: " + this_sens_name, "loading");
			getSens($(this),this_sens_id,this_sens_name,1);
		}	
	});
	
	$("span.sensItemEdit").live({
		click: function(){
			$(this).parent().parent().find('div.sensItemEditForm').show("");
			$(this).parent().hide("");
		}
	});
	
	$("button.saveSens").live({
		click: function(){
			var name = $(this).parent().find('input').val();
			var description = $(this).parent().find('textarea').val();
			var sens_name = $(this).parent().parent().attr('id');
			updateSens($(this),name,description,sens_name);
			return false;
		}
	});
	
	$("button.discardSens").live({
		click: function(){
			var edit_textarea_value = $(this).parent().find('#edit_text_area').val();
			var hidden_textarea_value = $(this).parent().find('#hidden_text_area').val();
			if(edit_textarea_value == hidden_textarea_value){
				$(this).parent().parent().parent().find('div.sensItemDescription').show("");
				$(this).parent().parent().hide("");
			}
			else{
				closeSensDialog($(this),hidden_textarea_value);
			}
		}
	});
	
	$("button.deleteSens").live({
		click: function(){
			var id = $(this).attr('id');
			var sens_id = $(this).parent().find('div.sens_id').attr('id');
			var sens_name = $(this).parent().find('div.sens_name').attr('id');
			deleteSensDialog(id,sens_id,sens_name);		
			return false;
		}
	});			
});
