/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

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
							name_str : $("#elementName").val(),
							desc_str : $("#elementDescription").val(),
					};
					
					var success = function(data){
						$("#restricted > tbody").append(
								'<tr>'+
									'<td style="color: grey; text-align: right">'+data.last_id+'</td>'+
									'<td><a href="?corpus='+data.last_id+'&amp;page=browse">'+_data.name_str+'</td>'+
									'<td>'+_data.desc_str+'</td>'+
									'<td style="text-align: right">0</td>'+
								'</tr>'
							);
					};
					
					var complete = function(){
						$dialogBox.dialog("close");
					};
					
					var login = function(){
						add_corpora();
					};
					
					doAjaxSync("corpus_add", _data, success, null, complete, null, login);
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}
