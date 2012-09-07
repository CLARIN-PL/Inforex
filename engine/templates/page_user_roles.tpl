{include file="inc_header.tpl"}

<td class="table_cell_content">

	{include file="inc_system_messages.tpl"}

	<h1>Przypisane role:</h1>
	<ul>
	{foreach from=$user.role item=description key=role}
		<li><b>{$role}</b> &mdash; {$description}</li>
	{foreachelse}
		<li><i>brak</i></li>
	{/foreach}
	</ul>
	
	{if $user.role.loggedin}
		<h1>Opcje konta:</h1>
		<ul>
			<li><a class="option" id="password_change_form" href="#">Zmiana hasła</a></li>
		</ul>
		<form  class="password_change_form" style="padding: 5px; background: #eee;  margin: 10px; width: 400px; display: none" action="index.php?page=user_roles" method="post">
			<input type="hidden" name="action" value="user_password_change"/>
			<table border="0" width="450"> 
				<tr> 
					<td class="tekst"> Poprzednie hasło: </td> 
					<td><input class="password_change" type="text" name="old_pass" size="20" /></td> 
				</tr> 
				<tr> 
					<td class="tekst"> Nowe hasło: </td> 
					<td><input class="password_change" type="password" name="new_pass1" size="20" maxlength="20"/></td> 
				</tr> 
				<tr> 
					<td class="tekst"> Powtórzenie nowego hasła: </td> 
					<td><input class="password_change" type="password" name="new_pass2" size="20" maxlength="20"/></td> 
				</tr> 
				<tr> 
					<td>&nbsp;</td> 
				</tr> 
				<tr> 
					<td align="center"><input type="submit" class="password_change" value="Zmiana hasła" disabled="disabled" /></td> 
				</tr> 
			</table> 
		</form> 
	{/if}
	{if $user.role.admin}
		<h1>Opcje administracyjne:</h1>
		<ul>
			<li><a class="option" id="user_add_form" href="#">Dodanie użytkownika</a></li>
		</ul>
		<form  class="user_add_form" style="padding: 5px; background: #eee;  margin: 10px; width: 400px; display: none" action="index.php?page=user_roles" method="post">
			<input type="hidden" name="action" value="user_add"/>
			<table border="0" width="450"> 
				<tr> 
					<td class="tekst"> Login: </td> 
					<td><input class="add_user" type="text" name="login" size="20" /></td> 
				</tr> 
				<tr> 
					<td class="tekst"> Nazwa użytkownika: </td> 
					<td><input class="add_user" type="text" name="name" size="20" maxlength="20"/></td> 
				</tr> 
				<tr> 
					<td class="tekst"> Hasło: </td> 
					<td><input class="add_user" type="password" name="password" size="20" maxlength="20"/></td> 
				</tr> 
				<tr> 
					<td>&nbsp;</td> 
				</tr> 
				<tr> 
					<td align="center"><input type="submit" class="add_user" value="Dodanie użytkownika" disabled="disabled" /></td> 
				</tr> 
			</table> 
		</form>
		<ul>
			<li><a class="option" id="user_edit_form" href="#">Edycja użytkownika</a></li>
		</ul>
		
		<form  class="user_edit_form" style="padding: 5px; background: #eee;  margin: 10px; width: 400px; display: none" action="index.php?page=user_roles" method="post">
			<input type="hidden" name="action" value="user_edit"/>
			<table border="0" width="450"> 
				<tr> 
					<td class="tekst"> Użytkownik: </td> 
					<td><select class="edit_user" name="user_id">
					<option value="" style="display:none;"></option>
					{foreach from=$all_users item=set}
						<option value="{$set.user_id}" login="{$set.login}">{$set.screename}</option>
					{/foreach}
					</select></td> 
				</tr>
				<tr> 
					<td class="tekst"> Login: </td> 
					<td><input class="edit_user" type="text" name="login" size="20" maxlength="20"/></td> 
				</tr> 
				<tr> 
					<td class="tekst"> Nazwa użytkownika: </td> 
					<td><input class="edit_user" type="text" name="name" size="20" maxlength="20"/></td> 
				</tr> 
				<tr> 
					<td class="tekst"> Hasło: </td> 
					<td><input class="edit_user" type="password" name="password" size="20" maxlength="20"/></td> 
				</tr> 
				<tr> 
					<td>&nbsp;</td> 
				</tr> 
				<tr> 
					<td align="center"><input type="submit" class="edit_user" value="Edycja użytkownika" disabled="disabled" /></td> 
				</tr> 
			</table> 
		</form>
		 
	{/if}
</td>

{include file="inc_footer.tpl"}
