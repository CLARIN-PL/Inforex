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
	
	$(".userReportPerspectives").click(function(e){
		e.preventDefault();
		getUserReportPerspectives($(this));
	});	
	
	$(".setUserReportPerspective").live("click",function(){
		setUserReportPerspective($(this));
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
								'<td><input class="setReportPerspective" type="checkbox" perspectiveid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
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
					},
					function(){
						updateReportPerspective($element);
					}
				);								
			}
		});
	}
}


function getUserReportPerspectives($element){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_get_user_report_perspectives",
			corpus_id : $("#corpusId").text(),
			user_id : $element.attr('userid')
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var userId = $element.attr('userid');
					var dialogHtml = 
						'<div class="userReportPerspectivesDialog">'+
							'<table class="tablesorter">'+
								'<thead>'+
									'<tr>'+
										'<th>id</th>'+
										'<th>title</th>'+
										'<th>order</th>'+
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
								'<td><input class="setUserReportPerspective" type="checkbox" userid="'+userId+'" perspectiveid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
							'</tr>';
					});
					dialogHtml += '</tbody></table></div>';
					var $dialogBox = $(dialogHtml).dialog({
						modal : true,
						height : 500,
						width : 'auto',
						title : 'Assign report perspectives <br/> to user in corpus',
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
					getUserReportPerspectives($element);
				}
			);								
		}
	});		
}

function setUserReportPerspective($element){
	var _data = {
			ajax : "corpus_set_corpus_perspective_roles",
			corpus_id : $("#corpusId").text(),
			perspective_id : $element.attr('perspectiveid'),
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
				
				},
				function(){
					setUserReportPerspective($element);
				}
			);								
		}
	});
}
