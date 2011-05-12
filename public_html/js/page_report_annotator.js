var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;
var hiddenAnnotations = 0;

//obiekt trybu dodawania relacji pomiedzy anotacjami
var AnnotationRelation = Object();
AnnotationRelation.relationMode = false;
AnnotationRelation.types = []; //array of available types
AnnotationRelation.target_type = {}; //target_type.relation_type=[X,Y,...] existing relations relation_type between source_id and target_id=X,Y,..

//obiekt trybu edycji slotow zdarzenia
var AnnotationEvent = Object();
AnnotationEvent.relationMode = false; //edycja zawartosci slotow - wybor anotacji
AnnotationEvent.initMode = false; //edycja slotow


//ta funkcja moze byc uzyta dla wszystkich ajaxow, potem najwyzej sie dorobi obsluge faliureHandler'a (obecnie cancel_relation)
//moved to tmp.js
/*function ajaxErrorHandler(data, successHandler, errorHandler){
	if (data['error']){
		if (data['error_code']=="ERROR_AUTHORIZATION"){
				loginForm(false, function(success){ 
					if (success){						
						if (errorHandler && $.isFunction(errorHandler)){
							errorHandler();
						}
					}else{
						//alert('Wystąpił problem z autoryzacją. Zmiany nie zostały zapisane.');
						cancel_relation(); 
					}
				});				
		}
		else {
			alert('nieznany blad!');
		}
	} 
	else {
		if (successHandler && $.isFunction(successHandler)){
			successHandler();
		}		
	}
} */

//annotation_clicked_by_label -> source  

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){
	$("a.an").click(function(){
		selection = new Selection();
		if ( !selection.isValid )
		{
			alert("Zaznacz tekst");
			return false;
		}
		add_annotation(selection, $(this).attr("value"));		
		return false;
	});
	
	
	//inicjalizacja prawego panelu
	//setTimeout(function(){
		
	//},3000);
	
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
	
	$("#relation_table span,#relationList span,#annotationList span, #eventSlotsTable span ").live('mouseover',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).addClass("hightlighted");
	}).live('mouseout',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).removeClass("hightlighted");
	});
	
	$("div.deleteRelation").live('click',function(){
		delete_relation(this);
	});
	
	
	$(".hideLayer").click(function(){
		if (!$(this).attr("disabled")){
			layerArray = $.parseJSON($.cookie('hiddenLayer'));
			layerId = $(this).attr("name").replace("layerId","id");
			if ($(this).hasClass("hiddenLayer")) {
				$(this).attr("title","show");
				delete layerArray[layerId];
			}
			else{
				layerArray[layerId]=1;
				$(this).attr("title","hide");
			}
			newCookie="{ ";
			$.each(layerArray,function(index,value){
				newCookie+='"'+index+'":'+value+',';
			});
			$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");
			set_visible_layers();
		}
	});

	$(".clearLayer").click(function(){
		layerArray = $.parseJSON($.cookie('clearedLayer'));
		layerArray2 = $.parseJSON($.cookie('hiddenLayer'));
		layerId = $(this).attr("name").replace("layerId","id");
		if ($(this).hasClass("clearedLayer")) {
			delete layerArray[layerId];
			delete layerArray2[layerId];
		}
		else {
			layerArray[layerId]=1;
			layerArray2[layerId]=1;
		}
		var newCookie="{ ";
		$.each(layerArray,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('clearedLayer',newCookie.slice(0,-1)+"}");
		newCookie="{ ";
		$.each(layerArray2,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");

		if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
		document.location = document.location;
		
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
	
	
	//----
	

	
	set_tokens();
	get_all_relations();
	set_visible_layers();
	updateEventGroupTypes();
	
});

//------obsluga zdarzen
function updateEventGroupTypes(){
	$("#addEvent").attr('disabled','disabled');
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_get_event_group_types", 
			group_id : $("#eventGroups :selected:first").attr('groupid')
		},				
		success : function(data){
			$egt = $("#eventGroupTypes").empty();
			contentStr = "";			
			$.each(data, function(index, value){
				contentStr+='<option value="'+value.name+'" typeid="'+value.event_type_id+'" >'+value.name+'</option>';
			});
			$egt.html(contentStr);
			$("#addEvent").attr('disabled','');
		}
	});		

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
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_add_event", 
			type_id : $eventType.attr('typeid'),
			report_id : $("#report_id").val()
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){ 
					$("#eventTable tbody").append('<tr><td><a href="#" eventid="'+data.event_id+'" typeid="'+typeId+'">#'+data.event_id+'</a></td><td>'+groupName+'</td><td>'+typeName+'</td><td>0</td></tr>');
					//new id returned with data
					//get_all_relations();
					$("#addEvent").attr('disabled','');
					$("#eventGroups").attr('disabled','');
					$("#eventGroupTypes").attr('disabled','');
					//cancelAddAnnotation();
				}, 
				function(){
					addEvent();
				}
			);
		}
	});		
	
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
	
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_get_event_type_slots", 
			type_id : typeId
		},				
		success : function(data){
			$ets = $("#eventTypeSlots").empty();
			var contentStr = "";			
			$.each(data, function(index, value){
				contentStr+='<option value="'+value.name+'" typeid="'+value.event_type_slot_id+'" >'+value.name+'</option>';
			});
			$ets.html(contentStr);
			$("#addEventSlot").attr('disabled','');
		}
	});		 
	

	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_get_event_slots", 
			event_id : eventId
		},				
		success : function(data){
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
		}
	});		
	

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
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { 
							ajax : "report_delete_event", 
							event_id : eventId
						},				
						success : function(data){
							ajaxErrorHandler(data,
								function(){		
									cancelEvent();
									$('#eventTable a[eventid="'+eventId+'"]').parent().parent().remove();
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									deleteEvent();
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
}

function addEventSlot(){
	var eventId = $("#eventDetailsId").attr('eventid');
	var slotTypeId = $("#eventTypeSlots :selected:first").attr('typeid');
	var slotType = $("#eventTypeSlots :selected:first").text();
	$("#addEventSlot").attr('disabled','disabled');
	$("#eventTypeSlots").attr('disabled','disabled');
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_add_event_slot", 
			event_id : eventId,
			type_id : slotTypeId
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){ 
					$("#eventSlotsTable tbody").append('<tr><td slotid="'+data.slot_id+'" typeid="'+slotTypeId+'">#'+data.slot_id+'</td><td>'+slotType+'</td><td class="eventSlotAnnotation emptySlot" style="text-align:center; cursor:pointer"><b>+</b></td><td class="deleteEventSlot" style="text-align:center;cursor:pointer "><b>X</b></td></tr>');
					$("#addEventSlot").attr('disabled','');
					$("#eventTypeSlots").attr('disabled','');
					$slotCount = $('#eventTable a[eventid="'+eventId+'"]').parent().next().next().next();
					$slotCount.text(parseInt($slotCount.text())+1);
				}, 
				function(){
					addEventSlot();
				}
			);
		}
	});			

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
	
	
	
	/*$.ajax({
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
				}, 
				function(){
					deleteEventSlot(handler);
				}
			);
		}
	});			*/
	
	
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
	
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_update_event_slot_annotation", 
			slot_id : slotId,
			annotation_id : annotationId
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){
					$(AnnotationEvent.handler).removeClass("emptySlot").attr('style','').html(
						'<span class="'+annotationType+'" title="an#'+annotationId+':'+annotationType+'">'+annotationText+'</span>'
					);
					cancelAddAnnotation();
					
					//editEvent( $('#eventTable a[eventid="'+ $("#eventDetailsId").text() +'"]'));
				}, 
				function(){
					updateEventSlotAnnotation(annotationObj);
				}
			);
		}
	});			

	
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




//-------


function set_visible_layers(){
	if (!$.cookie('hiddenLayer')) $.cookie('hiddenLayer','{}');
	if (!$.cookie('clearedLayer')) $.cookie('clearedLayer','{}');
	var layerArray = $.parseJSON($.cookie('hiddenLayer'));
	$(".hideLayer").removeClass('hiddenLayer').attr("title","hide").attr("checked","checked");//.css("background-color","");
	$("#content span:not(.token)").removeClass('hiddenAnnotation');
	$("#widget_annotation div[groupid]").children().show().filter(".hiddenAnnotationPadLayer").remove();
	$(".layerName").css("color","").css("text-decoration","");
	$("#annotationList ul").show();
	
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.hideLayer[name="layerId'+layerId+'"]').addClass('hiddenLayer').attr("checked","").attr("title","show").parent().prev().children("span").css("color","#AAA");
		$("#content span[groupid="+layerId+"]").addClass('hiddenAnnotation');
		$('#widget_annotation div[groupid="'+layerId+'"]').append('<div class="hiddenAnnotationPadLayer">This annotation layer was hidden (see Annotation layers)</div>').children("ul").hide();
		$('#annotationList ul[groupid="'+layerId+'"]').hide();
		
		
	});
	
	layerArray = $.parseJSON($.cookie('clearedLayer'));
	$(".clearLayer").removeClass('clearedLayer').attr("title","hide").attr("checked","checked");
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.clearLayer[name="layerId'+layerId+'"]').addClass('clearedLayer').attr("checked","").attr("title","show").parent().prev().children().attr("disabled","disabled").parent().prev().children("span").css("text-decoration","line-through");
		var $container = $('#widget_annotation div[groupid="'+layerId+'"]')
		if ($container.children(".hiddenAnnotationPadLayer").length==0)
			$container.append('<div class="hiddenAnnotationPadLayer">This annotation layer was disabled (see Annotation layers)</div>').children("ul").hide();
		else $container.children(".hiddenAnnotationPadLayer").text("This annotation layer was disabled (see Annotation layers)");
	});
	$("#annotationsCount").text(parseInt($.cookie("allcount"))-$("#content span:not(.hiddenAnnotation)").length);
	


}

function block_existing_relations(){
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
		//$("#rightPanel").accordion("activate","#cell_annotation_add_header");
		
		//$("#cell_annotation_edit").hide().prev().hide();
		//$("#cell_annotation_edit").hide().prev().hide();		
		
		//$("#rightPanel").accordion("option","disabled","true");

		
		jQuery.ajax({
			async : false,
			url : "index.php",
			dataType : "json",
			type : "post",
			data : { 
				ajax : "report_get_annotation_relations", 
				annotation_id : sourceObj.id
			},				
			success : function(data){
				ajaxErrorHandler(data,
					function(){ 
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
						//$("#cell_annotation_edit").show().prev().show();
						//$("#rightPanelAccordion").accordion("activate","#cell_annotation_edit_header");
						$("#rightPanelEdit").show();
						
						get_all_relations();
					}, 
					function(){
						get_relations();
					}
				);
			}
		});		
	}
}

function get_all_relations(){
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_get_relations", 
			report_id : $("#report_id").val()
		},				
		success : function(data){
			$("#content span").removeClass("unit_source unit_target");
			$.each(data, function(index, value){
				$("#an"+value.source_id).addClass("unit_source");
				$("#an"+value.target_id).addClass("unit_target");
			});
		}
	});		
	

}


function add_relation_init(){
	AnnotationRelation.types = [];
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { ajax : "report_get_annotation_relation_types", annotation_id : _wAnnotation._annotation.id },				
		success : function(data){
			ajaxErrorHandler(data,
				function(){
					AnnotationRelation.relationMode = true; //global variable in page_report_annotator.js
					$("#relation_add").hide();
					$("#relation_select").show();
					$listContainer = $("#relation_type").empty();//.append('<option style="display:none"></option>');
					$.each(data, function(index, value){
						$('<option value="'+value.name+'">'+value.name+'</option>').data(value).appendTo($listContainer);
					});
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { ajax : "report_get_annotation_types", annotation_id : _wAnnotation._annotation.id },				
						success : function(data2){
							$.each(data2,function(index, value){
								AnnotationRelation.types.push(value[0]);
							});
							block_existing_relations();
						}
					});	
				},
				function(){
					add_relation_init();
				}
			);
		}
	});
}

function add_relation(spanObj){
	sourceObj = _wAnnotation._annotation;
	targetObj = new Annotation(spanObj);
	relationTypeId = $("#relation_type").children(":selected:first").data('id');
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_add_annotation_relation", 
			source_id : sourceObj.id,
			target_id : targetObj.id,
			relation_type_id : relationTypeId
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){
					cancel_relation();
					get_relations();
				},
				function(){
					add_relation(spanObj);
				}
			);
		}
	});			
}
/*
 * 			$(this).attr("id").replace("relation",""), 
			$(this).offset().left-$(window).scrollLeft(),
			$(this).offset().top - $(window).scrollTop());

 */
function delete_relation(deleteHandler){
	relationId = $(deleteHandler).attr("id").replace("relation","");
	xPosition = $(deleteHandler).offset().left-$(window).scrollLeft();
	yPosition = $(deleteHandler).offset().top - $(window).scrollTop();
	
	
	$relation = $("#relation"+relationId);
	relationName = $relation.parent().prev().prev().text();
	$relationSrc = $("#content span.selected:first");
	$relationSrcTxt = $('<span class="'+$relationSrc.attr('title').split(":")[1]+'">'+$relationSrc.text()+'</span>');
	$relationDstTxt = $($relation.parent().prev().html()).removeAttr('title');
	//"Czy na pewno usunąć relację 'xxxx' pomiędzy 'aaaa' i 'bbb'?"
	//log(relationDstTxt);
	//log(relationName);
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
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { 
							ajax : "report_delete_annotation_relation", 
							relation_id : relationId
						},				
						success : function(data){
							ajaxErrorHandler(data,
								function(){							
									cancel_relation();
									get_relations();
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									delete_relation(deleteHandler);
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
	
	/*jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_delete_annotation_relation", 
			relation_id : relationId
		},				
		success : function(data){
			cancel_relation();
			get_relations();
		}
	});	*/
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
	get_all_relations();
	
}


//obsluga tokenow
function set_tokens(){
	$(".token").removeAttr("groupid").addClass("hiddenAnnotation");//.attr("id","an-1");
}


/*
 * Zmiana aktualnie zaznaczonej adnotacji po kliknięciu na dowolną adnotację (element span).
 */
function blockInsertion(info){
	$(".an").attr("disabled", "true");
	$("#block_reason").text(info);
	$("#block_message").show();
	$("#block_message_info").hide();
}
function unblockInsertion(){
	$(".an").removeAttr("disabled");
	$("#block_message").hide();
	$("#block_message_info").show();
}

/**
 * Obsługa kliknięcia w anotację.
 */
var annotation_clicked_by_label = null;


$("#content span:not(.hiddenAnnotation)").live("click", function(){
	if (annotation_clicked_by_label != null)
	{
		//alert("00");
		//czy to sie nigdy nie wykona?
		if (_wAnnotation.get() == annotation_clicked_by_label)		
			set_current_annotation(null);
		
		else 
			set_current_annotation(annotation_clicked_by_label);
		
		annotation_clicked_by_label = null;
	}
	else if ( getSelText() == "")
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
		
		var $annType = $("#annotation_type");
		$annType.html($("#widget_annotation").html()).find("*").removeAttr("id").removeClass("toggle_cookie");
		$annType.find("div[groupid]").show();
		$annType.find("small").remove();
		$annType.find("input").remove();
		$annType.find("button").remove();
		$annType.find("a").attr("href","#");
		
		//$annType.find(".scrolling").height(100);
		
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
					selection = new Selection();
					if ( selection.isValid )
						add_annotation(selection, quick_annotation);
				}
			}
		});
	});
}


//---------------------------------------------------------
// Po załadowaniu strony
//---------------------------------------------------------
$(document).ready(function(){
	$("#annotations").tablesorter(); 

	$(".autogrow").autogrow();
	
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

// Dodaj anotację wskazanego typu
function add_annotation(selection, type){
	selection.trim();
	selection.fit();

	if (!selection.isSimple){
		alert("Błąd ciągłości adnotacji.\n\nMożliwe przyczyny:\n 1) Zaznaczona adnotacja nie tworzy ciągłego tekstu w ramach jednego elementu.\n 2) Adnotacja jest zagnieżdżona w innej adnotacji.\n 3)Adnotacja zawiera wewnętrzne adnotacje.");
		return false;
	}

	sel = selection.sel;

	var report_id = $("#report_id").val();
	
	var newNode = document.createElement("xyz");
	sel.surroundContents(newNode);
	if ($(newNode).parent().is(".token")){
		status_fade();
		dialog_error("You cannot create new annotation inside a token");
		return;
	}
			
	var content_html = $.trim($("#content").html());

	//console.log(content_no_html);
	content_html = content_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);
	//content_no_html = html2txt(content_no_html);
	content_no_html = content_html.replace(/<\/?[^>]+>/gi, '');

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
		status_fade();
		dialog_error("Wystąpił błąd z odczytem granic anotacji. Odczytano ["+from+","+to+"]. <br/><br/>Zgłoś błąd administratorowi.");
		return;
	}
	
	$.ajax({
		type: 	'POST',
		url: 	"index.php",
		data:	{ 	
					ajax: "report_add_annotation", 
					report_id: report_id, 
					from: from,
					to: to,
					text: text,
					type: type
				},
		success:function(data){
					$("#content xyz").wrapInner("<span id='new'/>");
					$("#content xyz").replaceWith( $("#content xyz").contents() );
				
					if (data['success']){
						var annotation_id = data['annotation_id'];
						var node = $("#content span#new");
						var title = "an#"+annotation_id+":"+type;
						node.attr('title', title);
						node.attr('id', "an"+annotation_id);
						node.attr('class', type);
						console_add("anotacja <b> "+title+" </b> została dodana do tekstu <i>"+text+"</i>");
						recreate_labels(node);
					}else{
					    dialog_error(data['error']);
					    $("span#new").after($("span#new").html());
					    $("span#new").remove();
					}			
					status_fade();
				},
		error: function(request, textStatus, errorThrown){
				  dialog_error(request['responseText']);
				  status_fade();
				},
		dataType:"json"
	});	
}

