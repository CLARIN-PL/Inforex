/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */



/***************************************************************/
/********Obsługa strony*****************************************/
/***************************************************************/
$(function(){

    $(".sensCreate").click(function(){
        createWordDialog();
        return false;
    });

    $(".sensEdit").click(function(){
        var name = $(this).attr('id');
        editWordDialog(name);
        return false;
    });

    $(".sensDelete").click(function(){
        var name = $(this).attr('id');
        deleteWordDialog(name);
        return false;
    });

    $(".sensDescriptionCreate").click(function(){
        var name = $(this).attr('id');
        var id = $("#sensTableItems").find('.selected').attr('id');
        createNewSens(name,id);
    });

    $("#senses_options").on("click", ".sensDelete", function(){
        var name = $(this).attr('id');
        var id = $(this).parent().attr('id');
        createSensDialog(name,id);
        return false;
    });

    $("#sensContainer").on("click", ".sensName",function(){
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
        $("#sense_panel").show();
        //ajaxstatus("Ładuję słowo: " + this_sens_name, "loading");
        getSens($(this),this_sens_id,this_sens_name,1);
    });

    $("#senses_options").on("click", ".sensItemDescription", function(){
        $(this).parent().find('div.sensItemEditForm').toggle();
    });

    $("#senses_options").on("click", ".saveSens", function(){
        var name = $(this).parent().find('input').val();
        var description = $(this).parent().find('textarea').val();
        var sens_name = $(this).parent().parent().parent().attr('id');
        updateSens($(this),name,description,sens_name);
        return false;
    });

    $("#senses_options").on("click", ".discardSens", function(){
        var edit_textarea_value = $(this).parent().find('#edit_text_area').val();
        var hidden_textarea_value = $(this).parent().find('#hidden_text_area').val();
        if(edit_textarea_value == hidden_textarea_value){
            $(this).parent().parent().parent().find('div.sensItemDescription').show("");
            $(this).parent().parent().parent().hide("");
        }
        else{
            closeSensDialog($(this),hidden_textarea_value);
        }
    });

    $("#senses_options").on("click", ".deleteSens", function(){
        var id = $(this).attr('id');
        var sens_id = $(this).parent().find('div.sens_id').attr('id');
        var sens_name = $(this).parent().find('div.sens_name').attr('id');
        console.log(id + ',' + sens_id + ',' + sens_name);
        deleteSensDialog(id,sens_id,sens_name);
        return false;
    });

});


/*
Show Ajax status in ajax_status element
text: show text in Ajax status element
option: loading, success, error
*/
function ajaxstatus($text,$option){
	$(".ajax_status_text").show();
	$(".ajax_status_text").removeClass("loading").removeClass("success").removeClass("error").addClass($option);
	$(".ajax_status_text").html('<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+ "<strong>"+$text+"</strong>");
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
		for (a in data){
			html += "<div class='panel panel-default' style = 'margin-bottom: 5px;'><div class='panel-body'><div class='sensItemDescription' id=" + data[a]['value'] + "><b>" + data[a]['value'] + ":</b> " + data[a]['description'];
				html += "<br><button type = 'button' class='sensItemEdit btn btn-primary adminPanelButton' id=" + data[a]['value'] + ">edit description<sa/button></div>";
				html += "<div class='panel panel-default sensItemEditForm' id=" + data[a]['value'] + " style='display:none; margin-top: 40px;'>";
                html +=  "<div class = 'panel-heading'><b>Editing " + data[a]['value'] + "</b></div>";
				html += "<div class='panel-body'><form>";
				html += "<label class='input' for='sensNameEdit'><b>Lemma:</b></label> <input class='input' type='text' size='50' name='sensNameEdit' value=" + this_sens_name + " disabled='disabled'/><br /><br>";
                html += "<label class='input' for='sensDescriptionEdit'><b>Description:</b></label> <textarea class='input' cols='48' rows='10' id='edit_text_area' name='sensDescriptionEdit'>" + data[a]['description'] + "</textarea><br />"
                html += "<textarea id='hidden_text_area' style='display:none'>" + data[a]['description'] + "</textarea>";

                html += "<button type='button' class='btn btn-primary saveSens' name='saveSens'>Save</button> ";
                html += "<button type='button' class='btn btn-primary discardSens' name='discardSens' title='Without making changes'>Close</button> ";
                html += "<button type='button' class='btn btn-danger deleteSens' id=" + data[a]['value'] + " name='deleteSens'>Delete</button>";
                html += "<div class='sens_id' id=" + sens_id + "></div><div class='sens_name' id=" + this_sens_name + "></div>";
				html += "</form></div> ";

				html += "</div></div></div>";
		}

		$("#sensDescriptionContainer").show();
		$("#sensDescriptionList").html(html);
        $(".senses_actions").attr("id", sens_id);
        $(".sensDescriptionCreate").attr("id", this_sens_name);
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

	$("#create_lemma_modal").modal('show');

    $( "#create_lemma_form" ).validate({
        rules: {
            create_lemma_word: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'sens_edit',
                        mode: 'create'
                    }
                }
            }
        },
        messages: {
            create_lemma_word: {
                required: "Lemma must have a name.",
				remote: "This lemma already exists"
            }
        }
    });

    $( ".confirm_create_lemma" ).unbind( "click" ).click(function() {
        if($('#create_lemma_form').valid()) {

            var data = {
                wordname: $("#create_lemma_word").val()
            }

            var success = function(data){
                getWords();
                ajaxstatus("Added lemma: " + data.wordname, "success");
                $('#create_lemma_modal').modal('hide');
            };

            var error = function(code){
                if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
                    $("#create-word-form-error").html("Wystąpił błąd.");
                }
            };

            doAjax("sens_edit_add_word", data, success, error);
        }
    });
}

/*
Okno do edycji słów
*/
function editWordDialog(name){

    $( "#edit_lemma_form" ).validate({
        rules: {
            edit_lemma_word: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'sens_edit',
                        id: function(){
                            return $("#sensTableItems").find('.selected').attr('id');
                        },
                        mode: 'edit'
                    }
                }
            }
        },
        messages: {
            edit_lemma_word: {
                required: "Lemma must have a name.",
                remote: "This lemma already exists"
            }
        }
    });

	$("#edit_lemma_word").val(name);
    $("#edit_lemma_modal").modal('show');

    $( ".confirm_edit_lemma" ).unbind( "click" ).click(function() {
        if($('#edit_lemma_form').valid()) {

            var newwordname = $("#edit_lemma_word").val();

            var params = {
                newwordname: newwordname,
                oldwordname: name
            };

            var success = function(data){
                var sens_num = data['sens_num'];
                getWords();
                ajaxstatus("Edited lemma: " + params.newwordname, "success");
                $(".sensEdit").hide();
                $(".sensDelete").hide();
                $('#edit_lemma_modal').modal('hide');

            };

            var error = function(code){
                if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
                    $("#edit-word-form-error").html("Wystąpił błąd.");
                }
            };

            doAjax("sens_edit_update_word", params, success, error);
        }
    });
}

/*
Okno do usuwania słów
*/
function deleteWordDialog(name){
    $("#delete_lemma_modal").modal('show');
    $("#delete_lemma_word").val(name);

    $( ".confirm_delete_lemma" ).unbind( "click" ).click(function() {
        var success = function(data){
            $("#sensDescriptionContainer").hide();
            getWords();
            $(".sensEdit").hide();
            $(".sensDelete").hide();
            $('#delete_lemma_modal').modal('hide');

            ajaxstatus("Deleted lemma: " + name, "success");
        };

        var error = function(){
            if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
                $("#delete-word-form-error").html("Wystąpił błąd.");
            }
        };

        doAjax("sens_edit_delete_word", {name: name}, success, error);
    });
}


function createNewSens(name, id){
    $("#create_sens_modal").modal('show');
    $("#sens_name").html(name+" -");


    $( "#create_sens_form" ).validate({
        rules: {
            create_sens_name: {
                required: true,
                remote: {
                    url: "index.php",
                    type: "post",
                    data: {
                        ajax: 'administration_validation',
                        type: 'new_sense',
                        name: name + "-",
                        id: id
                    }
                }
            }
        },
        messages: {
            create_lemma_word: {
                required: "Sens must have a name."
            },
            create_sens_name: {
                remote: "This sense already exists."
            }
        }
    });

    $( ".confirm_create_sens" ).unbind( "click" ).click(function() {
        if($('#create_sens_form').valid()) {

            var sensnum = $("#sensnum").val();

            var params = {
                sensname: name,
                sensid: id,
                sensnum: sensnum,
                description: $("#create_sens_description").val()
            };

            var success = function(data){
                ajaxstatus("Added sense: " + params.sensname + "-" + sensnum, "success");
                $('#create_sens_modal').modal('hide');
                getSens($(this),id,name,1);
            };

            var error = function(){
                $('#create_sens_modal').modal('hide');
                if(code == "ERROR_APPLICATION" || code == "ERROR_AUTHORIZATION"){
                    $("#create-sens-form-error").html("Wystąpił błąd.");
                }
            };

            doAjax("sens_edit_add_sens", params ,success, error);
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
		getWords();as
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
		html += "<button type = 'button' class='sensItemEdit btn btn-primary adminPanelButton' id=" + sens_name + ">edit description<sa/button></div>";
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
	
	doAjax("sens_edit_delete_sens", {name: name}, success, error);
}
