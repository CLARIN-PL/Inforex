/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

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
				parent_id : $element.children(":first").text()
			};
		if (containerName=="annotationSetsContainer"){
			childId = "relationTypesContainer";
			_data.parent_type = 'annotation_set';
		}

		var success = function(data){
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
		};
		
		var login = function(){
			get($element);
		};
		
		doAjaxSyncWithLogin("relation_type_get", _data, success, login);			
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
							//ajax : "relation_type_add", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='relation_type'){
						_data.parent_id = $("#annotationSetsTable .hightlighted > td:first").text();
					}
					
					var success = function(data){
						$container.find("table > tbody").append(
								'<tr>'+
									'<td>'+data.last_id+'</td>'+
									'<td>'+_data.name_str+'</td>'+
									'<td>'+_data.desc_str+'</td>'+
								'</tr>'
							);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						add($element);
					};
					
					doAjaxSync("relation_type_add", _data, success, null, complete, null, login);
						
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
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType,
							
							element_id : $container.find('.hightlighted td:first').text()
						};
					
					var success = function(data){
						$container.find(".hightlighted:first").html(
								'<td>'+$container.find(".hightlighted td:first").text()+'</td>'+
								'<td>'+_data.name_str+'</td>'+
								'<td>'+_data.desc_str+'</td>'
							);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						edit($element);
					};
					
					doAjaxSync("relation_type_update", _data, success, null, complete, null, login);
								
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
							ajax : "relation_type_delete", 
							element_type : elementType,
							element_id : $container.find('.hightlighted td:first').text()
						};
					
					var success = function(data){
						$container.find(".hightlighted:first").remove();
						if (elementType=="relation_type"){
							$("#relationTypesContainer .create").show();
							$("#relationTypesContainer .edit,#relationTypesContainer .delete").hide();
						}
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						remove($element);
					};
					
					doAjaxSync("relation_type_update", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
	
}