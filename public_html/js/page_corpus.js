$(function(){
	$("#annotationSets").click(function(e){
		e.preventDefault();
		getAnnotationSets();
	});
	
	$(".setAnnotationSet").live("click",function(){
		setAnnotationSet($(this));
	});

	$("#eventGroups").click(function(e){
		e.preventDefault();
		getEventGroups();
	});
	
	$(".setEventGroup").live("click",function(){
		setEventGroup($(this));
	});
	
	$("#reportPerspectives").click(function(e){
		e.preventDefault();
		getReportPerspectives();
	});
	
	$(".setReportPerspective").live("click",function(){
		setReportPerspective($(this));
	});	

	$(".updateReportPerspective").live("change",function(){
		updateReportPerspective($(this));
	});	

	$("#usersInCorpus").click(function(e){
		e.preventDefault();
		getUserInCorpus();
	});	
	
	$(".setUserReportPerspective").live("click",function(){
		setUserReportPerspective($(this));
		$(this).parent().css('background',($(this).attr('checked') ? '#9DD943' : '#FFFFFF'));								
	});	
	
	$(".setCorpusRole").live("click",function(){
		setCorpusRole($(this));
		$(this).parent().css('background',($(this).attr('checked') ? '#9DD943' : '#FFFFFF'));								
	});	
	
	$(".ui-state-default").click(function(e){
		e.preventDefault();
		$(".ui-state-default").removeClass("ui-state-active ui-tabs-selected");	
		$(this).addClass("ui-state-active ui-tabs-selected");
		$("#tabs div").hide();
		$("#tabs div#"+$(this).attr('id')).show();
	});
});

function getAnnotationSets(){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_get_annotation_sets",
			corpus_id : $("#corpusId").text()
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var dialogHtml = 
						'<div class="annotationSetsDialog">'+
							'<table class="tablesorter">'+
								'<thead>'+
									'<tr>'+
										'<th>id</th>'+
										'<th>description</th>'+
										'<th>count</th>'+
										'<th>assign</th>'+
									'</tr>'+
								'</thead>'+
								'<tbody>';
					$.each(data,function(index,value){
						dialogHtml += 
							'<tr>'+
								'<td>'+value.id+'</td>'+
								'<td>'+value.description+'</td>'+
								'<td>'+value.count_ann+'</td>'+
								'<td><input class="setAnnotationSet" type="checkbox" setid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
							'</tr>';
					});
					dialogHtml += '</tbody></table></div>';
					var $dialogBox = $(dialogHtml).dialog({
						modal : true,
						height : 500,
						width : 'auto',
						title : 'Assign annotation sets<br/> to corpus',
						buttons : {
							Close: function() {
								$dialogBox.dialog("close");
							}
						},
						close: function(event, ui) {
							$dialogBox.dialog("destroy").remove();
							$dialogBox = null;
						}
					});	
				},
				function(){
					getAnnotationSets();
				}
			);								
		}
	});		
}

function setAnnotationSet($element){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_set_annotation_sets_corpora",
			corpus_id : $("#corpusId").text(),
			annotation_set_id : $element.attr('setid'),
			operation_type :  ($element.attr('checked') ? "add" : "remove")
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
				
				},
				function(){
					setAnnotationSet($element);
				}
			);								
		}
	});	
}

function getEventGroups(){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_get_event_groups",
			corpus_id : $("#corpusId").text()
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var dialogHtml = 
						'<div class="eventGroupsDialog">'+
							'<table class="tablesorter">'+
								'<thead>'+
									'<tr>'+
										'<th>id</th>'+
										'<th>name</th>'+
										'<th>description</th>'+
										//'<th>count</th>'+
										'<th>assign</th>'+
									'</tr>'+
								'</thead>'+
								'<tbody>';
					$.each(data,function(index,value){
						dialogHtml += 
							'<tr>'+
								'<td>'+value.id+'</td>'+
								'<td>'+value.name+'</td>'+
								'<td>'+value.description+'</td>'+
								//'<td>'+value.count_ann+'</td>'+
								'<td><input class="setEventGroup" type="checkbox" groupid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
							'</tr>';
					});
					dialogHtml += '</tbody></table></div>';
					var $dialogBox = $(dialogHtml).dialog({
						modal : true,
						height : 500,
						width : 'auto',
						title : 'Assign event groups<br/>to corpus',
						buttons : {
							Close: function() {
								$dialogBox.dialog("close");
							}
						},
						close: function(event, ui) {
							$dialogBox.dialog("destroy").remove();
							$dialogBox = null;
						}
					});	
				},
				function(){
					getEventGroups();
				}
			);								
		}
	});		
}

function setEventGroup($element){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_set_corpus_event_groups",
			corpus_id : $("#corpusId").text(),
			event_group_id : $element.attr('groupid'),
			operation_type :  ($element.attr('checked') ? "add" : "remove")
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
				
				},
				function(){
					setEventGroup($element);
				}
			);								
		}
	});	
}

function getReportPerspectives(){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_get_report_perspectives",
			corpus_id : $("#corpusId").text()
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var dialogHtml = 
						'<div class="reportPerspectivesDialog">'+
							'<table class="tablesorter">'+
								'<thead>'+
									'<tr>'+
										'<th>id</th>'+
										'<th>title</th>'+
										'<th>order</th>'+
										'<th>access</th>'+
										'<th>assign</th>'+
									'</tr>'+
								'</thead>'+
								'<tbody>';
					$.each(data,function(index,value){
						dialogHtml += 
							'<tr>'+
								'<td>'+value.id+'</td>'+
								'<td>'+value.title+'</td>'+
								'<td>'+value.order+'</td>'+
								'<td>'+
									'<select perspectiveid="'+value.id+'" class="updateReportPerspective">'+
										'<option perspectiveid="'+value.id+'" value="public" '+((value.access && value.access=="public") ? 'selected="selected"' : '' )+'>public</option>'+
										'<option perspectiveid="'+value.id+'" value="loggedin" '+((value.access && value.access=="loggedin") ? 'selected="selected"' : '' )+'>loggedin</option>'+
										'<option perspectiveid="'+value.id+'" value="role" '+((value.access && value.access=="role") ? 'selected="selected"' : '' )+'>role</option>'+
									'</select>'+
								'</td>'+
								'<td><input class="setReportPerspective" perspectivetitle="'+value.title+'" type="checkbox" perspectiveid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
							'</tr>';
					});
					dialogHtml += '</tbody></table></div>';
					var $dialogBox = $(dialogHtml).dialog({
						modal : true,
						height : 500,
						width : 'auto',
						title : 'Assign report perspectives <br/> to corpus',
						buttons : {
							Close: function() {
								$dialogBox.dialog("close");
							}
						},
						close: function(event, ui) {
							$dialogBox.dialog("destroy").remove();
							$dialogBox = null;
						}
					});	
				},
				function(){
					getReportPerspectives();
				}
			);								
		}
	});		
}

function setReportPerspective($element){
	var _data = {
			ajax : "corpus_set_corpus_and_report_perspectives",
			corpus_id : $("#corpusId").text(),
			perspective_id : $element.attr('perspectiveid'),
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : ($element.attr('checked') ? "add" : "remove")
		};
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : _data,				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					updatePerspectiveTable($element,($element.attr('checked') ? "add" : "remove"));
				},
				function(){
					setReportPerspective($element);
				}
			);								
		}
	});
}

function updateReportPerspective($element){
	if ($element.is("select") && $('input[perspectiveid="'+$element.attr('perspectiveid')+'"]').attr('checked')){
		$.ajax({
			async : false,
			url : "index.php",
			dataType : "json",
			type : "post",
			data : {
				ajax : "corpus_set_corpus_and_report_perspectives",
				corpus_id : $("#corpusId").text(),
				perspective_id : $element.attr('perspectiveid'),
				access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
				operation_type : "update"
			},				
			success : function(data){
				ajaxErrorHandler(data,
					function(){
						updatePerspectiveTable($element,"update");						
					},
					function(){
						updateReportPerspective($element);
					}
				);								
			}
		});
	}
}

function setUserReportPerspective($element){
	$element.after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	$element.attr("disabled", "disabled");
	var _data = {
			ajax : "corpus_set_corpus_perspective_roles",
			corpus_id : $("#corpusId").text(),
			perspective_id : $element.attr('perspective_id'),
			user_id : $element.attr('userid'),
			operation_type : ($element.attr('checked') ? "add" : "remove")
		};
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : _data,				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					$element.removeAttr("disabled");
					$(".ajax_indicator").remove();
				},
				function(){					
					setUserReportPerspective($element);
				}
			);								
		}
	});
}

function setCorpusRole($element){
	$element.after("<img class='ajax_indicator' src='gfx/ajax.gif'/>");
	$element.attr("disabled", "disabled");
	var _data = {
			ajax : "corpus_set_corpus_role",
			corpus_id : $("#corpusId").text(),
			user_id : $element.attr('userid'),
			role : $element.attr('role'),
			operation_type : ($element.attr('checked') ? "add" : "remove")
		};
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : _data,				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					$element.removeAttr("disabled");
					$(".ajax_indicator").remove();
				},
				function(){					
					setCorpusRole($element)
				}
			);								
		}
	});
}

function getUserInCorpus(){
	$.ajax({
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_get_users",
			corpus_id : $("#corpusId").text()
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var dialogHtml = 
						'<form method="POST" action="index.php?page=corpus&amp;corpus='+$("#corpusId").text()+'">'+
						'<input type="hidden" name="action" value="corpus_users_update"/>'+
							'<div class="usersInCorpusDialog">'+
								'<table class="tablesorter">'+
									'<thead>'+
										'<tr>'+
											'<th>user</th>'+
											'<th>assign</th>'+
										'</tr>'+
									'</thead>'+
									'<tbody>';
					$.each(data,function(index,value){
						dialogHtml += 
							'<tr>'+
								'<td>'+value.screename+'</td>'+
								'<td><input class="setUserInCorpus" type="checkbox" name="active_users[]" value="'+value.user_id+'"'+(value.role ? 'checked="checked"' : '')+'/></td>'+
							'</tr>';
					});
					dialogHtml += '</tbody></table></form></div>';
					var $dialogBox = $(dialogHtml).dialog({
						modal : true,
						height : 500,
						width : 300,
						title : 'Assign users to corpus',
						buttons : {
							Close: function() {
								$dialogBox.dialog("close");
							},
							Save: function() {
								$(this).submit();								
							}
						},
						close: function(event, ui) {
							$dialogBox.dialog("destroy").remove();
							$dialogBox = null;
						}
					});	
				}
			);								
		}
	});
}

function updatePerspectiveTable($element,operation_type){
	var perspective_id = $element.attr('perspectiveid'); 

	if(operation_type == "remove"){
		$("#perspectives td[perspective_id="+perspective_id+"]").remove();
		$("#perspectives th[perspective_id="+perspective_id+"]").remove();
	}
	else if(operation_type == "add"){
		var access = $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val();
		var title = $element.attr('perspectivetitle');
		$("#perspectives .thead").append("<th perspective_id='"+perspective_id+"' style='text-align: center'>"+title+"</th>");
		$("#perspectives .tbody").each(function(){
			var html="";
			if( access == "role"){
				html += "<td perspective_id='"+perspective_id+"' style='text-align: center;'>";
				html += "<input class='setUserReportPerspective' type='checkbox' userid=";
				html += $(this).attr('id');
				html += " perspective_id='"+perspective_id+"' value='1' />";
				html += "</td>";
			}
			else{
				html += "<td perspective_id='"+perspective_id+"' style='text-align: center;'>";
				html += "<i>"+access+"</i>";
			}
				html += "</td>";
			$(this).append(html);				
		});
	}
	else if(operation_type == "update"){
		var access = $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val();
		$("#perspectives .tbody").each(function(){
			var html="";
			if( access == "role"){
				var user_id = $(this).attr('id');
				html += "<input class='setUserReportPerspective' type='checkbox' userid='"+user_id+"' perspective_id='"+perspective_id+"' value='1' />";
			}
			else{
				html += "<i>"+access+"</i>";
			}
			$(this).find("td[perspective_id="+perspective_id+"]").html(html);	
			$(this).find("td[perspective_id="+perspective_id+"]").css('background', '#FFFFFF');					
		});		
	}	
}