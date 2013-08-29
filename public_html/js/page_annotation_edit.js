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
	
	$(".move").click(function(e){
		e.preventDefault();		
		move($(this));
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
		else if (containerType=="annotationTypesContainer"){
			$("#annotationTypesContainer .edit,#annotationTypesContainer .delete").show();
		}
		get($(this));
	});
	
	//$("")
}); 


function get($element){
	var $container = $element.parents(".tableContainer:first");
	var containerName = $container.attr("id");
	var childId = "";
	if (containerName=="annotationSetsContainer" || containerName=="annotationSubsetsContainer"){
		var _data = 	{ 
				//ajax : "annotation_edit_get",
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
		
		var success = function(data){
			var tableRows = "";
			$.each(data,function(index, value){
				//for annotation_set the last two objects contains data from annotation_sets_corpora and corpora 
				if (_data.parent_type=="annotation_set" && index<data.length-2){
					tableRows+=
					'<tr>'+
						'<td>'+value.id+'</td>'+
						'<td>'+value.description+'</td>'+
					'</tr>';
				}
				else if (_data.parent_type=="annotation_subset") 
					tableRows+=
						'<tr>'+
							'<td><span style="'+(value.css==null ? "" : value.css)+'">'+value.name+'</span></td>'+
							'<td>'+(value.short==null ? "" : value.short)+'</td>'+
							'<td>'+(value.description==null ? "" : value.description)+'</td>'+
							'<td style="display:none">'+(value.css==null ? "" : value.css)+'</td>'+
						'</tr>';
			});
			$("#"+childId+" table > tbody").html(tableRows);
			
			if (_data.parent_type=="annotation_set"){
				//annotation_sets_corpora:
				tableRows = "";
				$.each(data[data.length-2],function(index, value){
						tableRows+=
						'<tr>'+
							'<td>'+value.id+'</td>'+
							'<td>'+value.name+'</td>'+
							'<td>'+value.description+'</td>'+
						'</tr>';
				});
				$("#annotationSetsCorporaContainer table > tbody").html(tableRows);
				//corpora:
				tableRows = "";
				$.each(data[data.length-1],function(index, value){
						tableRows+=
						'<tr>'+
							'<td>'+value.id+'</td>'+
							'<td>'+value.name+'</td>'+
							'<td>'+value.description+'</td>'+
						'</tr>';
				});
				$("#corpusContainer table > tbody").html(tableRows);							
			}
		};
		var login = function(data){
			get($element);
		};
		doAjaxSyncWithLogin("annotation_edit_get", _data, success, login);
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
						'<td><textarea id="elementDescription" rows="4"></textarea></td>'+
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
						'<th style="text-align:right">Short</th>'+
						'<td><input id="elementShort" type="text" /></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><textarea id="elementDescription" rows="4"></textarea></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Css</th>'+
						'<td><textarea id="elementCss" rows="4"></textarea><br/>(<a href="#" id="previewCssButton">refresh preview</a>)</td>'+
					'</tr>'+					
					'<tr>'+
						'<th style="text-align:right">Preview</th>'+
						'<td><span id="previewCssSpan">sample</span></td>'+
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
							//ajax : "annotation_edit_add", 
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
						_data.description = $("#elementDescription").val();
						_data.css = $("#elementCss").val();
						_data.set_id = $("#annotationSetsTable .hightlighted > td:first").text();
					}
					
					var success = function(data){
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
										'<td><span style="'+_data.css+'">'+_data.name_str+'</span></td>'+
										'<td>'+_data.short+'</td>'+
										'<td>'+_data.desc_str+'</td>'+
										'<td style="display:none">'+_data.css+'</td>'+
									'</tr>'
								);
						$dialogBox.dialog("close");
					};
					var login = function(){
						$dialogBox.dialog("close");
						add($element);
					};
					
					doAjaxSyncWithLogin("annotation_edit_add", _data, success, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
	if (elementType=="annotation_type"){
		$("#previewCssButton").click(function(){
			$("#previewCssSpan").attr('style',$("#elementCss").val());
		});
	}
	
	
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
						'<td><textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:first').next().text()+'</textarea></td>'+
					'</tr>'+
				'</table>'+
		'</div>');
	else if (elementType=="annotation_type"){
		$vals = $container.find('.hightlighted td');
		$dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td style="padding-top: 4px"><span id="previewCssSpan" style="'+$($vals[3]).text()+'">'+ $($vals[0]).text()+'</span></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Short</th>'+
						'<td><input id="elementShort" type="text" value="'+$($vals[1]).text()+'"/></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Description</th>'+
						'<td><textarea id="elementDescription" rows="4">'+$($vals[2]).text()+'</textarea></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Css</th>'+
						'<td><textarea id="elementCss">'+$($vals[3]).text()+'</textarea><br/>(<a href="#" id="previewCssButton">refresh preview</a>)</td>'+
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
							//ajax : "annotation_edit_update", 
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
					
					var success = function(data){
						if (elementType=="annotation_set" || elementType=="annotation_subset")
							$container.find(".hightlighted:first").html(
								'<td>'+$container.find(".hightlighted td:first").text()+'</td>'+
								'<td>'+_data.desc_str+'</td>'
							);
						else if (elementType=="annotation_type")
							$container.find(".hightlighted:first").html(
									'<td><span style="'+_data.css+'">'+_data.element_id+'</span></td>'+
									'<td>'+_data.short+'</td>'+
									'<td>'+_data.desc_str+'</td>'+
									'<td style="display:none">'+_data.css+'</td>'
								);
						$dialogBox.dialog("close");
					};
					var login = function(){
						$dialogBox.dialog("close");
						edit($element);
					};
					
					doAjaxSyncWithLogin("annotation_edit_update", _data, success, login);	
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
	if (elementType=="annotation_type"){
		$("#previewCssButton").click(function(){
			$("#previewCssSpan").attr('style',$("#elementCss").val());
		});
	}	
	
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
							//ajax : "annotation_edit_delete", 
							element_type : elementType,
							element_id : $container.find('.hightlighted td:first').text()
						};
					
					var success = function(data){
						$container.find(".hightlighted:first").remove();
						if (elementType=="annotation_set"){
							$("#annotationSetsContainer .edit,#annotationSetsContainer .delete").hide();
							$("#annotationSubsetsContainer span").hide();
							$("#annotationTypesContainer span").hide();
							$("#annotationSubsetsContainer table > tbody").empty();
							$("#annotationTypesContainer table > tbody").empty();
							$("#annotationSetsCorporaTable > tbody").empty();
							$("#corpusTable > tbody").empty();
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
					};
					var login = function(){
						$dialogBox.dialog("close");
						remove($element);	
					};
					
					doAjaxSyncWithLogin("annotation_edit_delete", _data, success, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
	
}

function move($element){
	var $moveElement = null;
	var $targetElement = null;
	var _data = {
		//ajax : "annotation_edit_move" 
	};
	var $setElement = $("#annotationSetsTable tr.hightlighted:first");
	if ($element.hasClass("assign")){
		$moveElement =  $("#corpusTable tr.hightlighted:first").removeClass("hightlighted");
		$targetTable = $("#annotationSetsCorporaTable > tbody");
		_data.move_type = 'assign';
	}
	else if ($element.hasClass("unassign")){
		$moveElement =  $("#annotationSetsCorporaTable tr.hightlighted:first").removeClass("hightlighted");
		$targetTable = $("#corpusTable > tbody");
		_data.move_type = 'unassign';
	}
	if ($moveElement.length>0){
		_data.set_id = $setElement.children("td:first").text();
		_data.corpora_id = $moveElement.children("td:first").text();
		
		var success = function(data){
			$targetTable.append($moveElement);
		};
		var login = function(data){
			move($element);
		};
		
		doAjaxSyncWithLogin("annotation_edit_move", _data, success, login);
	}

}