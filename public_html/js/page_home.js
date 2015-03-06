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
		$('<div id="newCorpusDialog" class="addDialog">'+
				'<table>'+
					'<tr><th>Name</th><td><input id="elementName" type="text"/></td></tr>'+
					'<tr class="description"><th>Description</th><td><textarea id="elementDescription" rows="4"></textarea></td></tr>'+
					'<tr><th>Public</th><td><input id="elementPublic" type="checkbox"/> <small>(access for not logged users)</small></td></tr>'+
				'</table>'+
				'<span style="color: red; margin-left: 100px" id="dialog-form-new-corpora-error"></span>'+	
		'</div>')
		.dialog({
			width : 500,
			modal : true,
			title : 'Create new corpora',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					var name = $("#elementName").val();
					var description = $("#elementDescription").val();
					var ispublic = $("#elementPublic").attr("checked");
					
					var missing = [];
					if ( name  == "" ){
						missing.push("name");
					} 
					if ( description == "" ){
						missing.push("description");
					}
					
					if ( missing.length > 0 ){
						var error = "Fill missing fields: " + missing.join(", ");
						$("#dialog-form-new-corpora-error").html(error);
					}
					else{				
						var _data = { 
								name : name, 
								description : description,
								ispublic : ispublic
						};								
						
						var success = function(data){
							window.location.reload();
						};
						
						var complete = function(){
							$dialogBox.dialog("close");
						};
						
						var login = function(){
							add_corpora();
						};
						
						doAjaxSync("corpus_add", _data, success, null, complete, null, login);
					}
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}
