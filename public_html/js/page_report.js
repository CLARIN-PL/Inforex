/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$("#edit").markItUp(mySettings);

	$("#toogleFlags").click(function(event){
        event.isPropagationStopped();
		$("#col-flags").toggle();
		if ( $("#col-flags").is(":visible") ) {
			var className = null;
            $.each($(".col-main").attr("class").split(" "), function(index, item){
				if ( item.startsWith("col-md-") ){
					className = item;
				}
			});
            if ( className != null ){
            	var parts = className.split("-");
            	var num = parseInt(parts[2]);
                $(".col-main").removeClass("col-md-" + num);
                $(".col-main").addClass("col-md-" + (num-1));
			}
            $.cookie("flags_active", "1");
        } else {
            $.each($(".col-main").attr("class").split(" "), function(index, item){
                if ( item.startsWith("col-md-") ){
                    className = item;
                }
            });
            if ( className != null ){
                var parts = className.split("-");
                var num = parseInt(parts[2]);
                $(".col-main").removeClass("col-md-" + num);
                $(".col-main").addClass("col-md-" + (num+1));
            }
            $.cookie("flags_active", "0");
		}
		return false;
    });

    $("#toogleConfig").click(function(event){
        event.isPropagationStopped();
        if ( $("#col-config").size() == 0 ){
        	return;
		}
        $("#col-config").toggle();
        if ( $("#col-config").is(":visible") ) {
            var className = null;
            $.each($(".col-main").attr("class").split(" "), function(index, item){
                if ( item.startsWith("col-md-") ){
                    className = item;
                }
            });
            if ( className != null ){
                var parts = className.split("-");
                var num = parseInt(parts[2]);
                $(".col-main").removeClass("col-md-" + num);
                $(".col-main").addClass("col-md-" + (num-3));
            }
            $.cookie("config_active", "1");
        } else {
            $.each($(".col-main").attr("class").split(" "), function(index, item){
                if ( item.startsWith("col-md-") ){
                    className = item;
                }
            });
            if ( className != null ){
                var parts = className.split("-");
                var num = parseInt(parts[2]);
                $(".col-main").removeClass("col-md-" + num);
                $(".col-main").addClass("col-md-" + (num+3));
            }
            $.cookie("config_active", "0");
        }
        return false;
    });
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

/**
 * Handle delete document option
 */

$(function(){
    $(".delete_document_button").click(function(){
        deleteDocument($(this).attr('report_id'), $(this).attr('corpus_id'));
    });
})

function deleteDocument(documentId, corpusId){
    $("#deleteDocumentTitle").text($('.document_title').text());

	$( ".confirmDeleteDocument" ).unbind( "click" ).click(function() {
		$(".delete_info").hide();
		$(".delete_loader").show();
		$(".confirmDeleteDocument").prop("disabled", true);
		var params = {
            report_id: documentId,
		};

		var success = function(data){
			var new_url = window.location.href.slice(0,window.location.href.indexOf('?') + 1);
			new_url += 'page=corpus_documents&corpus=' + corpusId;
			document.location = new_url;
            $("#deleteCorpus").modal('hide');
		};

        var complete = function(){
            $(".delete_info").show();
            $(".delete_loader").hide();
            $(".confirmDeleteDocument").removeProp("disabled");
        };

		doAjax("report_delete_document", params, success, null, complete);
    });
}