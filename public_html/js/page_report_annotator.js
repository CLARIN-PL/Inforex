var isCtrl = false; 
var _wAnnotation = null;
var _oNavigator = null;
var hiddenAnnotations = 0;
var anaphora_target_n = 1;

// zmienna określająca globalne (dla perspektywy) zaznaczenie tekstu
var global_selection = null;

// sposób dekorowania aktywnych realcji na liście Relation list
// opcje: selected - podświetla wiersz z relacją; grey - nie aktywne relacje są szare
var relation_list_decoration = "selected";

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

	$("#content").mouseup(function(){
		global_selection = new Selection();
		if ( !global_selection.isValid ){
			global_selection = null;
		}
	});

	// zmiana relacji
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
		
	$("sup.rel").live({
		mouseover: function(){
			$(this).addClass("hightlighted");
			var target_id = $(this).attr('target');
			$("#an" + target_id).addClass("hightlighted");
			var rel_num = $(this).text().replace("↦",""); 
			$("sup.relin").each(function(i,val){
				if($(val).text() == rel_num){
					$(val).addClass("hightlighted");
				}
			});
			if($(this).prev().hasClass("rel")){						
				$(this).prevUntil("span").prev("span").addClass("hightlighted");
			}	
			else{
				$(this).prev("span").addClass("hightlighted");
			}			
		},		
		mouseout: function(){
			$(this).removeClass("hightlighted");
			var target_id = $(this).attr('target');
			$("#an" + target_id).removeClass("hightlighted");
			var rel_num = $(this).text().replace("↦",""); 
			$("sup.relin").each(function(i,val){
				if($(val).text() == rel_num){
					$(val).removeClass("hightlighted");
				}					
			});
			$(this).prev("span").removeClass("hightlighted");
		}
	});
	
	$("sup.relin").live({
		mouseover: function(){
			$(this).addClass("hightlighted");
			var target_id = $(this).next("span").attr("id").replace('an','');
			$(this).next("span").addClass("hightlighted");
			$("sup.rel[target="+target_id+"]").each(function(i,val){
				$(val).addClass("hightlighted");
				if($(val).prev().hasClass('rel')){
					$(val).prevUntil("span","sup").prev("span").addClass("hightlighted");				
				}
				else{
					$(val).prev("span").addClass("hightlighted");
				}
			});
		},		
		mouseout: function(){
			$(this).removeClass("hightlighted");
			var target_id = $(this).next("span").attr("id").replace('an','');
			$(this).next("span").removeClass("hightlighted");
			$("sup.rel[target="+target_id+"]").each(function(i,val){				
				$(val).removeClass("hightlighted");
				$(val).prev("span").removeClass("hightlighted");	
			});
		}
	});

	$("a.an").click(function(){
		selection = new Selection();
		if ( !selection.isValid && !global_selection)
		{
			alert("Zaznacz tekst");
			return false;
		}
		else if(global_selection){
			selection = global_selection;
			global_selection = null;
		}
		add_annotation(selection, $(this).attr("value"));		
		selection.clear();
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

	$(".hideSublayer").click(function(){
		if (!$(this).attr("disabled")){
			layerArray = $.parseJSON($.cookie('hiddenSublayer'));
			layerId = $(this).attr("name").replace("sublayerId","id");
			if ($(this).hasClass("hiddenSublayer")) {
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
			$.cookie('hiddenSublayer',newCookie.slice(0,-1)+"}");
			set_visible_layers();
		}
	});
	
	$(".leftLayer").click(function(){
		$(this).parents(".layerRow").nextUntil(".layerRow").find(".leftSublayer").attr("checked","checked");
	});

	$(".rightLayer").click(function(){
		$(this).parents(".layerRow").nextUntil(".layerRow").find(".rightSublayer").attr("checked","checked");
	});
	
	// szybkie przełączanie warst annotacji	
	$(".layerName").click(function(){
		var act_layer = $(this).parent().parent();
		layerArray = $.parseJSON($.cookie('clearedLayer'));
		layerArray1 = $.parseJSON($.cookie('clearedSublayer'));
		layerArray2 = $.parseJSON($.cookie('rightSublayer'));
		layerArray3 = $.parseJSON($.cookie('hiddenLayer'));
		layerArray4 = $.parseJSON($.cookie('rightLayer'));
		
		$.each($(".clearLayer"),function(index, value){
			layerId = $(value).attr("name").replace("layerId","id");
			layerArray[layerId]=1;						
		});
		
		if($(act_layer).hasClass('layerRow')){
			var this_layer = $(act_layer).attr("setid");
			var thisLayerId = "id" + this_layer;
			$.each($(".clearSublayer"),function(index, value){
				layerId = $(value).attr("name").replace("sublayerId","id");
				delete layerArray1[layerId];
				delete layerArray2[layerId];				
			});			
		}	
		else if($(act_layer).hasClass('sublayerRow')){
			if($(act_layer).prev().hasClass('layerRow')){
				var this_layer = $(act_layer).prev().attr("setid");
			}
			else{
				var this_layer = $(act_layer).prevUntil(".layerRow").prev().attr("setid");
			}			
			var thisLayerId = "id" + this_layer;
			var thisSublayerId = $(act_layer).find("input.clearSublayer").attr("name").replace("sublayerId","id");
			$.each($(".clearSublayer"),function(index, value){
				layerId = $(value).attr("name").replace("sublayerId","id");
				layerArray1[layerId]=1;
				delete layerArray2[layerId];				
			});		
			delete layerArray1[thisSublayerId];			
		}	
		else{
			return false;
		}
		
		delete layerArray[thisLayerId];
		delete layerArray3[thisLayerId];
		delete layerArray4[thisLayerId];
		
		var newCookie="{ ";
		$.each(layerArray,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('clearedLayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray1,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('clearedSublayer',newCookie.slice(0,-1)+"}");

		newCookie="{ ";
		$.each(layerArray2,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('rightSublayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray3,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray4,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('rightLayer',newCookie.slice(0,-1)+"}");
		
		if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
		document.location = document.location;
		
	});
	
	$("#applyLayer").click(function(){
		layerArray = $.parseJSON($.cookie('clearedLayer'));
		layerArray2 = $.parseJSON($.cookie('hiddenLayer'));
		layerArray3 = $.parseJSON($.cookie('rightLayer'));
		layerArray4 = $.parseJSON($.cookie('rightSublayer'));
		layerArray5 = $.parseJSON($.cookie('clearedSublayer'));
		layerArray6 = $.parseJSON($.cookie('active_annotation_types'));
		
		$.each($(".clearLayer"),function(index, value){
			layerId = $(value).attr("name").replace("layerId","id");
			if (!$(value).attr("checked")) {
				delete layerArray[layerId];
				delete layerArray2[layerId];
			}
			else {
				layerArray[layerId]=1;
				layerArray2[layerId]=1;
			}			
		});
		$.each($(".clearSublayer"),function(index, value){
			layerId = $(value).attr("name").replace("sublayerId","id");
			if (!$(value).attr("checked")) {
				delete layerArray5[layerId];
				delete layerArray4[layerId];
			}
			else {
				layerArray5[layerId]=1;
				layerArray4[layerId]=1;
			}			
		});
		$.each($(".rightLayer"),function(index, value){
			layerId = $(value).attr("name").replace("layerId","id");
			if ($(value).attr("checked")) {
				layerArray3[layerId]=1;
			}
			else {
				delete layerArray3[layerId];
			}			
		});		
		$.each($(".rightSublayer"),function(index, value){
			layerId = $(value).attr("name").replace("sublayerId","id");
			if ($(value).attr("checked")) {
				layerArray4[layerId]=1;
			}
			else {
				delete layerArray4[layerId];
			}			
		});		
		$.each($(".relation_sets"),function(index, value){
			layerId = "id" + $(value).val();
			if ($(value).attr("checked")) {
				layerArray6[layerId]=1;
			}
			else {
				delete layerArray6[layerId];
			}			
		});		
		
		var newCookie="{ ";
		$.each(layerArray,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('clearedLayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray5,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('clearedSublayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray2,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('hiddenLayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray3,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('rightLayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray4,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('rightSublayer',newCookie.slice(0,-1)+"}");
		
		newCookie="{ ";
		$.each(layerArray6,function(index,value){
			newCookie+='"'+index+'":'+value+',';
		});
		$.cookie('active_annotation_types',newCookie.slice(0,-1)+"}");
		
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
	
	$(".toggleLayer").click(function(){
		if ($(this).hasClass("ui-icon-circlesmall-plus")){
			$(this).removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
			$(this).parents(".layerRow").nextUntil(".layerRow").show();	
		} 
		else {
			$(this).removeClass("ui-icon-circlesmall-minus").addClass("ui-icon-circlesmall-plus");
			$(this).parents(".layerRow").nextUntil(".layerRow").hide();	
		}
	});
	$.each($(".toggleLayer").parents(".layerRow"), function(index, elem){
		if (!$(elem).nextUntil(".layerRow").length){
			$(elem).find(".toggleLayer").removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-close").css("opacity","0.5").unbind("click");//.removeClass("ui-icon-circlesmall-plus").addClass("ui-icon-circlesmall-minus");
		};
	});	
	
	$(".deleteAnnotation").live("click",function(){
		deleteAnnotation($(this).attr('annotation_id'));
		$(this).parent().remove();
	});
	
	//split by sentences
	$("#splitSentences").change(function(){
		$.cookie("splitSentences",$(this).is(":checked"));
		set_sentences();
	});

	//split by sentences
	$("#showRight").change(function(){
		$.cookie("showRight",$(this).is(":checked"));
		show_right();
	});
	
	create_anaphora_links();	
	set_stage();
	set_sentences();
	set_tokens();
	get_all_relations();
	set_visible_layers();
	updateEventGroupTypes();	
});


function getRelationsTypes(rel_id, sourcegroupid, sourcesubgroupid, targetgroupid, targetsubgroupid){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "report_get_relations_types",
			relation_id : rel_id,
			sourcegroupid : sourcegroupid,
			sourcesubgroupid : sourcesubgroupid,
			targetgroupid : targetgroupid,
			targetsubgroupid : targetsubgroupid
		},
		success : function(data){
			if ( typeof data.error_msg !== "undefined" && data.error_msg){
				dialog_error(data.error_msg);
			}
			else{
				ajaxErrorHandler(data,
					function(){
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
									$.ajax({
										async : false,
										url : "index.php",
										dataType : "json",
										type : "post",
										data : {
											ajax : "report_update_relations_type",
											relation_id : rel_id,
											relation_type : $("input[name='setRelationTypes']:checked").val()
										},
										success : function(data){
											if (document.location.href[document.location.href.length-1]=="#") document.location.href=document.location.href.slice(0,-1);
											document.location = document.location;
										},
										error : function(request, textStatus, errorThrown){
					 						dialog_error(request['responseText']);
										} 
									});
								}
							},
							close: function(event, ui) {
								$dialogBox.dialog("destroy").remove();
								$dialogBox = null;
							}
						});	
					},
					function(){
						getRelationsTypes(rel_id, sourcegroupid, sourcesubgroupid, targetgroupid, targetsubgroupid);
					}
				);
			}								
		},
		error : function(request, textStatus, errorThrown){
			dialog_error("<b>HTML result:</b><br/>" + request.responseText);
		}
	});
}

//split report by sentences
function set_sentences(){
	if ($.cookie("splitSentences")=="true"){
		
		if($("sentence").length){
			$("sentence").after('<div class="eosSpan"><hr/></div>');
		}
		else{
			$("span.token.eos").each(function(){
				var $this = $(this);
				while ( $this.get(0) == $this.parent().children().last().get(0)
						&& !$this.parent().hasClass("contentBox") ){
			    	$this = $this.parent();
				}
				$this.after('<div class="eosSpan"><hr/></div>');
			});
		}
	}else 
		$("div.eosSpan").remove();
}


//show/hide right content
function show_right(){
	if ($.cookie("showRight")=="true"){
		$("#leftContent").css('width','50%');
		$("#rightContent").show();
	}
	else {
		$("#leftContent").css('width','100%');
		$("#rightContent").hide();
	}
}

function set_stage(){	
	$(".stageItem").css("cursor","pointer").click(function(){
		$.cookie('listStage',$(this).attr('stage'));
		$("#annotationList tr[stage]").hide();
		$("#annotationList tr[stage='"+$(this).attr('stage')+"']").show();
		$(".stageItem").removeClass("hightlighted");
		$(this).addClass('hightlighted');
	});	
	if (!$.cookie('listStage')) $.cookie('listStage','final');	
	var stage = $.cookie('listStage');
	$(".stageItem[stage='"+stage+"']").addClass("hightlighted");
	$("#annotationList tr[stage]").hide();
	$("#annotationList tr[stage='"+stage+"']").show();
}


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
					$("#addEvent").attr('disabled','');
					$("#eventGroups").attr('disabled','');
					$("#eventGroupTypes").attr('disabled','');
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

function set_visible_layers(){
	if (!$.cookie('hiddenLayer')) $.cookie('hiddenLayer','{}');
	if (!$.cookie('hiddenSublayer')) $.cookie('hiddenSublayer','{}');
	if (!$.cookie('clearedLayer')) $.cookie('clearedLayer','{}');
	if (!$.cookie('clearedSublayer')) $.cookie('clearedSublayer','{}');
	if (!$.cookie('rightLayer')) $.cookie('rightLayer','{}');
	if (!$.cookie('rightSublayer')) $.cookie('rightSublayer','{}');
	if (!$.cookie('active_annotation_types')) $.cookie('active_annotation_types','{}');
	var layerArray = $.parseJSON($.cookie('hiddenLayer'));
	$(".hideLayer").removeClass('hiddenLayer').attr("title","hide").attr("checked","checked");//.css("background-color","");
	$("#content span:not(.token)").removeClass('hiddenAnnotation');
	$("#content sup").show();
	// --- dekoracja lista relacji w zależności od widoczności warstw annotacji
	if(relation_list_decoration == "selected"){ 
		$("#relationList tr").addClass("selected");
	}
	if(relation_list_decoration == "grey"){ 
		$("#relationList span").addClass('relationAvailable').removeClass('relationGrey');
	}		
	
	$("#widget_annotation div[groupid]").children().show().filter(".hiddenAnnotationPadLayer").remove();
	$(".layerName").css("color","").css("text-decoration","");
	$("#annotationList ul").show();
	
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.hideLayer[name="layerId'+layerId+'"]').addClass('hiddenLayer').attr("checked","").attr("title","show")//;.parent().prev().children("span").css("color","#AAA");
		$("#content span[groupid="+layerId+"]").addClass('hiddenAnnotation');
		$("#content sup[targetgroupid="+layerId+"]").hide();
		$("#content sup[sourcegroupid="+layerId+"]").hide();
		
		// --- dekoracja lista relacji w zależności od widoczności warstw annotacji
		if(relation_list_decoration == "selected"){ 
			$("#relationList td[sourcegroupid="+layerId+"]").parent().removeClass("selected");
			$("#relationList td[targetgroupid="+layerId+"]").parent().removeClass("selected");
		}
		if(relation_list_decoration == "grey"){ 
			$("#relationList td[sourcegroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
			$("#relationList td[targetgroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
		}
		
		$('#widget_annotation div[groupid="'+layerId+'"]').append('<div class="hiddenAnnotationPadLayer">This annotation layer was hidden (see Annotation layers)</div>').children("ul").hide();
		$('#annotationList ul[groupid="'+layerId+'"]').hide();
	});
	
	layerArray = $.parseJSON($.cookie('hiddenSublayer'));
	$(".hideSublayer").removeClass('hiddenSublayer').attr("title","hide").attr("checked","checked");//.css("background-color","");
	$("#widget_annotation li[subsetid]").children().show().filter(".hiddenAnnotationPadSublayer").remove();
	$("#annotationList li.subsetName").show();
	
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.hideSublayer[name="sublayerId'+layerId+'"]').addClass('hiddenSublayer').attr("checked","").attr("title","show");//.parent().prev().children("span").css("color","#AAA");
		$("#content span[subgroupid="+layerId+"]").addClass('hiddenAnnotation');
		$("#content sup[targetsubgroupid="+layerId+"]").hide();
		$("#content sup[sourcesubgroupid="+layerId+"]").hide();
		
		// --- dekoracja lista relacji w zależności od widoczności warstw annotacji
		if(relation_list_decoration == "selected"){ 
			$("#relationList td[sourcesubgroupid="+layerId+"]").parent().removeClass("selected");
			$("#relationList td[targetsubgroupid="+layerId+"]").parent().removeClass("selected");
		}
		if(relation_list_decoration == "grey"){ 
			$("#relationList td[sourcesubgroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
			$("#relationList td[targetsubgroupid="+layerId+"] span").addClass('relationGrey').removeClass('relationAvailable');
		}
		
		$('#widget_annotation li[subsetid="'+layerId+'"]').append('<div class="hiddenAnnotationPadSublayer">This annotation sublayer was hidden (see Annotation layers)</div>').children("ul").hide();
		$('#annotationList li[subsetid="'+layerId+'"]').hide();
	});
	//ukrywa elementy relin, jeżeli nie wskazuje na nie żaden widoczny element rel
	$("sup.relin:visible").each(function(index,element){
		var target_num1 = "↷"+$(this).text();
		var target_num2 = "⇢"+$(this).text();
		var count_visible_rel = 0;
		$("sup.rel:visible").each(function(i,val){
			if($(val).text() == target_num1 || $(val).text() == target_num2){
				count_visible_rel++;				
			} 
		});		
		if(count_visible_rel == 0){
			$(element).hide();
		}
	});
	
	layerArray = $.parseJSON($.cookie('clearedLayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.clearLayer[name="layerId'+layerId+'"]').addClass('clearedLayer').attr("checked","checked");//.parent().prev().children().attr("disabled","disabled").parent().prev().children("span").css("text-decoration","line-through");
		var $container = $('#widget_annotation div[groupid="'+layerId+'"]');
		if ($container.children(".hiddenAnnotationPadLayer").length==0)
			$container.append('<div class="hiddenAnnotationPadLayer">This annotation layer was disabled (see Annotation layers)</div>').children("ul").hide();
		else $container.children(".hiddenAnnotationPadLayer").text("This annotation layer was disabled (see Annotation layers)");
	});

	layerArray = $.parseJSON($.cookie('clearedSublayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.clearSublayer[name="sublayerId'+layerId+'"]').addClass('clearedSublayer').attr("checked","checked");//.parent().prev().children().attr("disabled","disabled").parent().prev().children("span").css("text-decoration","line-through");
		var $container = $('#widget_annotation li[subsetid="'+layerId+'"]');
		if ($container.children(".hiddenAnnotationPadSublayer").length==0)
			$container.append('<div class="hiddenAnnotationPadSublayer">This annotation sublayer was disabled (see Annotation layers)</div>').children("ul").hide();
		else $container.children(".hiddenAnnotationPadSublayer").text("This annotation sublayer was disabled (see Annotation layers)");
	});
	
	layerArray = $.parseJSON($.cookie('rightLayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.rightLayer[name="layerId'+layerId+'"]').attr("checked","checked");//.parent().prev().children().attr("disabled","disabled").parent().prev().children("span").css("text-decoration","line-through");
	});

	layerArray = $.parseJSON($.cookie('rightSublayer'));
	$.each(layerArray,function(index,value){
		layerId = index.replace("id","");
		$('.rightSublayer[name="sublayerId'+layerId+'"]').attr("checked","checked");//.parent().prev().children().attr("disabled","disabled").parent().prev().children("span").css("text-decoration","line-through");
	});
}

function block_existing_relations(){
	jQuery.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : { 
			ajax : "report_get_annotation_types", 
			annotation_id : _wAnnotation._annotation.id,
			relation_type_id : $("#relation_type").children(":selected:first").data('id')
		},				
		success : function(data2){
			AnnotationRelation.types = [];
			$.each(data2,function(index, value){
				AnnotationRelation.types.push(value[0].toLowerCase());
			});
		}
	});	
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
					block_existing_relations();
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
				},
				function(){
					add_relation(spanObj);
				}
			);
		}
	});			
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
							delete_anaphora_links(relationName, $relationSrc.attr("id"), $($relation.parent().prev().html()).attr('title').split(":")[0].replace("#",""));
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

// Dodaj anotację wskazanego typu
function add_annotation(selection, type){
	$("span.eosSpan").remove();

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

	/** Jeżeli zaznaczony tekst jest wewnątrz tokeny, to rozszerz na cały token. */ 
	if ($(newNode).parent().is(".token")){
		$(newNode).parent().wrap("<xyz></xyz>");
		$(newNode).replaceWith($(newNode).html());
		newNode = $("xyz");
	}
			
	//var content_html = $.trim($("#content").html());
	var content_html = $.trim($(newNode).parents("div.content").html());
	
	content_html = content_html.replace(/<sup.*?<\/sup>/gi, '');

	//console.log(content_no_html);
	content_html = content_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);
	//content_no_html = html2txt(content_no_html);
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
		remove_temporal_add_annotation_tag();
		status_fade();
		dialog_error("Wystąpił błąd z odczytem granic anotacji. Odczytano ["+from+","+to+"]. <br/><br/>Zgłoś błąd administratorowi.");
		return;
	}
	
	var contentSide = $("#content xyz").parents(".content").attr("id")=="leftContent" ? "left" : "right";
	var $layer = $("#widget_annotation span."+type); 
	var layerSide = $("#annotation_layers input.leftLayer[name='layerId"+$layer.attr('groupid')+"']").attr("checked") ? "left" : "right";
	if (contentSide!=layerSide){
		remove_temporal_add_annotation_tag();
		status_fade();
		dialog_error("<b>Wrong panel</b>.<br/>This annotation type should be added to the other panel.");
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
					remove_temporal_add_annotation_tag();
				
					if (data['success']){
						var annotation_id = data['annotation_id'];
						var node = $("#content span#new");
						var title = "an#"+annotation_id+":"+type;
						node.attr('title', title);
						node.attr('id', "an"+annotation_id);
						node.attr('groupid', $layer.attr("groupid"));
						node.attr('class', type);
						console_add("anotacja <b> "+title+" </b> została dodana do tekstu <i>"+text+"</i>");
					}
					else{
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

/** 
 * Tworzy wizualizację połączeń anaforycznych. Indeksuje anotacje, które biorą udział w relacji.
 */
function create_anaphora_links(){
	$("sup.relin").remove();
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		$("#an" + target_id).addClass("_anaphora_target");
		$(this).attr('targetgroupid',$("#an" + target_id).attr('groupid'));
		$(this).attr('targetsubgroupid',$("#an" + target_id).attr('subgroupid'));
		$(this).attr('sourcesubgroupid',$("#an" + $(this).attr('sourcegroupid')).attr('subgroupid'));
		$(this).attr('sourcegroupid',$("#an" + $(this).attr('sourcegroupid')).attr('groupid'));
	});
	$("span._anaphora_target").each(function(){
		$(this).before("<sup class='relin' targetsubgroupid="+$(this).attr('subgroupid')+" targetgroupid="+$(this).attr('groupid')+">"+anaphora_target_n+"</sup>");
		$(this).removeClass("_anaphora_target");
		anaphora_target_n++;
	});
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		var target_anaphora_n = $("#an" + target_id).prev("sup").text();
		var title = $(this).attr("title");
		if(title == 'Continous'){
			$(this).text("⇢" + target_anaphora_n);
			$(this).css({color: "#0055BB", background: "#EEFFFF"});
		}
		else{
			$(this).text("↷" + target_anaphora_n);
		}		 
		$("sup.relin").each(function(i,val){
			if($(val).text() == target_anaphora_n){
				$(val).attr("title",$(val).attr("title")+" "+title);
			}
		});
	});		
}

function delete_anaphora_links(relation_name, source_id, target_id){
	var actual_rel_num = $("#" + target_id).prev().text();
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
