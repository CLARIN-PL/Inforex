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
			$("#anaphoraTarget").html($("span.selectedTarget").clone().wrap('<div>').parent().html());
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
				}
				createRelation($(this).attr('relation_id'));
			}
		}
	});
	
	$(".relationDelete").live('click',function(){
		deleteRelation(this);
	});
	
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
	$.ajax({
		type: 	'POST',
		url: 	"index.php",
		async : false,
		data:	{ 	
					ajax: "report_add_annotation", 
					report_id: $("#report_id").val(), 
					from: from,
					to: to,
					text: text,
					type: 'anafora_wyznacznik'
				},
		success:function(data){
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
		jQuery.ajax({
			async : false,
			url : "index.php",
			dataType : "json",
			type : "post",
			data : { 
				ajax : "report_add_annotation_relation", 
				source_id : sourceId,
				target_id : targetId,
				relation_type_id : relation_id
			},				
			success : function(data){
				ajaxErrorHandler(data,
					function(){
						$("#relationListContainer").append(
							'<tr>'+
								'<td><span class="'+$source.attr('title').split(":")[1]+'" title="an#'+sourceId+':'+$source.attr('title').split(":")[1]+'"> '+$source.text()+'</span></td>'+
								'<td>'+$("span.addRelation[relation_id='"+relation_id+"']").text()+'</td>'+
								'<td><span class="'+$target.attr('title').split(":")[1]+'" title="an#'+targetId +':'+$target.attr('title').split(":")[1]+'"> '+$target.text()+'</span></td>'+
								'<td class="relationDelete" source_id="'+sourceId+'" target_id="'+targetId+'" relation_id="'+data.relation_id+'" type_id="'+relation_id+'"  style="cursor:pointer">X</td>'+
							'</tr>'	
						);
						$("#content .selectedSource").after("<sup class='rel'>↦</sup>");
					},
					function(){
						createRelation();
					}
				);
			}
		});			
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
					jQuery.ajax({
						async : false,
						url : "index.php",
						dataType : "json",
						type : "post",
						data : { 
							ajax : "report_delete_annotation_relation_anaphora", 
							relation_id : relationId,
							source_id : sourceId,
							target_id : targetId
						},				
						success : function(data){
							ajaxErrorHandler(data,
								function(){						
									$(deleteHandler).parent().remove();
									$dialogBox.dialog("close");
									$.each(data.deletedId, function(index, value){
										$("#an"+value).children(":first").unwrap().nextUntil(":not('sup')").remove();
									});
								},
								function(){
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
}
