/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$("#edit").markItUp(mySettings);
});

function deleteEventSlot(handler){
	var $eventHandler = $(handler);
	var slotId = $eventHandler.prev().prev().prev().attr('slotid');
	var eventId = $("#eventDetailsId").text();
	var xPosition = $("#flagsContainer").offset().left-$(window).scrollLeft();
	var yPosition = $("#flagsContainer").offset().top - $(window).scrollTop();
	
	$dialogBox = 
		$('<div class="deleteDialog annotations">Czy usunąć slot #'+slotId+'?</div>')
		.dialog({
			modal : true,
			title : 'Potwierdzenie usunięcia',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					
					var success = function(data){
						$('#eventSlotsTable td[slotid="'+slotId+'"]').parent().remove();
						$slotCount = $('#eventTable a[eventid="'+eventId+'"]').parent().next().next().next();
						$slotCount.text(parseInt($slotCount.text())-1);
						cancelAddAnnotation();
					};
					
					var login = function(){
						deleteEventSlot(handler);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					
					doAjaxSync("report_delete_event_slot", {slotId: slotId}, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}

		});
		$dialogBox.dialog("option", "position",[xPosition- $dialogBox.width(), yPosition]);	
}


//report flags management
$(function(){
	$("span.corporaFlag").click(function(){
		setFlag($(this));
	});
});

function setFlag($element){
	var xPosition = $element.offset().left-210;
	var yPosition = $element.offset().top;
	$dialogBox = $($("#flagStates").html()).dialog({
		modal : true,
		title : $element.text(),
		width : '200px', 
		buttons : {
			Cancel: function() {
				$dialogBox.dialog("close");
			}
		},
		close: function(event, ui) {
			$dialogBox.dialog("destroy").remove();
			$dialogBox = null;
		}			
	});
	$dialogBox.dialog("option", "position",[xPosition, yPosition]);
	$dialogBox.find("span.flagState").click(function(){
		$flag = $(this);
		var _data = { 
			report_id : $element.attr('report_id'),
			cflag_id : $element.attr('cflag_id'),
			flag_id : $(this).attr('flag_id')
		}
		
		var success = function(data){
			$element.children("img:first").attr('src','gfx/flag_'+_data.flag_id+'.png');
			$element.attr('title',$element.attr('title').split(":")[0]+": "+$flag.attr('title'));
		};
	
		var login = function(){
			setFlag($element);
		};
		
		var complete = function(){
			$dialogBox.dialog("close");
		};
		
		doAjaxSync("report_set_report_flags", _data, success, null, complete, null, login);
	});
}

//report options management
$(function(){
	$("span.optionsDocument").click(function(){
		deleteDocumentDialog($(this).attr('report_id'),$(this).attr('corpus'));
	});
});

function deleteDocumentDialog(report_id,corpus_id){
	$("body").append(''+
			'<div id="dialog-form-delete-document" title="Delete document" style="">'+
			'	<div style="float: left; text-align: right;margin-bottom: 5px; line-height: 1em">Delete document id:'+report_id+'?</div>'+
			'   <br><span style="color: red; margin-left: 70px" id="delete-document-form-error"></span>'+	
			'</div>');
	$("#dialog-form-delete-document").dialog({
		autoOpen: true,
		width: 280,
		modal: true,
		buttons: {
			'Yes': function() {
				deleteDocument($(this),report_id,corpus_id);
			},
			'No': function() {
				$(this).dialog('close');
			}
		},
		close: function() {
			$("#dialog-form-delete-document").remove();
		}
	});	
}

function deleteDocument(dialog,report_id,corpus_id){
	dialog.after("Deleting document, please wait ...<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	$(".ui-dialog-buttonpane button").attr("disabled",true)
	$(".ui-dialog-buttonpane button").hide();
	status_processing("Usuwanie dokumentu");
	
	var success = function(data){
		var new_url = window.location.href.slice(0,window.location.href.indexOf('?') + 1);
		new_url += 'page=browse&corpus=' + corpus_id;
		document.location = new_url;
	};
	
	var error = function(code){
		if(code != "ERROR_TRANSMISSION"){
			$("#delete-document-form-error").html(data['error']);
		}
	};
	
	var complete = function(){
		$(".ajax_indicator").remove();
		$(".ui-dialog-buttonpane button").removeAttr("disabled");
		$(".ui-dialog-buttonpane button").show();
		status_hide();
	};
	
	doAjax("report_delete_document", {report_id: report_id}, success, error);
}