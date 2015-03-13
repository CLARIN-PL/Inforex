/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(function(){
	$(".tablesorter").tablesorter();

	$("input[type=checkbox]").click(function(){
		set($(this));
	});

	$("input[type=checkbox]:checked").parent().css('background', '#9DD943');

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

	$(".tablesorter tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$(".tableOptions .edit").show();
		$(".tableOptions .ext_edit").show();
		$(".tableOptions .delete").show();
		$(".tableOptions").show();
	});

	$(".create").click(function(){
		add($(this));
	});

	$(".ext_edit").click(function(){
		console.log($(this).attr("class"));
		ext_edit($(this));
	});

	$(".edit").live("click", function(){
		if ($(this).parent().attr("element") == "corpus_details"){
			var tr = $(this).parents("tr")
			tr.siblings().removeClass("hightlighted");
			tr.addClass("hightlighted");
		}
		edit($(this));
	});

	$(".delete").click(function(){
		remove($(this));
	});

	$(".delete_corpora_button").click(function(){
		delete_corpus();
	});
});


function set($element){
	var attrs = $element[0].attributes;
	var _data = { 
			url: $.url(window.location.href).attr("query"),
			operation_type : ($element.is(':checked') ? "add" : "remove")
	}

	for(var i=0;i<attrs.length;i++) {
		_data[attrs[i].nodeName] = attrs[i].nodeValue;
	}

	var ajax = $element.parents(".tablesorter").attr("id");
	
	var success = function(data){
		$element.parent().css('background',($element.is(':checked') ? '#9DD943' : '#FFFFFF'));
		$(".tablesorter").trigger("update");
	};
	
	var login = function(){
		set($element);	
	};
	
	doAjaxSyncWithLogin(ajax, _data, success, login);
}



function getReportPerspectives(){
	
	var success = function(data){
		var dialogHtml = 
			'<div class="reportPerspectivesDialog">'+
				'<table class="tablesorter" cellspacing="1">'+
					'<thead>'+
						'<tr>'+
							'<th>active</th>'+
							'<th>title</th>'+
							'<th>description</th>'+
							'<th>access</th>'+
						'</tr>'+
					'</thead>'+
					'<tbody>';
		$.each(data,function(index,value){
			dialogHtml += 
				'<tr'+(value.cid ? '' : ' class="inactive"')+'>'+
					'<td>'+'<input class="setReportPerspective" perspectivetitle="'+value.title+'" type="checkbox" perspectiveid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
					'<td>'+value.title+'</td>'+
					'<td>'+value.description+'</td>'+
					'<td>'+
						'<select perspectiveid="'+value.id+'" class="updateReportPerspective">'+
							'<option perspectiveid="'+value.id+'" value="public" '+((value.access && value.access=="public") ? 'selected="selected"' : '' )+'>public</option>'+
							'<option perspectiveid="'+value.id+'" value="loggedin" '+((value.access && value.access=="loggedin") ? 'selected="selected"' : '' )+'>loggedin</option>'+
							'<option perspectiveid="'+value.id+'" value="role" '+((value.access && value.access=="role") ? 'selected="selected"' : '' )+'>role</option>'+
						'</select>'+
					'</td>'+
				'</tr>';
		});
		dialogHtml += '</tbody></table></div>';
		var $dialogBox = $(dialogHtml).dialog({
			modal : true,
			height : 500,
			width : 'auto',
			title : 'Assign report perspectives to corpus',
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
	};
	
	var login = function(data){
		getReportPerspectives();
	};
	
	var url = $.url(window.location.href);
	var corpus_id = url.param("corpus");
	doAjaxSyncWithLogin("corpus_get_report_perspectives", {url: "corpus="+corpus_id}, success, login);
}


function setReportPerspective($element){
	var _data = {
			url: $.url(window.location.href).attr('query'),
			perspective_id : $element.attr('perspectiveid'),
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : ($element.attr('checked') ? "add" : "remove")
		};
	
	var success = function(data){
		$element.parent().parent().toggleClass("inactive");
		updatePerspectiveTable($element,($element.attr('checked') ? "add" : "remove"));
	};
	
	var login = function(data){
		setReportPerspective($element);
	};
	
	doAjaxSyncWithLogin("corpus_set_corpus_and_report_perspectives", _data, success, login);
}

function updateReportPerspective($element){
	if ($element.is("select") && $('input[perspectiveid="'+$element.attr('perspectiveid')+'"]').attr('checked')){
		var params = {
			url: $.url(window.location.href).attr('query'),
			perspective_id : $element.attr('perspectiveid'),
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : "update"
		};
		
		var success = function(data){
			updatePerspectiveTable($element,"update");
		};
		
		var login = function(){
			updateReportPerspective($element);
		};
		
		
		doAjaxSyncWithLogin("corpus_set_corpus_and_report_perspectives", params, success, login);
	}
}

function updatePerspectiveTable($element,operation_type){
	var perspective_id = $element.attr('perspectiveid'); 

	if(operation_type == "remove"){
		$("#corpus_set_corpus_perspective_roles td[perspective_id="+perspective_id+"]").remove();
		$("#corpus_set_corpus_perspective_roles th[perspective_id="+perspective_id+"]").remove();
	}
	else if(operation_type == "add"){
		var access = $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val();
		var title = $element.attr('perspectivetitle');
		$("#corpus_set_corpus_perspective_roles thead tr").append("<th perspective_id='"+perspective_id+"' style='text-align: center'>"+title+"</th>");
		$("#corpus_set_corpus_perspective_roles tbody tr").each(function(){
			var html="";
			if( access == "role"){
				html += "<td perspective_id='"+perspective_id+"' style='text-align: center;'>";
				html += "<input class='userReportPerspective' type='checkbox' userid=";
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
		$("#corpus_set_corpus_perspective_roles tbody").each(function(){
			var html="";
			if( access == "role"){
				var user_id = $(this).attr('id');
				html += "<input class='userReportPerspective' type='checkbox' userid='"+user_id+"' perspective_id='"+perspective_id+"' value='1' />";
			}
			else{
				html += "<i>"+access+"</i>";
			}
			$(this).find("td[perspective_id="+perspective_id+"]").html(html);
			$(this).find("td[perspective_id="+perspective_id+"]").css('background', '#FFFFFF');
		});
	}
}


function add($element){
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr><th style="text-align:right">Name</th><td><input id="elementName" type="text" /></td></tr>'+
					(elementType=='flag' 
					? 
					'<tr><th style="text-align:right">Short</th><td><input id="elementDescription" type="text" /></td></tr>'+
					'<tr><th style="text-align:right">Sort</th><td><input id="elementSort" type="text" /></td></tr>'
					:
					'<tr><th style="text-align:right">Description</th><td><textarea id="elementDescription" rows="4"></textarea></td></tr>'
					)+
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
							url : (elementType=='corpus' ? "" : $.url(window.location.href).attr('query') ), 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='flag'){
						_data.element_sort = $("#elementSort").val();
					}
					
					var success = function(data){
						$("#"+parent+" > tbody").append(
								'<tr>'+
									'<td>'+data.last_id+'</td>'+
									'<td>'+_data.name_str+'</td>'+
									'<td>'+_data.desc_str+'</td>'+
									(elementType=='flag' ? '<td>'+_data.element_sort+'</td>' : '')+
								'</tr>'
							);
					};
					
					var login = function(){
						add($element);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					doAjaxSync($element.attr("action"), _data, success, null, complete, null, login);
					
						
					
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
	var $container = $("#"+parent);
	var editElement = (elementType == 'corpus_details' ? $container.find('.hightlighted th:first').attr("id") : $container.find('.hightlighted td:first').next().text());
	var attrName = $container.find('.hightlighted th:first').text();
	
	var $dialogBox = 
		$('<div class="editDialog">'+
				'<table>'+
					(elementType == 'corpus_details' 
					?
					'<tr><th style="text-align:right">' + attrName + '</th><td>'+ 
						(editElement == "user_id" 
						? get_users($container.find('.hightlighted td:first').text()) 
						: (  editElement == "public" 
							? '<select id="elementDescription"><option value="0">restricted</option><option value="1"'+($container.find('.hightlighted td:first').text() == 'public' ? " selected " : "" )+'>public</option></select>' 
							: '<textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:first').text()+'</textarea>')
						) +'</td></tr>'
					:
					'<tr><th style="text-align:right">Name</th><td><input id="elementName" type="text" value="'+editElement+'"/></td></tr>'+
						(elementType == "flag" 
						? 
						'<tr><th style="text-align:right">Short</th><td><input id="elementDescription" type="text" value="'+$container.find('.hightlighted td:last').prev().text()+'" /></td></tr>'+
						'<tr><th style="text-align:right">Sort</th><td><input id="elementSort" type="text" value="'+$container.find('.hightlighted td:last').text()+'" /></td></tr>'
						: 
						'<tr><th style="text-align:right">Description</th><td><textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:last').text()+'</textarea></td></tr>'
					)) +
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Edit '+elementType.replace(/_/g," ")+ (elementType == 'corpus_details' ? '' : ' #'+$container.find('.hightlighted td:first').text()),
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var edit_id = (elementType == 'corpus_details' ? $container.find('.hightlighted th:first').attr("id") : $container.find('.hightlighted td:first').text());
					var _data = 	{ 
							//ajax : "corpus_update",
							url: $.url(window.location.href).attr('query'),
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType,
							element_id : edit_id
						};
					if (elementType == "flag"){
						_data.sort_str = $("#elementSort").val();
					}
					
					
					var success = function(data){
						var html = (elementType == 'corpus_details' ? '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>' : '<td>'+_data.element_id+'</td><td id="'+_data.element_id+'">'+_data.name_str+'</td>' )+
						'<td>'+(_data.name_str == "user_id" ? $("#elementDescription option:selected").text() : (_data.name_str == "public" ? (_data.desc_str == "1" ? "public" : "restricted" ) : _data.desc_str))+'</td>'+
						(elementType == 'flag' ? '<td>'+_data.sort_str+'</td>' : '');
						if (elementType == 'corpus_details'){
							html += '<td>' +$container.find('.hightlighted td:last').html() + '</td>';
						}
						$container.find(".hightlighted:first").html(html);
					};
					
					var login = function(){
						edit($element);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					doAjaxSync("corpus_update", _data, success, null, complete, null, login);
					
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
	var $container = $("#"+parent);
	var $dialogBox = 
		$('<div class="deleteDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td>'+$container.find('.hightlighted td:first').next().text()+'</td>'+
					'</tr>'+
					(elementType == "subcorpus" ? '<tr><th style="text-align:right">Description</th><td>'+$container.find('.hightlighted td:last').text()+'</td></tr>' : "") +
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
							url: $.url(window.location.href).attr('query'),
							element_type : elementType,
							element_id : $container.find('.hightlighted td:first').text()
					};
					
					var success = function(data){
						$container.find(".hightlighted:first").remove();
						$(".delete").hide();
						$(".edit").hide();	
					};
					
					var login = function(){
						remove($element);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					doAjaxSync("corpus_delete", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}


function delete_corpus(){
	var $dialogBox = 
		$('<div class="deleteDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Name</th>'+
						'<td>'+$('#corpus_name').val()+'</td>'+
					'</tr>'+
					'<tr><th style="text-align:right">Description</th>'+
					'<td>'+$('#corpus_description').val()+'</td></tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Delete corpora #'+ $('#corpus_id').val() + "?",
			buttons : {
				No: function() {
					$dialogBox.dialog("close");
				},
				Yes : function(){
					var _data = 	{ 
							url: $.url(window.location.href).attr("query"),
							element_type : "corpus",
							element_id : $('#corpus_id').val()
						};
					
					var success = function(data){
						var href = document.location.origin + document.location.pathname + '?page=home';
						document.location = href;						
					};
					
					var login = function(){
						remove($element);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					doAjaxSync("corpus_delete", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}


function get_users(userName){
	var select = "<select id=\"elementDescription\">";
	
	var success = function(data){
		$.each(data,function(index, value){
			select += '<option value="'+value.user_id+'" '+(value.screename == userName ? " selected " : "")+'>'+value.screename+'</option>';
		});
	};
	
	var login = function(){
		get_users(userName);
	} ;
	
	doAjaxSyncWithLogin("users_get", {}, success, login);

	return select + "</select>";
}


function ext_edit($element){
	var parent = $element.parent().attr("parent");
	var $container = $("#"+parent);
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr><th style="text-align:right">Field</th><td><input id="elementField" type="text" '+($element.attr("action") == "edit" ? 'value="'+$container.find('.hightlighted td:first').text()+'"' : '')+'/></td></tr>'+
					'<tr><th style="text-align:right">Type</th><td><input id="elementType" type="text" '+($element.attr("action") == "edit" ? 'value="'+$container.find('.hightlighted td:first').next().text()+'"' : '')+'/></td></tr>'+
					'<tr><th style="text-align:right">Null</th><td><input id="elementNull" type="checkbox" '+($element.attr("action") == "edit" ? ($container.find('.hightlighted td:last').text() == "YES" ? 'checked="checked"' : '' ) : '')+'/></td></tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : ($element.attr("action") == "edit" ? 'Edit' : 'Create') + ' metadata element',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							url: $.url(window.location.href).attr('query'), 
							action : $element.attr("action"),
							field : $("#elementField").val(),
							type : $("#elementType").val(),
							is_null : $("#elementNull").is(':checked')
						};
					if ($element.attr("action") == "edit"){
						_data.old_field = $container.find('.hightlighted td:first').text();
					}
					
					var success = function(data){
						get_corpus_ext_elements();
						$(".ext_edit[action=add_table]").hide();
						$(".ext_edit[action=edit]").hide();
						$(".tableOptions").show();
					};
					
					var login = function(){
						ext_edit($element);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					doAjaxSync("corpus_edit_ext", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}


function get_corpus_ext_elements(){
	var params = {
		url: $.url(window.location.href).attr('query'),
		action : "get"
	}
	
	var success = function(data){
		var tableRows = "";
		$.each(data,function(index, value){
			tableRows += 
			'<tr>'+
			'<td>'+value.field+'</td>'+
			'<td>'+value.type+'</td>'+
			'<td>'+value.null+'</td>'+
			'</tr>';
		});
		$("#extListContainer > tbody").html(tableRows);
		$("#extListContainer .create").show();
		$("#extListContainer").show();
		$(".tablesorter").trigger("update");
	};
	
	var login = function(data){
		get_corpus_ext_elements();
	};
	
	var error = function(){
		$("#extListContainer").hide();
	};

	doAjaxSync("corpus_edit_ext", params, success, error, null, null, login);
}
