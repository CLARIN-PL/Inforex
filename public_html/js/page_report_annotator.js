/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;
var hiddenAnnotations = 0;
var anaphora_target_n = 1;

// zmienna określająca globalne (dla perspektywy) zaznaczenie tekstu
var global_selection = null;
var prevent_from_annotation_selection = false;
var temporal_annotation_wrap_id = 0;

//obiekt trybu dodawania relacji pomiedzy anotacjami
var AnnotationRelation = Object();
AnnotationRelation.relationMode = false;
AnnotationRelation.types = []; //array of available types
AnnotationRelation.target_type = {}; //target_type.relation_type=[X,Y,...] existing relations relation_type between source_id and target_id=X,Y,..

//obiekt trybu edycji slotow zdarzenia
var AnnotationEvent = Object();
AnnotationEvent.relationMode = false; //edycja zawartosci slotow - wybor anotacji
AnnotationEvent.initMode = false; //edycja slotow

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){

	$("chunk[type=p]").css("display", "block").css("margin", "5px");

	/**
	 * Po zwolnieniu przycisku myszy utworz obiekt zaznaczenia.
	 */
	$("#content").mouseup(function(){
		prevent_from_annotation_selection = getSelText() != "";
		global_selection = new Selection();
		if ( !global_selection.isValid ){
			global_selection = null;
		}		
	});

	/**
	 * Zmiana kategorii relacji.
	 */
	$(".relation_type_switcher").hover(function() {  
	    	$(this).css("cursor", "pointer");
	    	$(this).attr("title", "kliknij, aby zmienić typ relacji");
	}); 
	
	$(".relation_type_switcher").click(function(e){
		e.preventDefault();
		rel_id = $(this).attr("id");
		sourcegroupid = $(this).parent().find('td[sourcegroupid]').attr("sourcegroupid");
		sourcesubgroupid = $(this).parent().find('td[sourcesubgroupid]').attr("sourcesubgroupid");
		targetgroupid = $(this).parent().find('td[targetgroupid]').attr("targetgroupid");
		targetsubgroupid = $(this).parent().find('td[targetsubgroupid]').attr("targetsubgroupid");
		getRelationsTypes(rel_id, sourcegroupid, sourcesubgroupid, targetgroupid, targetsubgroupid);
	});

	/**
	 * Akcja utworzenia anotacji po kliknięciu w typ anotacji.
	 */
	$("a.an").click(function(){
		if ( !global_selection || !global_selection.isValid ){
			alert("Zaznacz tekst");
		}else{
			add_annotation(global_selection, 
					$(this).attr("value"),
					getNewAnnotationStage());		
			global_selection.clear();
			global_selection = null;
		}
		return false;
	});

	//---------------------------------------------------------
	//Obsługa pada
	//---------------------------------------------------------

	$("a.short_all").click(function(){
		$(this).closest("ul").find("li.notcommon").toggleClass('hidden');
	});
	
	//---------------------------------------------------------
	//Obsługa relacji
	//---------------------------------------------------------
	$("#relation_add").click(function(){
		add_relation_init();
	});

	$("#relation_cancel").click(function(){	
		cancel_relation();
		get_relations();
	});
	
	$("#relation_type").change(function(){
		block_existing_relations();
	});

	$("div.deleteRelation").live('click',function(){
		delete_relation(this);
	});


	//------obsluga zdarzen
	$("#eventGroups").change(function(){
		updateEventGroupTypes();
	});
	
	$("#addEvent").click(function(){
		addEvent();
	});
	
	$("#deleteEvent").click(function(){
		deleteEvent();
	});
	
	$("#eventTable a[typeid]").live("click", function(){
		editEvent(this);
	});
	
	$("#cancelEvent").click(function(){
		cancelEvent();
	});
	
	$("#addEventSlot").click(function(){
		addEventSlot();
	});
	
	$(".eventSlotAnnotation.emptySlot").live('click', function(){
		initEventSlotAnnotation(this);
	});

	$(".deleteEventSlot").live('click', function(){
		deleteEventSlot(this);
	});
	
	$("#cancelAddAnnotation").click(function(){
		cancelAddAnnotation();
	});

	$(".deleteAnnotation").live("click",function(){
		deleteAnnotation($(this).attr('annotation_id'));
		$(this).parent().remove();
	});

	updateEventGroupTypes();
	
	setupAnnotationMode();
});


function getRelationsTypes(rel_id, sourcegroupid, sourcesubgroupid, targetgroupid, targetsubgroupid){
	var params = {
			relation_id : rel_id,
			sourcegroupid : sourcegroupid,
			sourcesubgroupid : sourcesubgroupid,
			targetgroupid : targetgroupid,
			targetsubgroupid : targetsubgroupid
	};
	
	var success = function(data){
		var dialogHtml = 
			'<div class="relationsSwitchDialog">'+
				'<table class="tablesorter">'+
					'<thead>'+
						'<tr>'+
							'<th>id</th>'+
							'<th>name</th>'+
							'<th>active</th>'+
						'</tr>'+
					'</thead>'+
					'<tbody>';
		
		$.each(data,function(index,value){
			dialogHtml += 
				'<tr class="relations_type" id="'+value.id+'">'+
					'<td style="color: grey; text-align: right">'+value.id+'</td>'+
					'<td>'+value.name+'</td>'+
					'<td><input name="setRelationTypes" type="radio" value="'+value.id+'" '+(value.active ? 'checked="checked"' : '')+'/></td>'
				'</tr>';
		});
		
		dialogHtml += '</tbody></table></div>';
		var $dialogBox = $(dialogHtml).dialog({
			modal : true,
			height : 'auto',
			width : 'auto',
			title : 'Relation types:',
			buttons : {
				Close: function() {
					$dialogBox.dialog("close");
				},
				Save: function() {
					var params = {
						relation_id : rel_id,
						relation_type : $("input[name='setRelationTypes']:checked").val()
					};
					
					var success = function(data){
						if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
						document.location = document.location;
					};
					
					doAjaxSync("report_update_relations_type", params, success);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});	
	};
	
	var login = function(){
		getRelationsTypes(rel_id, sourcegroupid, sourcesubgroupid, targetgroupid, targetsubgroupid);
	};
	
	doAjaxSyncWithLogin("report_get_relations_types", params, success, login)
		
}

//------obsluga zdarzen
function updateEventGroupTypes(){
	$("#addEvent").attr('disabled','disabled');
	var params = {
		group_id : $("#eventGroups :selected:first").attr('groupid')
	};
	
	var success = function(data){
		$egt = $("#eventGroupTypes").empty();
		contentStr = "";			
		$.each(data, function(index, value){
			contentStr+='<option value="'+value.name+'" typeid="'+value.event_type_id+'" >'+value.name+'</option>';
		});
		$egt.html(contentStr);
		$("#addEvent").attr('disabled','');
	
	};
	
	doAjaxSync("report_get_event_group_types", params, success);
}

function addEvent(){
	var $eventGroup = $("#eventGroups :selected:first");
	var $eventType = $("#eventGroupTypes :selected:first");
	var groupName = $eventGroup.val();
	var typeName = $eventType.val();
	var typeId = $eventType.attr('typeid');
	$("#addEvent").attr('disabled','disabled');
	$("#eventGroups").attr('disabled','disabled');
	$("#eventGroupTypes").attr('disabled','disabled');
	
	var params = {
		type_id : $eventType.attr('typeid'),
		report_id : $("#report_id").val()
	};
	
	var success = function(data){
		$("#eventTable tbody").append('<tr><td><a href="#" eventid="'+data.event_id+'" typeid="'+typeId+'">#'+data.event_id+'</a></td><td>'+groupName+'</td><td>'+typeName+'</td><td>0</td></tr>');
	};
	
	var login = function(){
		addEvent();
	};
	
	var complete = function(){
		$("#addEvent").attr('disabled','');
		$("#eventGroups").attr('disabled','');
		$("#eventGroupTypes").attr('disabled','');
	};
	
	doAjaxSync("report_add_event", params, success, null, complete, null, login);
	
}

function editEvent(handler){
	AnnotationEvent.initMode = true;
	$("#cell_annotation_wait").show();
	$("#rightPanelAccordion").hide();
	$("#rightPanelEventEdit").hide();
	
	var $eventHandler = $(handler);
	var typeId = $eventHandler.attr('typeid');
	var eventId = $eventHandler.attr('eventid');
	$("#addEventSlot").attr('disabled','disabled');
	$("#eventDetailsId").text(eventId).attr('eventid',eventId);
	$("#eventDetailsType").text( $eventHandler.parent().next().text()).attr('typeid',typeId);
	$("#eventSlotsTable tbody").empty();
	
	var params = {
		type_id : typeId
	};
	
	var success = function(data){
		$ets = $("#eventTypeSlots").empty();
		var contentStr = "";			
		$.each(data, function(index, value){
			contentStr+='<option value="'+value.name+'" typeid="'+value.event_type_slot_id+'" >'+value.name+'</option>';
		});
		$ets.html(contentStr);
		$("#addEventSlot").attr('disabled','');
	};
	
	doAjaxSync("report_get_event_type_slots", params, success);
	
	var params = {
		event_id : eventId	
	};
	
	var success = function(data){
		var contentStr = "";			
		$.each(data, function(index, value){
			contentStr+= 
			'<tr>'+
				'<td slotid="'+value.slot_id+'" typeid="'+value.slot_type_id+'">#'+value.slot_id+'</td>'+
				'<td>'+value.slot_type+'</td>';
			if (value.annotation_id){
				contentStr+=
				'<td class="eventSlotAnnotation">'+
					'<span class="'+value.annotation_type+'" title="an#'+value.annotation_id+':'+value.annotation_type+'">'+value.annotation_text+'</span>'+
				'</td>';
			}
			else {
				contentStr+='<td class="eventSlotAnnotation emptySlot" style="text-align:center; cursor:pointer"><b>+</b></td>';
			}
			contentStr+='<td class="deleteEventSlot" style="text-align:center; cursor:pointer"><b>X</b></td></tr>'; 
						 
		});
		$("#eventSlotsTable tbody").html(contentStr);
		$("#rightPanelEventEdit").show();
		$("#cell_annotation_wait").hide();
		cancelAddAnnotation();
	};
	
	doAjaxSync("report_get_event_slots", params, success);		
}

function cancelEvent(){
	$("#rightPanelEventEdit").hide();
	$("#rightPanelAccordion").show();
	AnnotationEvent.initMode = false;
	$('#content span').removeClass('relationAvailable').removeClass("relationGrey");
}

function deleteEvent(){
	var eventId = $("#eventDetailsId").text();
	$dialogBox = 
		$('<div class="deleteDialog annotations">Czy usunąć zdarzenie #'+eventId+'?</div>')
		.dialog({
			modal : true,
			title : 'Potwierdzenie usunięcia',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var params = {
						event_id : eventId
					};
					
					var success = function(data){
						cancelEvent();
						$('#eventTable a[eventid="'+eventId+'"]').parent().parent().remove();
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						$dialogBox.dialog("close");
						deleteEvent();
					};
					
					doAjaxSyncWithLogin("report_delete_event", params, success);
				
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}

function addEventSlot(){
	var eventId = $("#eventDetailsId").attr('eventid');
	var slotTypeId = $("#eventTypeSlots :selected:first").attr('typeid');
	var slotType = $("#eventTypeSlots :selected:first").text();
	$("#addEventSlot").attr('disabled','disabled');
	$("#eventTypeSlots").attr('disabled','disabled');
	
	var params = {
		event_id : eventId,
		type_id : slotTypeId
	};
	
	var success = function(data){
		$("#eventSlotsTable tbody").append('<tr><td slotid="'+data.slot_id+'" typeid="'+slotTypeId+'">#'+data.slot_id+'</td><td>'+slotType+'</td><td class="eventSlotAnnotation emptySlot" style="text-align:center; cursor:pointer"><b>+</b></td><td class="deleteEventSlot" style="text-align:center;cursor:pointer "><b>X</b></td></tr>');
		$("#addEventSlot").attr('disabled','');
		$("#eventTypeSlots").attr('disabled','');
		$slotCount = $('#eventTable a[eventid="'+eventId+'"]').parent().next().next().next();
		$slotCount.text(parseInt($slotCount.text())+1);
	};
	
	var login = function(){
		addEventSlot();
	};
	
	doAjaxSyncWithLogin("report_add_event_slot", params, success, login);
}

function deleteEventSlot(handler){
	var $eventHandler = $(handler);
	var slotId = $eventHandler.prev().prev().prev().attr('slotid');
	var eventId = $("#eventDetailsId").text();
	var xPosition = $(handler).offset().left-$(window).scrollLeft();
	var yPosition = $(handler).offset().top - $(window).scrollTop();
	
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
					
					var params = {
						slot_id : slotId		
					};
					
					var success = function(data){
						$('#eventSlotsTable td[slotid="'+slotId+'"]').parent().remove();
						$slotCount = $('#eventTable a[eventid="'+eventId+'"]').parent().next().next().next();
						$slotCount.text(parseInt($slotCount.text())-1);
						cancelAddAnnotation();
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						$dialogBox.dialog("close");
						deleteEventSlot(handler);
					};
					
					doAjaxSyncWithLogin("report_delete_event_slot", params, success, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}

		});
		$dialogBox.dialog("option", "position",[xPosition- $dialogBox.width(), yPosition]);	
}

function initEventSlotAnnotation(handler){
	if (AnnotationEvent.relationMode) return false;
	else AnnotationEvent.relationMode = true;
	var $eventHandler = $(handler);
	var slotId = $eventHandler.prev().prev().attr('slotid');
	$eventHandler.parent().addClass("hightlighted");
	$("#addAnnotationContainer").show();
	$('#content span').addClass('relationAvailable').removeClass('relationGrey');
	AnnotationEvent.handler = handler;
}

function updateEventSlotAnnotation(annotationObj){
	var slotId = $(AnnotationEvent.handler).prev().prev().attr('slotid');
	var annotationId = $(annotationObj).attr('id').replace("an","");
	var annotationType = $(annotationObj).attr('title').split(":")[1];
	var annotationText = $(annotationObj).text();
	
	var params = {
		slot_id : slotId,
		annotation_id : annotationId
	};
	
	var success = function(data){
		$(AnnotationEvent.handler).removeClass("emptySlot").attr('style','').html(
			'<span class="'+annotationType+'" title="an#'+annotationId+':'+annotationType+'">'+annotationText+'</span>'
		);
		cancelAddAnnotation();
	};
	
	var login = function(){
		updateEventSlotAnnotation(annotationObj);		
	};		
}

function cancelAddAnnotation(){
	$("#addAnnotationContainer").hide();
	AnnotationEvent.relationMode = false;
	$('#content span').removeClass('relationAvailable').addClass("relationGrey");
	$.each( $(".eventSlotAnnotation span"), function(index, value){
		$("#"+$(value).attr('title').split(":")[0].replace("#","")).removeClass("relationGrey");
	});
	
	$("#eventSlotsTable tr").removeClass("hightlighted");
}

function block_existing_relations(){
	var params = {
		annotation_id : _wAnnotation._annotation.id,
		relation_type_id : $("#relation_type").children(":selected:first").data('id')
	};
	
	var success = function(data){
		AnnotationRelation.types = [];
		$.each(data,function(index, value){
			AnnotationRelation.types.push(value[0].toLowerCase());
		});
	};
	
	doAjaxSync("report_get_annotation_types", params, success);
		
	$annotations = $("#content span:not(.token)");
	$annotations.addClass("relationGrey");
	$.each(AnnotationRelation.types,function(index, value){
		$annotations.filter("."+value).removeClass("relationGrey").addClass("relationAvailable");
	});	
	selectedType = $("#relation_type").children(":selected:first").val();
	if (AnnotationRelation.target_type[selectedType]){
		$.each(AnnotationRelation.target_type[selectedType], function(index, value){
			$annotations.filter("#an"+value).addClass("relationGrey").removeClass("relationAvailable");
		});
	}
}

function get_relations(){
	if (_wAnnotation && _wAnnotation._annotation){
		sourceObj = _wAnnotation._annotation;
		AnnotationRelation.target_type = {};
		$("#cell_annotation_wait").show();
		$("#rightPanelAccordion").hide();
		$("#rightPanelEdit").hide();
		
		var params = {
			annotation_id : sourceObj.id
		};
		
		var success = function(data){
			$("#relation_table > tbody tr").remove();
			$table = $("#relation_table");
			
			$("#content span:not(.token)").addClass("relationGrey");
			$.each(data, function(index, value){
				$('<tr>'+
						'<td>'+value.name+'</td>'+
						'<td><span class="'+value.type+'" title="an#'+value.target_id+':'+value.type+'">'+value.text+'</span></td>'+
						'<td><div id="relation'+value.id+'"  class="deleteRelation"><b>X</b></div></td>'+
				  '</tr>').appendTo($table);
				if (AnnotationRelation.target_type[value.name]){
					AnnotationRelation.target_type[value.name].push(value.target_id);
				}
				else {
					AnnotationRelation.target_type[value.name] = [];
					AnnotationRelation.target_type[value.name].push(value.target_id);
				}
				$("#an"+value.target_id).removeClass("relationGrey");
				
			});
			$("#cell_annotation_wait").hide();
			$("#rightPanelEdit").show();
		};
		
		var login = function(){
			get_relations();
		};
		
		doAjaxSyncWithLogin("report_get_annotation_relations", params, success, login);
	}
}

function add_relation_init(){
	AnnotationRelation.types = [];
	
	var params = {
		annotation_id : _wAnnotation._annotation.id		
	};
	
	var success = function(data){
		AnnotationRelation.relationMode = true; //global variable in page_report_annotator.js
		$("#relation_add").hide();
		$("#relation_select").show();
		$listContainer = $("#relation_type").empty();//.append('<option style="display:none"></option>');
		$.each(data, function(index, value){
			$('<option value="'+value.name+'">'+value.name+'</option>').data(value).appendTo($listContainer);
		});
		block_existing_relations();
	};
	
	var login = function(){
		add_relation_init();
	};
	
	doAjaxSyncWithLogin("report_get_annotation_relation_types", params, success, login);
}

function add_relation(spanObj){
	sourceObj = _wAnnotation._annotation;
	targetObj = new Annotation(spanObj);
	relationTypeId = $("#relation_type").children(":selected:first").data('id');
	
	var params = {
		source_id : sourceObj.id,
		target_id : targetObj.id,
		relation_type_id : relationTypeId
	};
	
	var success = function(data){
		cancel_relation();
		get_relations();
		if($("#an" + targetObj.id).prev().hasClass('relin')){
			var relin = $("#an" + targetObj.id).prev("sup");
			var target_n = $(relin).text();
			$(relin).attr("title",$(relin).attr("title")+" "+data['relation_name']);
			add_sup_rel(sourceObj.id, targetObj.id, targetObj.id, sourceObj.id, target_n, data['relation_name']);				
		}
		else{
			$("#an" + targetObj.id).before("<sup class='relin' targetsubgroupid="+ $("#an" + targetObj.id).attr('subgroupid')+
															" targetgroupid="+ $("#an" + targetObj.id).attr('groupid')+
															" title=" + data['relation_name'] +
															">"+anaphora_target_n+"</sup>");								
			add_sup_rel(sourceObj.id, targetObj.id, targetObj.id, sourceObj.id, anaphora_target_n, data['relation_name']);
			anaphora_target_n++;
		}		
	};
	
	var login = function(){
		add_relation(spanObj);
	};
	
	doAjaxSyncWithLogin("report_add_annotation_relation", params, success, login);			
}

function add_sup_rel(source_id, target, target_id, source_id, target_n, relation_name){
	$("#an" + source_id).after("<sup class='rel' target="+target+
				" targetgroupid="+ $("#an" + target_id).attr('groupid')+
				" targetsubgroupid="+ $("#an" + target_id).attr('subgroupid')+
				" sourcesubgroupid="+ $("#an" + source_id).attr('subgroupid')+
				" sourcegroupid="+ $("#an" + source_id).attr('groupid')+
				" title=" + relation_name +
				">"+(relation_name=='Continous' ? "⇢" : "↷" )
				+target_n+"</sup>");
}

function delete_relation(deleteHandler){
	relationId = $(deleteHandler).attr("id").replace("relation","");
	xPosition = $(deleteHandler).offset().left-$(window).scrollLeft();
	yPosition = $(deleteHandler).offset().top - $(window).scrollTop();
	
	
	$relation = $("#relation"+relationId);
	relationName = $relation.parent().prev().prev().text();
	$relationSrc = $("#content span.selected:first");
	$relationSrcTxt = $('<span class="'+$relationSrc.attr('title').split(":")[1]+'">'+$relationSrc.text()+'</span>');
	$relationDstTxt = $($relation.parent().prev().html()).removeAttr('title');
	$dialogBox = 
		$('<div class="deleteDialog annotations">Czy usunąć relację "'+relationName+'" pomiędzy <br/></div>')
		.append($relationSrcTxt)
		.append("<br/>oraz<br/>")
		.append($relationDstTxt)
		.dialog({
			modal : true,
			title : 'Potwierdzenie usunięcia',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var params = {
						relation_id: relationId
					};
					
					var success = function(data){
						cancel_relation();
						get_relations();
					};
					
					var login = function(){
						$dialogBox.dialog("close");
						delete_relation(deleteHandler);	
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
						delete_anaphora_links(relationName, $relationSrc.attr("id"), $($relation.parent().prev().html()).attr('title').split(":")[0].replace("#",""));
					}
					
					doAjaxSync("report_delete_annotation_relation", params, success, null , complete, null, login)
					//doAjaxSyncWithLogin("report_delete_annotation_relation", params, success, login);
					
				
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}

		});
	$dialogBox.dialog("option", "position",[xPosition- $dialogBox.width(), yPosition]);
}

function cancel_relation(){
	$("#relation_table > tbody tr").remove();	
	$("#relation_add").show();
	$("#relation_select").hide();
	AnnotationRelation.relationMode = false;
	$("#content span").removeClass("relationGrey relationAvailable");
	$dialogObj = $(".deleteDialog");
	if ($dialogObj.length>0){
		$dialogObj.dialog("destroy").remove();
	}
}

/**
 * Zmiana aktualnie zaznaczonej adnotacji po kliknięciu na dowolną adnotację (element span).
 */
function blockInsertion(info){
	$(".an").attr("disabled", "true");
	$("#block_reason").text(info);
}

function unblockInsertion(){
	$(".an").removeAttr("disabled");
}

/**
 * Obsługa kliknięcia w anotację.
 */
var annotation_clicked_by_label = null;


$("#content span:not(.hiddenAnnotation)").live("click", function(){
	if (annotation_clicked_by_label != null)
	{
		if (_wAnnotation.get() == annotation_clicked_by_label)		
			set_current_annotation(null);
		
		else 
			set_current_annotation(annotation_clicked_by_label);
		
		annotation_clicked_by_label = null;
	}
	else if ( !prevent_from_annotation_selection )
	{
		if (!AnnotationRelation.relationMode && !AnnotationEvent.initMode && !AnnotationEvent.relationMode){
			if (_wAnnotation.get() == this){
				set_current_annotation(null);
				cancel_relation();
			}
			else {
				set_current_annotation(this);
				get_relations();
			}
		}
		else if (!AnnotationEvent.relationMode && !AnnotationEvent.initMode &&_wAnnotation.get() != this && !$(this).hasClass("relationGrey")) {
			add_relation(this);
		}
		else if (AnnotationEvent.relationMode && AnnotationEvent.initMode){
			updateEventSlotAnnotation(this);
		}
	}
	return false;
});

$("#content .annotation_label").live("click", function(){
	annotation_clicked_by_label = $("span[title='"+$(this).attr("title")+"']");
});




//--------------------
//Ustaw aktywną anotację
//---------------------------------------------------------
/**
 * Ustaw anotację do edycji.
 * @param annotation referencja na znacznik SPAN reprezentujący anotację.
 */
function set_current_annotation(annotation){
	$("#content span.selected").removeClass("selected");
	var context = $("#content .context");
	context.removeClass("context");
	if ( context.attr("class") == "" ) context.removeAttr("class");
	$("#cell_annotation_wait").show();
	$("#rightPanelAccordion").hide();
	$("#rightPanelEdit").hide();
	
	_wAnnotation.set(annotation);	
	if ( annotation == null ){
		$("#cell_annotation_wait").hide();
		$("#rightPanelAccordion").show();
		$("#rightPanelEdit").hide();
	}
	else{
		$("#cell_annotation_wait").hide();
		$("#rightPanelAccordion").hide();
		$("#rightPanelEdit").show();

		/* Copy list of annotation types */
		var $annTypeClone = $("#widget_annotation").clone();
		// Remove elements that are not needed in the new context.
		$annTypeClone.find("*").removeAttr("id");
		$annTypeClone.find("input").remove();
		$annTypeClone.find("button").remove();
		$annTypeClone.find("small").remove();
		$annTypeClone.find("a.short_all").parent().remove();
		// Show all hidden groups
		$annTypeClone.find("*").show();
		
		$("#annotation_type").html($annTypeClone.html());
		// wycina znaczniki relacji
		var annotation_html = $(annotation).html().replace(/<sup.*?<\/sup>/gi, '');
		_wAnnotation.setText($(annotation_html).text());	
		
		$("#annotation_redo_type").attr("title","Original: "+$(annotation).attr("title").split(":")[1]);
	}
}

/**
 * Zdarzenia tabeli z adnotacjami.
 */
$(".an_row").live("click", function(){
	var id = $(this).attr("label");
	$("#"+id).click();
});

/**
 * Ustawienie funkcji szybkiego dodawania anotacji.
 * @return
 */
function setup_quick_annotation_add(){
	$(function(){
		var default_annotation = $.cookie("default_annotation");
	
		if (default_annotation != null && default_annotation!=""){
			var $input = $("#widget_annotation input[value='"+default_annotation+"']");
			if ($input.length>0){
				$input.attr('checked', true);
				//$(".annotation_list input[value='"+default_annotation+"']").next().addClass("hightlighted");
				$("#quick_add_cancel").show();
			}
		}
		
		$("#quick_add_cancel").click(function(){
			$("#default_annotation_zero").attr('checked', true);
			//$("input:default_annotation ~ span").removeClass("hightlighted");
			$.cookie("default_annotation", "");
			$(this).hide();
			return false;
		});
		
		$("input[name='default_annotation']").click(function(){
			$("#quick_add_cancel").show();
			$.cookie("default_annotation", $(this).val());
		});
			
		$("#content").mouseup(function(){
			if ( _wAnnotation.get() == null ){
				var quick_annotation = $("input[name='default_annotation']:checked").val();
				if (quick_annotation){
					if ( global_selection && global_selection.isValid ){
						add_annotation(global_selection, 
								quick_annotation,
								getNewAnnotationStage());
						global_selection.clear();
						prevent_from_annotation_selection = true;
					}
					global_selection = null;					
				}
			}
		});
	});
}


//---------------------------------------------------------
// Po załadowaniu strony
//---------------------------------------------------------
$(document).ready(function(){
	_wAnnotation = new WidgetAnnotation();
	
	//_oNavigator = new Navigator($("#content"));
	setup_quick_annotation_add();
});

//---------------------------------------------------------
$(function(){
	isCtrl = false;
	isShift = false;
	
	$(document)
		.keyup(function (e) { 
			if(e.which == 17) 
				isCtrl=false; 
			if(e.which == 16) 
				isShift=false; 
		})
		.keydown(function (e) { 
			if(e.which == 17) 
				isCtrl=true; 
			if(e.which == 16){ 
				isShift=true; 
			}
			if(e.which == 83 && isCtrl == true) { 
				//run code for CTRL+S -- ie, save! return false; 
			}
			if(e.which == 37 && isCtrl == true && $("#article_prev")){
				//window.location = $("#article_prev").attr("href");			
			} 
			if(e.which == 39 && isCtrl == true && $("#article_next")){
				//window.location = $("#article_next").attr("href");
			}
			if (e.which == 39){
				//_oNavigator.moveRight();
			}
			if ( _wAnnotation != null ){
				_wAnnotation.keyDown(e, isCtrl, isShift)
			}
			if(isCtrl && isShift){ 
				return false;
			}
		});
});

$(document).ready(function(){
	var input = $("#report_type");
	if (input){
		input.change(function(){
			var report_id = $("#report_id").val();
			var report_type = $("#report_type").val();
			$("#report_type").after("<img src='gfx/ajax.gif'/>");
			$.post("index.php", { ajax: "report_set_type", id: report_id, type: report_type },
			  function(data){
				$("#report_type + img").remove();
				$("#report_type").after("<span class='ajax_success'>zapisano</span>");
				$("#report_type + span").fadeOut("1000", function(){$("#report_type + span").remove();});
				console_add("zmieniono typ raportu na <b>"+data['type_name']+"</b>");
			  }, "json");
		});
	}
});

/**
 * Usuwa tymczasowe tagi użyte do zaznaczenia tekstu, 
 * dookoła którego miała zostać utworzona anotacja.
 *   <xyz>...</xyz> w div#content
 * @return
 */
function remove_temporal_add_annotation_tag(){
	$("#content xyz").replaceWith(function(){
			return $(this).contents();
		}
	);
}

function remove_temporal_add_annotation_tag_by_id(id){
	$("#content xyz[id=" + id + "]").replaceWith(function(id){
			return $(this).contents();
		}
	);
}

/**
 * Wywołanie akcji dodania anotacji określonego typu i stage-u.
 * @param selection Obiekt klasy Selection reprezentujący anotację
 * @param type Identyfikator anotacji (ToDo: Nadal używana jest nazwa zamiast identyfikatora)
 */
function add_annotation(selection, type, stage){
	$("span.eosSpan").remove();

	var tmpid = temporal_annotation_wrap_id++;

	if (!selection.isSimple){
		alert("Błąd ciągłości adnotacji.\n\nMożliwe przyczyny:\n 1) Zaznaczona adnotacja nie tworzy ciągłego tekstu w ramach jednego elementu.\n 2) Adnotacja jest zagnieżdżona w innej adnotacji.\n 3)Adnotacja zawiera wewnętrzne adnotacje.");
		return false;
	}

	sel = selection.sel;
	var report_id = $("#report_id").val();
	
	var newNode = document.createElement("xyz");
	newNode.id = tmpid;
	sel.surroundContents(newNode);

	/** Jeżeli zaznaczony tekst jest wewnątrz tokeny, to rozszerz na cały token. */ 
	if ($(newNode).parent().is(".token")){
		$(newNode).parent().wrap("<xyz id='"+tmpid+"'></xyz>");
		$(newNode).replaceWith($(newNode).html());
		newNode = $("xyz[id=" + tmpid + "]");
	}
			
	var content_html = $.trim($(newNode).parents("div.content").html());
	
	content_html = content_html.replace(/<sup.*?<\/sup>/gi, '');

	var pattern = new RegExp("<xyz id=['\"]"+tmpid+"['\"]>(.*?)</xyz>");
	content_html = content_html.replace(pattern, fromDelimiter+"$1"+toDelimiter);
	content_no_html = content_html.replace(/<\/?[^>]+>/gi, '');
	content_no_html = html_entity_decode(content_no_html);

	// Pobierz treść anotacji przed usunięciem białych znaków
	var from = content_no_html.indexOf(fromDelimiter) + fromDelimiter.length;
	var to = content_no_html.indexOf(toDelimiter);
	var text = content_no_html.substring(from, to);
 
	// Oblicz właściwe indeksy
	content_no_html = content_no_html.replace(/\s/g, '');
	from = content_no_html.indexOf(fromDelimiter);
	to = content_no_html.indexOf(toDelimiter) - fromDelimiter.length - 1;
	
	status_processing("dodawanie anotacji ...");
	
	if (from < 0 || to < 0 ){
		remove_temporal_add_annotation_tag_by_id(tmpid);
		status_fade();
		dialog_error("Wystąpił błąd z odczytem granic anotacji. Odczytano ["+from+","+to+"]. <br/><br/>Zgłoś błąd administratorowi.");
		return;
	}
	
	var contentSide = $("#content xyz").parents(".content").attr("id")=="leftContent" ? "left" : "right";
	var $layer = $("#widget_annotation span."+type); 
	var layerSide = $("#annotation_layers input.leftLayer[name='layerId"+$layer.attr('groupid')+"']").attr("checked") ? "left" : "right";
	if ( contentSide != layerSide ){
		remove_temporal_add_annotation_tag_by_id(tmpid);
		status_fade();
		dialog_error("This category of annotation can be added in <b>" + layerSide + "</b> panel.");
		return;
	}

	/* Tablica z parametrami tworzonej anotacji */
	/* ToDo: wymaga dodania type_id */
	var params = {
		report_id: report_id, 
		from: from,
		to: to,
		text: text,
		type: type,
		stage: stage
	};

	/* Callback dla pomyślnego dodania anotacji */
	var success = function(data){
		$("#content xyz[id="+tmpid+"]").wrapInner("<span id='new" + tmpid + "'/>");
		remove_temporal_add_annotation_tag_by_id(tmpid);
	
		var annotation_id = data['annotation_id'];
		var node = $("#content span#new" + tmpid);
		var title = "an#"+annotation_id+":"+type;
		node.attr('title', title);
		node.attr('id', "an"+annotation_id);
		node.attr('groupid', $layer.attr("groupid"));
		node.attr('class', type);
		console_add("anotacja <b> "+title+" </b> została dodana do tekstu <i>"+text+"</i>");
	};
	
	/* Callback wywołany po przetworzeniu żądania */
	var complete = function(){
		status_fade();
	};
	
	doAjax("report_add_annotation", params, success, null, complete);
}

function delete_anaphora_links(relation_name, source_id, target_id){
	$.each($("#" + source_id).nextUntil("span"), function(num,element){
		if($(element).attr("target") == target_id.replace("an","") && $(element).attr("title") == relation_name){
			var rel_title = $(element).attr("title");
			var old_relin_title = $("#" + target_id).prev().attr("title");
			var new_relin_title = old_relin_title.replace(rel_title, "");
			if($.trim(new_relin_title) == ""){
				$("#" + target_id).prev().remove();
			}
			else{
				$("#" + target_id).prev().attr("title", new_relin_title);
			}
			$(element).remove();
		}
	});
}

/**
 * Metoda zwraca aktualnie ustawioną wartość stage dla nowo tworzonych anotacji.
 * @return jedna z wartości "new", "final", null 
 */
function getNewAnnotationStage(){
	/* Wartość stage ustalana jest na podstawie ustalonego trybu pracy, tj. annotation_mode. */
	var annotation_mode = $('input[name=annotation_mode]:checked').val(); 
	if ( annotation_mode == "final" ){
		return "final";
	}
	else if ( annotation_mode == "agreement" ){
		return "new";
	}
	else{
		return null;
	}
}

/**
 * Ustawia aktualny tryb pracy, podpina zdarzenia do automatycznego zapisu trybu.
 */
function setupAnnotationMode(){
	var annotation_mode = $.cookie('annotation_mode');
	if ( annotation_mode != null ){
		$('input[name=annotation_mode][value='+annotation_mode+']').attr("checked", true);
	}
	if ( getNewAnnotationStage() == null ){
		$('input[name=annotation_mode]:first').attr("checked", true);
		$.cookie('annotation_mode', $('input[name=annotation_mode]:checked').val());
	}
	$('#annotation_mode_list li').click(function(event){
		$(this).find("input").attr("checked", true);
		$.cookie('annotation_mode', $('input[name=annotation_mode]:checked').val());
	});
}
