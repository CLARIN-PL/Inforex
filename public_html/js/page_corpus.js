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
		$(".tableOptions .delete").show();		
		$(".tableOptions").show();
	});

	$(".create").click(function(){
		add($(this));
	});

	$(".create_ext").click(function(){
		add_ext($(this));
	});

	$(".edit").click(function(){
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
			ajax : $element.parents(".tablesorter").attr("id"),
			operation_type : ($element.is(':checked') ? "add" : "remove")
	}

	for(var i=0;i<attrs.length;i++) {
		_data[attrs[i].nodeName] = attrs[i].nodeValue;
	}

	$.ajax({
		async : false,
		url : "index.php&amp;corpus=".corpus_id,
		dataType : "json",
		type : "post",
		data : _data,				
		success : function(data){
			ajaxErrorHandler(data,
				function(){
					$element.parent().css('background',($element.is(':checked') ? '#9DD943' : '#FFFFFF'));
					$(".tablesorter").trigger("update");
				},
				function(){
					set($element);
				}
			);								
		}
	});		
}



function getReportPerspectives(){
	$.ajax({
		async : false,
		url : "index.php&amp;corpus=".corpus_id,
		dataType : "json",
		type : "post",
		data : {
			ajax : "corpus_get_report_perspectives"
		},				
		success : function(data){
			ajaxErrorHandler(data,
				function(){	
					var dialogHtml = 
						'<div class="reportPerspectivesDialog">'+
							'<table class="tablesorter">'+
								'<thead>'+
									'<tr>'+
										'<th>assign</th>'+
										'<th>title</th>'+
										'<th>description</th>'+
										'<th>access</th>'+
									'</tr>'+
								'</thead>'+
								'<tbody>';
					$.each(data,function(index,value){
						var td_start = '<td'+(value.cid ? '' : ' style="background-color: #DDD"')+'>';
						dialogHtml += 
							'<tr>'+
								td_start+'<input class="setReportPerspective" perspectivetitle="'+value.title+'" type="checkbox" perspectiveid="'+value.id+'" '+(value.cid ? 'checked="checked"' : '')+'/></td>'+
								td_start+value.title+'</td>'+
								td_start+value.description+'</td>'+
								td_start+
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
			perspective_id : $element.attr('perspectiveid'),
			access : $('option[perspectiveid="'+$element.attr('perspectiveid')+'"]:selected').val(),
			operation_type : ($element.attr('checked') ? "add" : "remove")
		};
	$.ajax({
		async : false,
		url : "index.php&amp;corpus=".corpus_id,
		dataType : "json",
		type : "post",
		data : _data,				
		success : function(data){
			ajaxErrorHandler(data,
				function(){
					$element.parent().siblings().andSelf().css("background-color", ($element.attr('checked') ? "#FFF" : "#DDD"));
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
			url : "index.php&amp;corpus=".corpus_id,
			dataType : "json",
			type : "post",
			data : {
				ajax : "corpus_set_corpus_and_report_perspectives",
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
							ajax : $element.attr("action"), 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='flag'){
						_data.element_sort = $("#elementSort").val();
					}
					$.ajax({
						async : false,
						url : (elementType=='corpus' ? "index.php" : "index.php&amp;corpus=".corpus_id ),
						dataType : "json",
						type : "post",
						data : _data,				
						success : function(data){
							ajaxErrorHandler(data,
								function(){		
									//update lastrowid in data
									$("#"+parent+" > tbody").append(
										'<tr>'+
											'<td>'+data.last_id+'</td>'+
											'<td>'+_data.name_str+'</td>'+
											'<td>'+_data.desc_str+'</td>'+
											(elementType=='flag' ? '<td>'+_data.element_sort+'</td>' : '')+
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
	var $container = $("#"+parent);
	var editElement = (elementType == 'corpus_details' ? $container.find('.hightlighted th:first').attr("id") : $container.find('.hightlighted td:first').next().text());
	var $dialogBox = 
		$('<div class="editDialog">'+
				'<table>'+
					(elementType == 'corpus_details' 
					?
					'<tr><th style="text-align:right">Element</th><td><input id="name" type="text" disabled="disabled" value="'+$container.find('.hightlighted th:first').text()+'"/>'+
					'<input id="elementName" type="hidden" value="'+editElement+'"/></td></tr>'+
					'<tr><th style="text-align:right">Value</th><td>'+ 
						(editElement == "user_id" 
						? get_users($container.find('.hightlighted td:last').text()) 
						: (  editElement == "public" 
							? '<select id="elementDescription"><option value="0">restricted</option><option value="1"'+($container.find('.hightlighted td:last').text() == 'public' ? " selected " : "" )+'>public</option></select>' 
							: '<textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:last').text()+'</textarea>')
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
							ajax : "corpus_update", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType,
							element_id : edit_id
						};			
					if (elementType == "flag"){
						_data.sort_str = $("#elementSort").val();
					}		
					$.ajax({
						async : false,
						url : "index.php&amp;corpus=".corpus_id,
						dataType : "json",
						type : "post",
						data : _data,				
						success : function(data){
							ajaxErrorHandler(data,
								function(){	
									var html = (elementType == 'corpus_details' ? '<th id="'+_data.element_id+'">'+$container.find('.hightlighted th:first').text()+'</th>' : '<td>'+_data.element_id+'</td><td id="'+_data.element_id+'">'+_data.name_str+'</td>' )+
										'<td>'+(_data.name_str == "user_id" ? $("#elementDescription option:selected").text() : (_data.name_str == "public" ? (_data.desc_str == "1" ? "public" : "restricted" ) : _data.desc_str))+'</td>'+
										(elementType == 'flag' ? '<td>'+_data.sort_str+'</td>' : '');	
									$container.find(".hightlighted:first").html(html);
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
							ajax : "corpus_delete", 
							element_type : elementType,
							element_id : $container.find('.hightlighted td:first').text()
						};
					$.ajax({
						async : false,
						url : "index.php&amp;corpus=".corpus_id,
						dataType : "json",
						type : "post",
						data : _data,				
						success : function(data){
							ajaxErrorHandler(data,
								function(){											
									$container.find(".hightlighted:first").remove();
									$(".delete").hide();
									$(".edit").hide();
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
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "corpus_delete",
							element_type : "corpus",
							element_id : $('#corpus_id').val()
						};
					$.ajax({
						async : false,
						url : "index.php&amp;corpus=".corpus_id,
						dataType : "json",
						type : "post",
						data : _data,				
						success : function(data){
							ajaxErrorHandler(data,
								function(){											
									$dialogBox.dialog("close");
									var href = document.location.origin + document.location.pathname + '?page=home';
									document.location = href;
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


function get_users(userName){
	var select = "<select id=\"elementDescription\">";
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
				ajax : "users_get"
		},
		success : function(data){
			ajaxErrorHandler(data,
				function(){					
					$.each(data,function(index, value){
						select += '<option value="'+value.user_id+'" '+(value.screename == userName ? " selected " : "")+'>'+value.screename+'</option>';						
					});					
				},
				function(){
					get_users(userName);
				}
			);
		}
	});
	return select + "</select>";
}


function add_ext($element){	
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr><th style="text-align:right">Field</th><td><input id="elementField" type="text" /></td></tr>'+
					'<tr><th style="text-align:right">Type</th><td><input id="elementType" type="text" /></td></tr>'+
					'<tr><th style="text-align:right">Null</th><td><input id="elementNull" type="checkbox" /></td></tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Create metadata element',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : $element.attr("action"), 
							action : "add",
							field : $("#elementField").val(),
							type : $("#elementType").val(),
							is_null : $("#elementNull").is(':checked')
						};
					$.ajax({
						async : false,
						url : "index.php&amp;corpus=".corpus_id,
						dataType : "json",
						type : "post",
						data : _data,				
						success : function(data){
							ajaxErrorHandler(data,
								function(){
									get_corpus_ext_elements();
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									add_ext($element);
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


function get_corpus_ext_elements(){
	$.ajax({
		async : false,
		url : "index.php&amp;corpus=".corpus_id,
		dataType : "json",
		type : "post",
		data : {
				ajax : "corpus_edit_ext",
				action : "get"
		},
		success : function(data){
			ajaxErrorHandler(data, 
				function(){
					if(data.empty){
						$("#extListContainer").hide();
					}
					else{
						var tableRows = "";
						$.each(data,function(index, value){
							if (value.field != "id"){
								tableRows += 
								'<tr>'+
								'<td>'+value.field+'</td>'+
								'<td>'+value.type+'</td>'+
								'<td>'+value.null+'</td>'+
								'</tr>';
							}
						});
						$("#extListContainer > tbody").html(tableRows);
						$("#extListContainer .create").show();
						$("#extListContainer").show();
					}					
				},
				function(){
					get_corpus_ext_elements();
				}
			);
		}
	});
}
