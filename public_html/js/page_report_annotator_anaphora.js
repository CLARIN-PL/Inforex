var _wAnnotation = null;
/**
 * Przypisanie akcji po wczytaniu się strony.
 */
$(document).ready(function(){
	/*$("#relation_type").change(function(){
		block_existing_relations();
	});*/
	_wAnnotation = new WidgetAnnotation();
	
	$("#relationList span").live('mouseover',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).addClass("hightlighted");
	}).live('mouseout',function(){
		$("#"+$(this).attr('title').split(":")[0].replace("#","")).removeClass("hightlighted");
	});
	
	/*$("div.deleteRelation").live('click',function(){
		delete_relation(this);
	});*/
	
	
	/*$("#content span:not(.hiddenAnnotation)").live("click", function(){
		if ($(this).hasClass("selected")) $(this).removeClass("selected"); 
		else {
			$("#content span.selected").removeClass("selected");
			$(this).addClass("selected");
		}
	});*/
	
	$(".token").removeAttr("groupid").addClass("hiddenAnnotation");
	
	$("#leftContent span").css("cursor","pointer").click(function(){
		if ($(this).is(".token") && $(this).parent().is("span") && $(this).parent().text()==$(this).text()){
			if ($(this).parent().hasClass("selected")) $(this).parent().removeClass("selected");
			else {
				$("#leftContent span").removeClass("selected");
				$(this).parent().addClass("selected");
			}
		}
		else {
			if ($(this).hasClass("selected")) $(this).removeClass("selected");
			else {
				$("#leftContent span").removeClass("selected");				
				$(this).addClass("selected");
			}
		}
		return false;
	});
	
	
	$("#rightContent span").css("cursor","pointer").click(function(){		
		var $leftElement = $("#leftContent .selected"); 
		if ($leftElement.length>0){
			if ($("#quickAdd").is(":checked") && $("input[name='quickAdd']:checked").length>0){
				if ($leftElement.is(".token")){				
					addAnnotation($leftElement);
				}
				addRelation($(this));
				$("#rightContent span").removeClass("selected");
				$(this).addClass("selected");
				
				return false;
			}
		}
		if ($(this).hasClass("selected")) $(this).removeClass("selected");
		else {
			$("#rightContent span").removeClass("selected");
			$(this).addClass("selected");
		}
		return false;
		
	});
	
	$(".addRelation").live('click',function(){
		var $rightElement = $("#rightContent .selected");
		if ($rightElement.length>0){
			var $leftElement = $("#leftContent .selected");
			if ($leftElement.length>0){
				if ($leftElement.is(".token")){				
					addAnnotation($leftElement);
				}
				addRelation($rightElement, $(this).attr('relation_id'));
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
						$("#leftContent span").removeClass("selected");
						var annotation_id = data['annotation_id'];
						var node = $("#content span#new");
						var title = "an#"+annotation_id+":anafora_wyznacznik";
						node.attr('title', title);
						node.attr('id', "an"+annotation_id);
						node.attr('groupid', '9');
						node.attr('class', 'anafora_wyznacznik selected');
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



/*function block_existing_relations(){
	//log(AnnotationRelation);
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

*/
function addRelation($target, relation_id){
	if (!relation_id)
		relation_id = $("input[name='quickAdd']:checked").attr("relation_id");
	var $source = $("#leftContent span.selected");
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
						//log(data);
						$("#relationListContainer").append(
							'<tr>'+
								'<td><span class="'+$source.attr('title').split(":")[1]+'" title="an#'+sourceId+':'+$source.attr('title').split(":")[1]+'"> '+$source.text()+'</span></td>'+
								'<td>'+$("span.addRelation[relation_id='"+relation_id+"']").text()+'</td>'+
								'<td><span class="'+$target.attr('title').split(":")[1]+'" title="an#'+targetId +':'+$target.attr('title').split(":")[1]+'"> '+$target.text()+'</span></td>'+
								'<td class="relationDelete" source_id="'+sourceId+'" target_id="'+targetId+'" relation_id="'+data.relation_id+'" type_id="'+relation_id+'"  style="cursor:pointer">X</td>'+
							'</tr>'	
						);
					},
					function(){
						addRelation($target);
					}
				);
			}
		});			
	}
}

function deleteRelation(deleteHandler){
	relationId = $(deleteHandler).attr("relation_id");
	xPosition = $(deleteHandler).offset().left-$(window).scrollLeft();
	yPosition = $(deleteHandler).offset().top - $(window).scrollTop();
	
	
	$dialogBox = 
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
							ajax : "report_delete_annotation_relation", 
							relation_id : relationId
						},				
						success : function(data){
							ajaxErrorHandler(data,
								function(){						
									$(deleteHandler).parent().remove();
									$dialogBox.dialog("close");
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



