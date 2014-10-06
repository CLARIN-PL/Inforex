/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

const COOKIE_ANAPHORA_RELATION_ID = 'anaphoraRelationId';

/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){
	
	/** Disable text selection in the 'content' box */
	$("#content").attr('unselectable', 'on')
	    .css('-moz-user-select', 'none')
	    .each(function() { 
	        this.onselectstart = function() { return false; };
     });
	
	/** Set current relation type from cookie */
	if ( $.cookie(COOKIE_ANAPHORA_RELATION_ID) ){
		var relation_id = $.cookie(COOKIE_ANAPHORA_RELATION_ID);
		$("input[relation_id="+relation_id+"]").attr("checked", "checked");
	}
	
	/** When relation type is selected, then save its id in the cookie */
	$("input[name=quickAdd]").click(function(){
		var relation_id = $(this).attr('relation_id');
		$.cookie(COOKIE_ANAPHORA_RELATION_ID, relation_id);
	});
	
	$("#relationList span").live('mouseover',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).addClass("hightlighted");
	}).live('mouseout',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).removeClass("hightlighted");
	});
			
	$(".token").removeAttr("groupid").addClass("hiddenAnnotation");
	
	/** Left panel */
	$("#leftContent span").css("cursor","pointer").click(function(){
		var element = this;
		if ($(this).is(".token") && $(this).parent().is("span") && $(this).parent().text()==$(this).text()){
			element = $(this).parent();
		}
		
		if ( !$(element).hasClass("selectedSource")) 
			$(".selectedSource").toggleClass("selectedSource");
		$(element).toggleClass("selectedSource");
		
		$("#anaphoraSource").html("");
		$("#anaphoraSource").html($("span.selectedSource").clone().wrap('<div>').parent().html());
		$("#anaphoraSource .selectedSource").removeClass("selectedSource");
		
		return false;
	});
	
	/** Right panel */
	$("#rightContent span").css("cursor","pointer").click(function(e){
		
		if (e.ctrlKey){
			if ( !$(this).hasClass("selectedSource") ){
				$(".selectedSource").toggleClass("selectedSource");
			}
			$(this).toggleClass("selectedSource");
				
			$("#anaphoraSource").html("");
			$("#anaphoraSource").html($("span.selectedSource").clone().wrap('<div>').parent().html());
			$("#anaphoraSource .selectedSource").removeClass("selectedSource");
		}
		else{
		
			var $leftElement = $(".selectedSource"); 
			
			if ($leftElement.length>0){
				if ($("input[name=quickAdd]").is(":checked") && $("input[name='quickAdd']:checked").attr("relation_id")>0){
					if ($leftElement.is(".token")){				
						addAnnotation($leftElement);
					}
					$(this).addClass("selectedTarget");
					createRelation();
					
					return false;
				}
			}
			
			if ( !$(this).hasClass("selectedTarget") ) {
				$(".selectedTarget").removeClass("selectedTarget");
			}
			$(this).toggleClass("selectedTarget");
						
			$("#anaphoraTarget").html("");
			$("#anaphoraTarget").html($("span.selectedTarget").clone().removeAttr('id').wrap('<div>').parent().html());
			$("#anaphoraSource .selectedTarget").removeClass("selectedTarget");
		}
		
		return false;
		
	});
	
	$(".addRelation").live('click',function(){
		var $rightElement = $(".selectedTarget");
		if ($rightElement.length>0){
			var $leftElement = $(".selectedSource");
			if ($leftElement.length>0){
				if ($leftElement.is(".token")){				
					addAnnotation($leftElement);
					$("#anaphoraSource").html("");
					$("#anaphoraSource").html($("span.selectedSource").clone().wrap('<div>').parent().html());
					$("#anaphoraSource .selectedSource").removeClass("selectedSource");					
				}
				createRelation($(this).attr('relation_id'));
			}
		}
	});
	
	$(".relationDelete").live('click',function(){
		deleteRelation(this);
	});
	
	/** Utwórz etykiety dla jednostek połączonych anaforą. */
	create_anaphora_links();
});

function addAnnotation($element){
	$element.wrap("<xyz/>");
	var content_html = $.trim($("#leftContent").html());
	
	content_html = content_html.replace(/<sup[^>]*>(.*?)<\/sup>/g, "");
	content_html = content_html.replace(/<xyz>(.*?)<\/xyz>/, fromDelimiter+"$1"+toDelimiter);
	content_no_html = content_html.replace(/<\/?[^>]+>/gi, '');
	var from = content_no_html.indexOf(fromDelimiter) + fromDelimiter.length;
	var to = content_no_html.indexOf(toDelimiter);
	var text = content_no_html.substring(from, to);
	content_no_html = content_no_html.replace(/\s/g, '');
	from = content_no_html.indexOf(fromDelimiter);
	to = content_no_html.indexOf(toDelimiter) - fromDelimiter.length - 1;
	status_processing("dodawanie anotacji ...");
	if (from < 0 || to < 0 ){
		status_fade();
		dialog_error("Wystąpił błąd z odczytem granic anotacji. Odczytano ["+from+","+to+"]. <br/><br/>Zgłoś błąd administratorowi.");
		return;
	}
	
	var params = {
			report_id: $("#report_id").val(), 
			from: from,
			to: to,
			text: text,
			type: 'anafora_wyznacznik'
	};
	
	var success = function(data){
		$("#content xyz").wrapInner("<span id='new'/>");
		$("#content xyz").replaceWith( $("#content xyz").contents() );
	
		if (data['success']){
			$("#leftContent span").removeClass("selectedSource");
			var annotation_id = data['annotation_id'];
			var node = $("#content span#new");
			var title = "an#"+annotation_id+":anafora_wyznacznik";
			node.attr('title', title);
			node.attr('id', "an"+annotation_id);
			node.attr('groupid', '9');
			node.attr('class', 'anafora_wyznacznik selectedSource');
			console_add("anotacja <b> "+title+" </b> została dodana do tekstu <i>"+text+"</i>");
		}else{
		    dialog_error(data['error']);
		    $("span#new").after($("span#new").html());
		    $("span#new").remove();
		}
	};
	
	var complete = function(){
		status_fade();
	};
	
	doAjaxSync("report_add_annotation", params, success, null, complete, null, null, true);
}

/**
 * 
 * @param relation_id
 * @return
 */
function createRelation(relation_id){
	if ( !relation_id )
		relation_id = $("input[name='quickAdd']:checked").attr("relation_id");
	var $source = $("#content .selectedSource");
	var $target = $("#content .selectedTarget");
	var sourceId = $source.attr("id").replace("an","");
	var targetId = $target.attr("id").replace("an","");
	if ($("td.relationDelete[source_id='"+sourceId+"'][target_id='"+targetId+"'][type_id='"+relation_id+"']").length==0) {	
		
		var params = {
			source_id : sourceId,
			target_id : targetId,
			relation_type_id : relation_id
		};
		
		var success = function(data){
			$("#relationListContainer").append(
				'<tr>'+
					'<td><span class="'+$source.attr('title').split(":")[1]+'" title="an#'+sourceId+':'+$source.attr('title').split(":")[1]+'"> '+$source.text()+'</span></td>'+
					'<td>'+$("span.addRelation[relation_id='"+relation_id+"']").text()+'</td>'+
					'<td><span class="'+$target.attr('title').split(":")[1]+'" title="an#'+targetId +':'+$target.attr('title').split(":")[1]+'"> '+$target.text()+'</span></td>'+
					'<td class="relationDelete" source_id="'+sourceId+'" target_id="'+targetId+'" relation_id="'+data.relation_id+'" type_id="'+relation_id+'"  style="cursor:pointer">X</td>'+
				'</tr>'	
			);
			$("#content .selectedSource").after("<sup class='rel' target='"+targetId+"'>↦</sup>");
			create_anaphora_links();
		};
		
		var login = function(){
			createRelation();
		};
		
		doAjaxSyncWithLogin("report_add_annotation_relation", params, success, login);
	}
}

function deleteRelation(deleteHandler){
	var relationId = $(deleteHandler).attr("relation_id");
	var sourceId = $(deleteHandler).attr("source_id");
	var targetId = $(deleteHandler).attr("target_id");
	
	var xPosition = $(deleteHandler).offset().left-$(window).scrollLeft();
	var yPosition = $(deleteHandler).offset().top - $(window).scrollTop();
	
	var $dialogBox = 
		$('<div class="deleteDialog annotations">Are you sure?</div>')
		.dialog({
			modal : true,
			title : 'Delete relation',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					
					var params = {
						relation_id : relationId,
						source_id : sourceId,
						target_id : targetId
					};
					
					var success = function(data){
						$(deleteHandler).parent().remove();
						$.each(data.deletedId, function(index, value){
							$("#an"+value).children(":first").unwrap().nextUntil(":not('sup')").remove();
						});
					};
					
					var login = function(){
						delete_relation(deleteHandler);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					doAjax("report_delete_annotation_relation_anaphora", params, success, null, complete, null, login, true);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}

		});
	$dialogBox.dialog("option", "position",[xPosition- $dialogBox.width(), yPosition]);
}

/** 
 * Tworzy wizualizację połączeń anaforycznych. Indeksuje anotacje, które biorą udział w relacji.
 */
function create_anaphora_links(){
	$("sup.relin").remove();
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		$("#an" + target_id).addClass("_anaphora_target");
	});
	var anaphora_target_n = 1;
	$("span._anaphora_target").each(function(){
		$(this).before("<sup class='relin'>"+anaphora_target_n+"</sup>");
		$(this).removeClass("_anaphora_target");
		anaphora_target_n++;
	});
	$("sup.rel").each(function(){
		var target_id = $(this).attr('target');
		var target_anaphora_n = $("#an" + target_id).prev("sup").text();
		$(this).text("↦" + target_anaphora_n);
	});
}
