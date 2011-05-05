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
		if (containerType=="eventGroupsContainer"){
			$("#eventGroupsContainer .edit,#eventGroupsContainer .delete").show();
			$("#eventTypesContainer .create").show();
			$("#eventTypesContainer .edit,#eventTypesContainer .delete").hide();
			$("#eventTypeSlotsContainer span").hide();
			$("#eventTypeSlotsContainer table > tbody").empty();
		}
		else if (containerType=="eventTypesContainer"){
			$("#eventTypesContainer .edit,#eventTypesContainer .delete").show();
			$("#eventTypeSlotsContainer .create").show();
			$("#eventTypeSlotsContainer .edit,#eventTypeSlotsContainer .delete").hide();
		}
		else {
			$("#eventTypeSlotsContainer .edit,#eventTypeSlotsContainer .delete").show();
		}
		get($(this));
	});
}); 


function get($element){
	var $container = $element.parents(".tableContainer:first");
	var containerName = $container.attr("id");
	var childId = "";
	if (containerName!="eventTypeSlotsContainer"){
		var _data = 	{ 
				ajax : "event_edit_get",
				parent_id : $element.children(":first").text()
			};
		if (containerName=="eventGroupsContainer"){
			childId = "eventTypesContainer";
			_data.parent_type = 'event_group';
		}
		else {
			childId = "eventTypeSlotsContainer";
			_data.parent_type = 'event_type';
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
							tableRows+=
							'<tr>'+
								'<td>'+value.id+'</td>'+
								'<td>'+value.name+'</td>'+
								'<td>'+value.description+'</td>'+
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
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td><input id="elementName" type="text" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><textarea id="elementDescription" rows="4"></textarea></td>'+
					'</tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Create '+elementType.replace(/_/g," "),
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "event_edit_add", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='event_type'){
						_data.parent_id = $("#eventGroupsTable .hightlighted > td:first").text();
					}
					else if (elementType=='event_type_slot'){
						_data.parent_id = $("#eventTypesTable .hightlighted > td:first").text();
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
									//update lastrowid in data
									$container.find("table > tbody").append(
										'<tr>'+
											'<td>'+data.last_id+'</td>'+
											'<td>'+_data.name_str+'</td>'+
											'<td>'+_data.desc_str+'</td>'+
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
					
					//todelete:
					//$dialogBox.dialog("close");
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
	var $dialogBox = 
		$('<div class="editDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td><input id="elementName" type="text" value="'+$container.find('.hightlighted td:first').next().text()+'"/></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:last').text()+'</textarea></td>'+
					'</tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Edit '+elementType.replace(/_/g," ")+ ' #'+$container.find('.hightlighted td:first').text(),
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "event_edit_update", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
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
									//update lastrowid in data
									$container.find(".hightlighted:first").html(
										'<td>'+$container.find(".hightlighted td:first").text()+'</td>'+
										'<td>'+_data.name_str+'</td>'+
										'<td>'+_data.desc_str+'</td>'
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
					
					//todelete:
					//$dialogBox.dialog("close");
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
	var $dialogBox = 
		$('<div class="deleteDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td>'+$container.find('.hightlighted td:first').next().text()+'</td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td>'+$container.find('.hightlighted td:last').text()+'</td>'+
					'</tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Delete '+elementType.replace(/_/g," ")+ ' #'+$container.find('.hightlighted td:first').text()+"?",
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "event_edit_delete", 
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
									//update lastrowid in data
									$container.find(".hightlighted:first").remove();
									if (elementType=="event_group"){
										$("#eventGroupsContainer .edit,#eventGroupsContainer .delete").hide();
										$("#eventTypesContainer span").hide();
										$("#eventTypeSlotsContainer span").hide();
										$("#eventTypesContainer table > tbody").empty();
										$("#eventTypeSlotsContainer table > tbody").empty();
									}
									else if (elementType=="event_type"){
										$("#eventTypesContainer .create").show();
										$("#eventTypesContainer .edit,#eventTypesContainer .delete").hide();
										$("#eventTypeSlotsContainer span").hide();
										$("#eventTypeSlotsContainer table > tbody").empty();
									}
									else {
										$("#eventTypeSlotsContainer .edit,#eventTypeSlotsContainer .delete").hide();
									}

									
									/*$("#eventTypeSlotsContainer table > tbody").empty();									
									$("#eventTypesContainer table > tbody").empty();*/									
									
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									remove($element);
								}
							);								
						}
					});	
					
					//todelete:
					//$dialogBox.dialog("close");
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
	
}