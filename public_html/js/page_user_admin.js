/**
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 */

$(document).ready(function(){
	$("#usersTable").tablesorter();

	$("#usersTable tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
	});

	$('.add_user_button').click(function() {
		user_add("", "", "", "");
	});

	$('.edit_user_button').click(function() {
		var tr = $(this).closest("tr");
		var id = tr.find("td.id").text();
		var login = tr.find("td.login").text();
		var screename = tr.find("td.screename").text();
		var email = tr.find("td.email").text();
		
		user_edit(id, login, screename, email);
	});
});

function user_add(){	
	var $dialogBox = 
		$('<div class="addDialog">'+
			'<form  class="user_add_form" action="index.php?page=user_admin" method="post">'+
			'<input type="hidden" name="action" value="user_add"/>'+
				'<table>'+
					'<tr><td class="tekst"> Login: </td>'+
					'<td><input class="add_user" type="text" name="login" size="20" /></td>'+
					'</tr><tr><td class="tekst"> User name: </td>'+
					'<td><input class="add_user" type="text" name="name" size="20" maxlength="20"/></td>'+
					'</tr><tr><td class="tekst"> Email: </td>'+
					'<td><input class="add_user" type="text" name="email" size="20" maxlength="20"/></td>'+
					'</tr><tr><td class="tekst"> Password: </td>'+
					'<td><input class="add_user" type="password" name="password" size="20" maxlength="20"/></td>'+
					'</tr>'+
				'</table>'+
			'</form>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Create user',
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					$(".user_add_form").submit();
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}

function user_edit(id, login, screename, email){	
	var selectedRow = $("#usersTable tr.hightlighted");
	var $dialogBox = 
		$('<div class="editDialog">'+
			'<form  class="user_edit_form" action="index.php?page=user_admin" method="post">'+
			'<input type="hidden" name="action" value="user_edit"/>'+
			'<input type="hidden" name="user_id" value="'+id+'"/>'+
				'<table>'+
					'<tr><td class="tekst"> Login: </td>'+
					'<td><input class="add_user" type="text" name="login" size="20" value="'+login+'"/></td>'+
					'</tr><tr><td class="tekst"> User name: </td>'+
					'<td><input class="add_user" type="text" name="name" size="20" maxlength="20" value="'+screename+'"/></td>'+
					'</tr><tr><td class="tekst"> Email: </td>'+
					'<td><input class="add_user" type="text" name="email" size="20" maxlength="20" value="'+email+'"/></td>'+
					'</tr><tr><td class="tekst"> Password: </td>'+
					'<td><input class="add_user" type="password" name="password" size="20" maxlength="20"/></td>'+
					'</tr>'+
				'</table>'+
			'</form>'+
		'</div>')
		.dialog({
			modal : true,
			title : 'Edit user #'+id,
			buttons : {
				Cancel: function() {
					$dialogBox.dialog("close");
				},
				Ok : function(){
					$(".user_edit_form").submit();
				}
			},
			close: function(event, ui) {
				$dialogBox.dialog("destroy").remove();
				$dialogBox = null;
			}
		});
}
