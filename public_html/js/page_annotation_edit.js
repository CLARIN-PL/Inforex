//ajaxErrorHandler is located in tmp.js

$(function(){
	$(".create").click(function(){
		add($(this));
	});
	
	$(".edit").click(function(){
		edit($(this));
	});

	$(".delete").click(function(){
		remove($(this));
	});

	
	$(".tableContent tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		containerType = $(this).parents(".tableContainer:first").attr('id');
		if (containerType=="annotationSetsContainer"){
			$("#annotationSetsContainer .edit,#annotationSetsContainer .delete").show();
			$("#annotationSubsetsContainer .create").show();
			$("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").hide();
			$("#annotationTypesContainer span").hide();
			$("#annotationTypesContainer table > tbody").empty();
		}
		else if (containerType=="annotationSubsetsContainer"){
			$("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").show();
			$("#annotationTypesContainer .create").show();
			$("#annotationTypesContainer .edit,#annotationTypesContainer .delete").hide();
		}
		else {
			$("#annotationTypesContainer .edit,#annotationTypesContainer .delete").show();
		}
		get($(this));
	});
}); 


function get($element){
	var $container = $element.parents(".tableContainer:first");
	var containerName = $container.attr("id");
	var childId = "";
	if (containerName!="annotationTypesContainer"){
		var _data = 	{ 
				ajax : "annotation_edit_get",
				parent_id : $element.children(":first").text()
			};
		if (containerName=="annotationSetsContainer"){
			childId = "annotationSubsetsContainer";
			_data.parent_type = 'annotation_set';
		}
		else {
			childId = "annotationTypesContainer";
			_data.parent_type = 'annotation_subset';
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
						var tableRows = "";
						$.each(data,function(index, value){
							if (_data.parent_type=="annotation_set")
								tableRows+=
								'<tr>'+
									'<td>'+value.id+'</td>'+
									'<td>'+value.description+'</td>'+
								'</tr>';
							else if (_data.parent_type=="annotation_subset") 
								tableRows+=
									'<tr>'+
										'<td>'+value.name+'</td>'+
										'<td>'+(value.short==null ? "" : value.short)+'</td>'+
										'<td>'+(value.description==null ? "" : value.description)+'</td>'+
										'<td style="display:none">'+(value.css==null ? "" : value.css)+'</td>'+
									'</tr>';
						});
						$("#"+childId+" table > tbody").html(tableRows);
					},
					function(){
						get($element);
					}
				);								
			}
		});		
	}
}

function add($element){	
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");
	var $dialogBox = null;
	if (elementType=="annotation_set" || elementType=="annotation_subset")
		$dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><input id="elementDescription" type="text" /></td>'+
					'</tr>'+
				'</table>'+
		'</div>');
	else if (elementType=="annotation_type")
		$dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td><input id="elementName" type="text" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Short desc.</th>'+
						'<td><input id="elementShort" type="text" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><input id="elementDescription" type="text" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Css</th>'+
						'<td><input id="elementCss" type="text" /></td>'+
					'</tr>'+					
				'</table>'+
		'</div>');
	$dialogBox.dialog({
			modal : true,
			title : 'Create '+elementType.replace(/_/g," "),
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "annotation_edit_add", 
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='annotation_subset'){
						_data.parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
					}
					else if (elementType=='annotation_type'){
						_data.parent_id = $("#annotationSubsetsTable .hightlighted > td:first").text();
						_data.name_str = $("#elementName").val();
						_data.short = $("#elementShort").val();
						_data.css = $("#elementCss").val();
						_data.set_id = $("#annotationSetsTable .hightlighted > td:first").text();
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
									if (elementType=="annotation_set" || elementType=="annotation_subset")
										$container.find("table > tbody").append(
											'<tr>'+
												'<td>'+data.last_id+'</td>'+
												'<td>'+_data.desc_str+'</td>'+
											'</tr>'
										);
									else if (elementType=="annotation_type")
										$container.find("table > tbody").append(
												'<tr>'+
													'<td>'+_data.name_str+'</td>'+
													'<td>'+_data.short+'</td>'+
													'<td>'+_data.desc_str+'</td>'+
													'<td style="display:none">'+_data.css+'</td>'+
												'</tr>'
											);
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									add($element);
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

function edit($element){	
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");
	var $dialogBox = null;
	if (elementType=="annotation_set" || elementType=="annotation_subset")
		$dialogBox = 
		$('<div class="editDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><input id="elementDescription" type="text" value="'+$container.find('.hightlighted td:first').next().text()+'"/></td>'+
					'</tr>'+
				'</table>'+
		'</div>');
	else if (elementType=="annotation_type"){
		$vals = $container.find('.hightlighted td');
		$dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Short desc.</th>'+
						'<td><input id="elementShort" type="text" value="'+$($vals[1]).text()+'"/></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><input id="elementDescription" type="text" value="'+$($vals[2]).text()+'" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Css</th>'+
						'<td><input id="elementCss" type="text" value="'+$($vals[3]).text()+'"/></td>'+
					'</tr>'+					
				'</table>'+
		'</div>');
	}
	$dialogBox.dialog({
			modal : true,
			title : 'Edit '+elementType.replace(/_/g," "),
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "annotation_edit_update", 
							desc_str : $("#elementDescription").val(),
							element_id : $container.find('.hightlighted td:first').text(),							
							element_type : elementType
						};
					if (elementType=='annotation_subset'){
						_data.parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
					}
					else if (elementType=='annotation_type'){
						_data.parent_id = $("#annotationSubsetsTable .hightlighted > td:first").text();
						_data.short = $("#elementShort").val();
						_data.css = $("#elementCss").val();
						_data.set_id = $("#annotationSetsTable .hightlighted > td:first").text();
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
									if (elementType=="annotation_set" || elementType=="annotation_subset")
										$container.find(".hightlighted:first").html(
											'<td>'+$container.find(".hightlighted td:first").text()+'</td>'+
											'<td>'+_data.desc_str+'</td>'
										);
									else if (elementType=="annotation_type")
										$container.find(".hightlighted:first").html(
												'<td>'+_data.element_id+'</td>'+
												'<td>'+_data.short+'</td>'+
												'<td>'+_data.desc_str+'</td>'+
												'<td style="display:none">'+_data.css+'</td>'
											);
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									edit($element);
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


function remove($element){	
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");
	var $dialogBox = null;
	if (elementType=="annotation_set" || elementType=="annotation_subset")
		$dialogBox = 
		$('<div class="deleteDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td>'+$container.find('.hightlighted td:first').next().text()+'</td>'+
					'</tr>'+
				'</table>'+
		'</div>');
	else if (elementType=="annotation_type"){
		$vals = $container.find('.hightlighted td');
		$dialogBox = 
		$('<div class="deleteDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Short desc.</th>'+
						'<td>'+$($vals[1]).text()+'</td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td>'+$($vals[2]).text()+'</td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Css</th>'+
						'<td>'+$($vals[3]).text()+'</td>'+
					'</tr>'+					
				'</table>'+
		'</div>');
	}
	$dialogBox.dialog({
			modal : true,
			title : 'Delete '+elementType.replace(/_/g," ")+ ' #'+$container.find('.hightlighted td:first').text()+"?",
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "annotation_edit_delete", 
							element_type : elementType,
							element_id : $container.find('.hightlighted td:first').text()
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
									$container.find(".hightlighted:first").remove();
									if (elementType=="annotation_set"){
										$("#annotationSetsContainer .edit,#annotationSetsContainer .delete").hide();
										$("#annotationSubsetsContainer span").hide();
										$("#annotationTypesContainer span").hide();
										$("#annotationSubsetsContainer table > tbody").empty();
										$("#annotationTypesContainer table > tbody").empty();
									}
									else if (elementType=="annotation_subset"){
										$("#annotationSubsetsContainer .create").show();
										$("#annotationSubsetsContainer .edit,#annotationSubsetsContainer .delete").hide();
										$("#annotationTypesContainer span").hide();
										$("#annotationTypesContainer table > tbody").empty();
									}
									else {
										$("#annotationTypesContainer .edit,#annotationTypesContainer .delete").hide();
									}
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									remove($element);
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