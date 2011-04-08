$(function(){
	$(".create").click(function(){
		add($(this));
			
	});
	
	$(".tableContent tr").click(function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
	});
});

function ajaxErrorHandler(data, successHandler, errorHandler){
	if (data['error']){
		if (data['error_code']=="ERROR_AUTHORIZATION"){
				loginForm(false, function(success){ 
					if (success){						
						if (errorHandler && $.isFunction(errorHandler)){
							errorHandler();
						}
					}else{
						//alert('Wystąpił problem z autoryzacją. Zmiany nie zostały zapisane.');
						cancel_relation(); 
					}
				});				
		}
		else {
			alert('nieznany blad!');
		}
	} 
	else {
		if (successHandler && $.isFunction(successHandler)){
			successHandler();
		}		
	}
} 


function add($element){	//can make it as edit/delete also
	var elementType = $element.parent().attr("element");
	var parent = $element.parent().attr("parent");
	var $container = $element.parents(".tableContainer");
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr>'+
						'<th>Name</th>'+
						'<input id="elementName" type="text" />'+
					'</tr>'+
					'<tr>'+
						'<th>Type</th>'+
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
							ajax : "event_edit_add", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
							element_type : elementType
						};	
					//console.log(_data);
					jQuery.ajax({
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