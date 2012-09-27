$(document).ready(function(){
	$("#usersTable").tablesorter();

	$("#usersTable tr").live("click",function(){
		$(this).siblings().removeClass("hightlighted");
		$(this).addClass("hightlighted");
		$('.edit_user_button').attr("disabled","disabled");
		$(this).find('.edit_user_button').attr("disabled","");
	});

	$('.add_user_button').click(function() {
		user_add();
	});

	$('.edit_user_button').click(function() {
		user_edit();
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

function user_edit(){	
	var selectedRow = $("#usersTable tr.hightlighted");
	var id = selectedRow.find("td.id").text();
	var login = selectedRow.find("td.login").text();
	var screename = selectedRow.find("td.screename").text();
	var email = selectedRow.find("td.email").text();
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
