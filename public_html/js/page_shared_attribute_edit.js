/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$("#create_shared_attribute").click(function(){
		add_shared_attribute();
	});
	
	$("#delete_shared_attribute").click(function(){
		delete_shared_attribute();
	});
	
	$("#sharedAttributesTable tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#create_shared_attribute,#delete_shared_attribute").show();		
		if ($(this).find("td").eq(2).text() == "enum"){
			$("#create_shared_attribute_enum").show();
			$("#delete_shared_attribute_enum").hide();
			$("#move_detach").hide();	
			$("#move_attach").hide();
			get_shared_attributes_enum();
			get_annotation_types();
		}
		else { 
			$("#sharedAttributesEnumTable > tbody").empty();
			$("#create_shared_attribute_enum").hide();
			$("#delete_shared_attribute_enum").hide();
			$("#annotationTypesDetachedTable > tbody").empty();
			$("#annotationTypesAttachedTable > tbody").empty();
			$("#move_detach").hide();	
			$("#move_attach").hide();
			get_annotation_types();
		}
	});
	
	$("#create_shared_attribute_enum").click(function(){
		add_shared_attribute_enum();
	});
	
	
	$("#sharedAttributesEnumTable tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#create_shared_attribute_enum,#delete_shared_attribute_enum").show();		
	});	
	
	$("#delete_shared_attribute_enum").click(function(){
		delete_shared_attribute_enum();
	});	
	
	$("#annotationTypesAttachedTable tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#move_detach").show();		
	});	
	
	$("#annotationTypesDetachedTable tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$("#move_attach").show();		
	});		
	
	$("#move_attach").click(function(){
		add_annotation_type();
		$("#move_attach").hide();
	});	
	
	$("#move_detach").click(function(){
		delete_annotation_type();
		$("#move_detach").hide();
	});	
	
}); 


function get_shared_attributes_enum(){
	var _data = { 
		shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text()
	};

	var success = function(data){
		var tableRows = "";
		$.each(data,function(index, value){
			tableRows+=
			'<tr>'+
				'<td>'+value.value+'</td>'+
				'<td>'+value.description+'</td>'+
			'</tr>';
		});
		$("#sharedAttributesEnumTable > tbody").html(tableRows);
	};
	
	var login = function(){
		get_shared_attributes_enum();
	};
	
	doAjaxSyncWithLogin("shared_attribute_enum_get", _data, success, login);
}

function get_annotation_types(){
	var _data = { 
		shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text()
	};

	var success = function(data){
		var tableRowsAttached = "";
		var tableRowsDetached = "";
		$.each(data,function(index, value){
			if (value.shared_attribute_id)
				tableRowsAttached += 
				'<tr>' +
					'<td>' + value.annotation_type_id + '</td>' +
					'<td>' + value.name + '</td>' +
				'</tr>';
			else
				tableRowsDetached += 
					'<tr>' +
						'<td>' + value.annotation_type_id + '</td>' +
						'<td>' + value.name + '</td>' +
					'</tr>';
				
		});
		$("#annotationTypesAttachedTable > tbody").html(tableRowsAttached);
		$("#annotationTypesDetachedTable > tbody").html(tableRowsDetached);
	};
	
	var login = function(){
		get_annotation_types();
	};
	
	doAjaxSyncWithLogin("shared_attribute_annotation_types_get", _data, success, login);
}

function add_annotation_type(){
	var _data = 	{ 
			shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
			annotation_type_id : $("#annotationTypesDetachedTable .hightlighted td:first").text(),
			name : $("#annotationTypesDetachedTable .hightlighted td:last").text()
		};
	var success = function(data){
		$("#annotationTypesAttachedTable > tbody").append(
				'<tr>'+
					'<td>'+_data.annotation_type_id+'</td>'+
					'<td>'+_data.name+'</td>'+
				'</tr>'
		);	
		$("#annotationTypesDetachedTable .hightlighted").remove();
	};
	
	var login = function(){
		add_annotation_type();
	};
	
	doAjaxSyncWithLogin("annotation_type_shared_attribute_add", _data, success, login);	
}

function delete_annotation_type(){
	var _data = 	{ 
			shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
			annotation_type_id : $("#annotationTypesAttachedTable .hightlighted td:first").text(),
			name : $("#annotationTypesAttachedTable .hightlighted td:last").text()
		};
	var success = function(data){
		$("#annotationTypesDetachedTable > tbody").append(
				'<tr>'+
					'<td>'+_data.annotation_type_id+'</td>'+
					'<td>'+_data.name+'</td>'+
				'</tr>'
		);	
		$("#annotationTypesAttachedTable .hightlighted").remove();
	};
	
	var login = function(){
		delete_annotation_type();
	};
	
	doAjaxSyncWithLogin("annotation_type_shared_attribute_delete", _data, success, login);	
}

function add_shared_attribute(){	
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td><input id="shared_attribute_name" type="text" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Type</th>'+
						'<td><select id="shared_attribute_type">' + 
							'<option value="string" checked="checked">string</option>' +
							'<option value="enum">enum</option>' +
						'</select></td>' +
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><textarea id="shared_attribute_desc" rows="4"></textarea></td>'+
					'</tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Create shared attribute',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							name_str : $("#shared_attribute_name").val(),
							type_str : $("#shared_attribute_type").val(),
							desc_str : $("#shared_attribute_desc").val(),
						};
					var success = function(data){
						$("#sharedAttributesContainer").find("table > tbody").append(
								'<tr>'+
									'<td>'+data.last_id+'</td>'+
									'<td>'+_data.name_str+'</td>'+
									'<td>'+_data.type_str+'</td>'+
									'<td>'+_data.desc_str+'</td>'+
								'</tr>'
							);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						add_shared_attribute();
					};
					
					doAjaxSync("shared_attribute_add", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});	
}


function delete_shared_attribute(){	
	var $container = $("#sharedAttributesTable");
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
			title : 'Delete shared attribute #'+$container.find('.hightlighted td:first').text()+"?",
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							shared_attribute_id : $container.find('.hightlighted td:first').text()
						};
					
					var success = function(data){
						$container.find(".hightlighted:first").remove();
						$("#delete_shared_attribute").hide();						
						$("#sharedAttributesEnumTable > tbody").empty();
						$("#create_shared_attribute_enum").hide();						
						$("#delete_shared_attribute_enum").hide();	
						$("#annotationTypesAttachedTable > tbody").empty();
						$("#annotationTypesDetachedTable > tbody").empty();						
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						delete_shared_attribute();
					};
					
					doAjaxSync("shared_attribute_delete", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
	
}

function add_shared_attribute_enum(){	
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Value</th>'+
						'<td><input id="shared_attribute_value" type="text" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><textarea id="shared_attribute_value_desc" rows="4"></textarea></td>'+
					'</tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Create shared attribute value',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
							value_str : $("#shared_attribute_value").val(),
							desc_str : $("#shared_attribute_value_desc").val()
						};
					var success = function(data){
						$("#sharedAttributesEnumTable > tbody").append(
								'<tr>'+
									'<td>'+_data.value_str+'</td>'+
									'<td>'+_data.desc_str+'</td>'+
								'</tr>'
							);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						add_shared_attribute_enum();
					};
					
					doAjaxSync("shared_attribute_enum_add", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});	
}

function delete_shared_attribute_enum(){	
	var $container = $("#sharedAttributesEnumTable");
	var $dialogBox = 
		$('<div class="deleteDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Value</th>'+
						'<td>'+$container.find('.hightlighted td:first').text()+'</td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td>'+$container.find('.hightlighted td:last').text()+'</td>'+
					'</tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Delete value #'+$container.find('.hightlighted td:first').text()+"?",
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							shared_attribute_id : $("#sharedAttributesTable .hightlighted td:first").text(),
							value_str : $container.find('.hightlighted td:first').text()
						};
					
					var success = function(data){
						$container.find(".hightlighted:first").remove();
						$("#delete_shared_attribute_enum").hide();						
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						delete_shared_attribute();
					};
					
					doAjaxSync("shared_attribute_enum_delete", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
	
}
