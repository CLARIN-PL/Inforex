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
		if (containerType=="corpusListContainer"){
			$("#corpusListContainer .delete").show();
			$("#corpusElementsContainer .edit").hide();
			get_corpus_elements($(this));
			get_subcorpus_list($(this));
		}
		if (containerType=="corpusElementsContainer"){
			$("#corpusElementsContainer .edit").show();
		}
		if (containerType=="subcorpusListContainer"){			
			$("#subcorpusListContainer .edit").show();
			$("#subcorpusListContainer .delete").show();
		}
	});
}); 

function get_corpus_elements($element){
	var corpus_id = $element.children(":first").text();
	$.ajax({
		async : false,
		url : "index.php&amp;corpus=".corpus_id,
		dataType : "json",
		type : "post",
		data : {
				ajax : "corpus_get_details",
				corpus_id : corpus_id
		},
		success : function(data){
			ajaxErrorHandler(data, 
				function(){
					var tableRows = "";
					$.each(data[0],function(index, value){
						tableRows += 
						'<tr>'+
						'<td id="'+corpus_id+'">'+index+'</td>'+
						'<td>'+value+'</td>'+
						'</tr>';
					});
					$("#corpusElementsContainer table > tbody").html(tableRows);
				},
				function(){
					get_corpus_elements($element);
				}
			);
		}
	});
}


function get_subcorpus_list($element){
	var corpus_id = $element.children(":first").text();
	$.ajax({
		async : false,
		url : "index.php&amp;corpus=".corpus_id,
		dataType : "json",
		type : "post",
		data : {
				ajax : "corpus_get_subcorpus",
				corpus_id : corpus_id
		},
		success : function(data){
			ajaxErrorHandler(data, 
				function(){
					var tableRows = "";
					$.each(data,function(index, value){
						tableRows += 
						'<tr>'+
						'<td>'+value.id+'</td>'+
						'<td>'+value.name+'</td>'+
						'<td>'+value.description+'</td>'+
						'</tr>';
					});
					$("#subcorpusListContainer table > tbody").html(tableRows);
					$("#subcorpusListContainer .create").attr("id", corpus_id);
					$("#subcorpusListContainer .create").show();
				},
				function(){
					get_subcorpus_list($element);
				}
			);
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
							ajax : "corpus_add", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};
					if (elementType=='subcorpus'){
						var corpus_id = $element.attr("id");
						_data.corpus_id = corpus_id;
						_data.ajax = "subcorpus_add";
					}
					$.ajax({
						async : false,
						url : (elementType=='subcorpus' ? "index.php&amp;corpus=".corpus_id : "index.php"),
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
											(elementType=='subcorpus' ? '<td>'+_data.desc_str+'</td>' : '') +
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
	var editElement = (elementType == 'subcorpus' ? $container.find('.hightlighted td:first').next().text() : $container.find('.hightlighted td:first').text());
	var $dialogBox = 
		$('<div class="editDialog">'+
				'<table>'+
					'<tr>'+
						'<th style="text-align:right">Element</th>'+
						'<td><input id="elementName" type="text" ' + (elementType == 'corpus' ? 'disabled="disabled"' : '') + 'value="'+editElement+'"/></td>'+
					'</tr>'+
					'<tr>'+
						'<th style="text-align:right">Value</th>'+
						'<td>'+ (editElement == "screename" ? get_users($container.find('.hightlighted td:last').text()) : '<textarea id="elementDescription" rows="4">'+$container.find('.hightlighted td:last').text()+'</textarea>') +'</td>'+
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
					var edit_id = (elementType == 'subcorpus' ? $container.find('.hightlighted td:first').text() : $container.find('.hightlighted td:first').attr("id"));
					var _data = 	{ 
							ajax : "corpus_update", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType,
							element_id : edit_id
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
									$container.find(".hightlighted:first").html(
										(elementType == 'subcorpus' ? '<td>'+_data.element_id+'</td>' : '' )+
										'<td id="'+_data.element_id+'">'+_data.name_str+'</td>'+
										'<td>'+(_data.name_str == "screename" ? $("#elementDescription option:selected").text() : _data.desc_str)+'</td>'
									);
									if(_data.name_str == 'name' && elementType == "corpus_details"){
										$("#corpusListTable").find('.hightlighted:first').html(
											'<td>'+_data.element_id+'</td>'+
											'<td>'+_data.desc_str+'</td>'
										);
									}
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
						'<th style="text-align:right">Name</th>'+
						'<td>'+$container.find('.hightlighted td:first').next().text()+'</td>'+
					'</tr>'+
					(elementType != "corpus" ? '<tr><th style="text-align:right">Description</th><td>'+$container.find('.hightlighted td:last').text()+'</td></tr>' : "") +
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
					var corpus_id = $container.find('.hightlighted td:first').text();
					var _data = 	{ 
							ajax : "corpus_delete", 
							element_type : elementType,
							element_id : corpus_id
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
									if (elementType=="corpus"){
										$("#corpusListContainer .delete").hide();
										$("#corpusElementsTable > tbody").html("");
									}
									if (elementType=="subcorpus"){
										$("#subcorpusListContainer .delete").hide();
										$("#subcorpusListContainer .edit").hide();
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
