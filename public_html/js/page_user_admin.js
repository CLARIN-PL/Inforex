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
		user_edit(id);
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

/**
 * Otwiera okno do edycji danych użytkownika o wskazanym identyfikatorze.
 * @param id
 */
function user_edit(user_id){
	var roles = null;
	doAjaxSync("roles_get", {}, function(data){
		roles = data;
	});
	var success = function(data){
		var user = data;
		user_edit_form(user, roles);
	};	
	var login = function(){
		user_edit(user_id);
	};
	doAjaxSyncWithLogin("user_get", {user_id: user_id}, success, login);	
}

/**
 * Wyświetla formularz do edycji danych użytkownika.
 * @param user
 */
function user_edit_form(user, roles){
	var rolesForm = '';
	for (var i = 0; i < roles.length; i++) {
		var checked = $.inArray(roles[i].role, user.roles) > -1 ? ' checked="checked"' : "";
		rolesForm += '<input type="checkbox" name="roles[]" value="'+roles[i].role+'"'+checked+'/> ' + roles[i].description + "<br/>";
	}
	var form = '<div class="editDialog">'+
	'<form  class="user_edit_form" action="index.php?page=user_admin" method="post">'+
	'<input type="hidden" name="action" value="user_edit"/>'+
	'<input type="hidden" name="user_id" value="'+user.user_id+'"/>'+
		'<table>'+
			'<tr><td class="tekst"> Login: </td>'+
			'<td><input class="add_user" type="text" name="login" size="20" value="'+(user.login?user.login:"")+'"/></td>'+
			'</tr><tr><td class="tekst"> User name: </td>'+
			'<td><input class="add_user" type="text" name="name" size="20" maxlength="20" value="'+(user.screename?user.screename:"")+'"/></td>'+
			'</tr><tr><td class="tekst"> Email: </td>'+
			'<td><input class="add_user" type="text" name="email" size="30" value="'+(user.email?user.email:"")+'"/></td>'+
			'</tr><tr><td class="tekst"> Password: </td>'+
			'<td><input class="add_user" type="password" name="password" size="30" maxlength="20"/></td>'+
			'</tr>'+
			'<tr><td class="tekst" style="vertical-align: top; padding-top: 4px;"> Roles: </td>'+
			'<td>'+rolesForm+'</td>'+
		'</table>'+
	'</form>'+
'</div>'; 
	var $dialogBox = $(form)
		.dialog({
			width : 400,
			modal : true,
			title : 'Edit user '+user.login,
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
