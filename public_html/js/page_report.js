$(function(){
	$("#edit").markItUp(mySettings);
	$(".token").removeAttr('title');
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
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { 
							ajax : "report_delete_event_slot", 
							slot_id : slotId
						},				
						success : function(data){
							ajaxErrorHandler(data,
								function(){							
									$('#eventSlotsTable td[slotid="'+slotId+'"]').parent().remove();
									$slotCount = $('#eventTable a[eventid="'+eventId+'"]').parent().next().next().next();
									$slotCount.text(parseInt($slotCount.text())-1);
									cancelAddAnnotation();
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									deleteEventSlot(handler);
								}
							);								
						}
					});	
				
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
	var xPosition = $("#flagsContainer").offset().left - $(window).scrollLeft() + $("#flagsContainer").width() - 25;
	var yPosition = $("#flagsContainer").offset().top - $(window).scrollTop() + $("#flagsContainer").height();		
	$dialogBox = $($("#flagStates").html()).dialog({
		modal : true,
		title : 'Change state',
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
	$dialogBox.dialog("option", "position",[xPosition-$dialogBox.width(), yPosition]);
	$dialogBox.find("span.flagState").click(function(){
		$flag = $(this);
		var _data = { 
			ajax : "report_set_report_flags", 
			report_id : $element.attr('report_id'),
			cflag_id : $element.attr('cflag_id'),
			flag_id : $(this).attr('flag_id')
		}
		$.ajax({
			async : false,
			url : "index.php",
			dataType : "json",
			type : "post",
			data : _data,				
			success : function(data){
				ajaxErrorHandler(data,
					function(){ 
						$element.children("img:first").attr('src','gfx/flag_'+_data.flag_id+'.png');
						$element.attr('title',$element.attr('title').split(":")[0]+": "+$flag.attr('title'));
						$dialogBox.dialog("close");
					}, 
					function(){
						$dialogBox.dialog("close");
						setFlag($element);
					}
				);
			}
		});
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
	dialog.after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	dialog.attr("disabled", "disabled");
	$.ajax({
			type: 	'POST',
			url: 	"index.php",
			data:	{ 	
						ajax: "report_delete_document", 
						report_id: report_id
					},						
			success: function(data){
						if (data['success']){
							dialog.removeAttr("disabled");
							$(".ajax_indicator").remove();
							var new_url = window.location.href.slice(0,window.location.href.indexOf('?') + 1);
							new_url += 'page=browse&corpus=' + corpus_id;
							document.location = new_url;
						}else{
							dialog.removeAttr("disabled");
							$(".ajax_indicator").remove();
							$("#delete-document-form-error").html(data['error']);							
						}
					},
			error: function(request, textStatus, errorThrown){	
						dialog_error("<b>HTML result:</b><br/>" + request.responseText);		
					},
			dataType:"json"						
	});
}