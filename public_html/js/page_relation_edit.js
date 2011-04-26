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
			$("#relationTypesContainer .create").show();
			$("#relationTypesContainer .edit,#relationTypesContainer .delete").hide();
			$("#relationTypesContainer table > tbody").empty();
		}
		else if (containerType=="relationTypesContainer"){
			$("#relationTypesContainer .edit,#relationTypesContainer .delete").show();
		}
		get($(this));
	});
}); 


function get($element){
	var $container = $element.parents(".tableContainer:first");
	var containerName = $container.attr("id");
	var childId = "";
	if (containerName!="relationTypesContainer"){
		var _data = 	{ 
				ajax : "relation_type_get",
				parent_id : $element.children(":first").text()
			};
		if (containerName=="annotationSetsContainer"){
			childId = "relationTypesContainer";
			_data.parent_type = 'annotation_set';
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
						'<input id="elementName" type="text" />'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<input id="elementDescription" type="text" />'+
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
							ajax : "relation_type_add", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='relation_type'){
						_data.parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
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
									//console.log(data);
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
						'<th>Name</th>'+
						'<input id="elementName" type="text" value="'+$container.find('.hightlighted td:first').next().text()+'"/>'+
					'</tr>'+
					'<tr>'+
						'<th>Type</th>'+
						'<input id="elementDescription" type="text"  value="'+$container.find('.hightlighted td:last').text()+'"/>'+
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
							ajax : "relation_type_update", 
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
						'<th>Name</th>'+
						'<td>'+$container.find('.hightlighted td:first').next().text()+'</td>'+
					'</tr>'+
					'<tr>'+
						'<th>Type</th>'+
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
							ajax : "relation_type_delete", 
							element_type : elementType,
							element_id : $container.find('.hightlighted td:first').text()
						};
					//console.log(_data);
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
									if (elementType=="relation_type"){
										$("#relationTypesContainer .create").show();
										$("#relationTypesContainer .edit,#relationTypesContainer .delete").hide();
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