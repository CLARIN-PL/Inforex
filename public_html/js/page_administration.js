$(function(){
	$(".create").click(function(){
		add($(this));
	});
	
	$(".tableContent tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		containerType = $(this).parents(".tableContainer:first").attr('id');
		if (containerType=="corpusListContainer"){
			get_corpus_elements($(this));
		}
	});
}); 

function get_corpus_elements($element){
	$.ajax({
		async : false,
		url : "index.php",
		dataType : "json",
		type : "post",
		data : {
				ajax : "corpus_get_details",
				corpus_id : $element.children(":first").text()
		},
		success : function(data){
			ajaxErrorHandler(data, 
				function(){
					var tableRows = "";
					$.each(data[0],function(index, value){
						tableRows += 
						'<tr>'+
						'<td>'+index+'</td>'+
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
