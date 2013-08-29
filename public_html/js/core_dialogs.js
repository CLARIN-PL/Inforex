/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

function dialog_error(text, error_code, errorCallbackOk, errorCallbackClose){
	errorCallbackClose = errorCallbackClose || errorCallbackOk;
	
	var html = '<div id="dialog_error" title="Operation could not be completed" style="display: none; " style="ui-state-error">'
	+ '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+text+'</p>'
    + '</div>';
	$(document.body).prepend(html);
	$("#dialog_error").dialog({
		autoOpen: true,
		bgiframe: true,
		resizable: false,
		height:240,
		width: 400,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		},
		buttons: {
			'Ok': function() {
				$(this).dialog('close');
				if(errorCallbackOk && $.isFunction(errorCallbackOk)){
					errorCallbackOk(error_code);
				}
			}
		},
		close: function(event, ui) { 
			$("#dialog_error").remove();
			if(errorCallbackClose && $.isFunction(errorCallbackClose)){
				errorCallbackClose(error_code);
			}
		}
	});	
}

function dialog_yes_no(text, header, func_yes, func_no){
	var html = '<div id="dialog_yes_no" title="'+header+'" style="display: none; " style="ui-state-error">'
	+ '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+text+'</p>'
    + '</div>';
	$(document.body).prepend(html);
	$("#dialog_yes_no").dialog({
		autoOpen: false,
		bgiframe: true,
		resizable: false,
		height:140,
		modal: true,
		overlay: {
			backgroundColor: '#000',
			opacity: 0.5
		},
		buttons: {
			'Usuń': function() {
				$(this).dialog('close');
				_delete_action();		
				if (func_yes!=null) func_yes();
			},
			'Anuluj': function() {
				$(this).dialog('close');
				if (func_no!=null) func_no();
			}
		},
		close: function(event, ui) { 
			$("#dialog_yes_no").remove();			
		}
	});	
}