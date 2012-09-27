$(document).ready(function(){
	$('.add_corpora_button').click(function() {
		add_corpora();
	});
});

function add_corpora(){
	var $dialogBox = 
		$('<div class="addDialog">'+
				'<table>'+
					'<tr><th style="text-align:right">Name</th><td><input id="elementName" type="text" /></td></tr>'+
					'<tr><th style="text-align:right">Description</th><td><textarea id="elementDescription" rows="4"></textarea></td></tr>'+
				'</table>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Create new corpora',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var _data = 	{ 
							ajax : "corpus_add", 
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
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
									$("#restricted > tbody").append(
										'<tr>'+
											'<td style="color: grey; text-align: right">'+data.last_id+'</td>'+
											'<td><a href="?corpus='+data.last_id+'&amp;page=browse">'+_data.name_str+'</td>'+
											'<td>'+_data.desc_str+'</td>'+
											'<td style="text-align: right">0</td>'+
										'</tr>'
									);
									$dialogBox.dialog("close");
								},
								function(){
									$dialogBox.dialog("close");
									add_corpora();
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
