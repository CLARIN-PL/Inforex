{include file="inc_header.tpl"}

<td class="table_cell_content">
{if "admin"|has_role}
	{include file="inc_system_messages.tpl"}
	
	
	<h1>Users</h1>
	<table id="usersTable" class="tablesorter" cellspacing="1">
		<thead>
			<tr>
    	    	<th style="text-align: left">ID</th>
				<th style="text-align: left">Login</th>
				<th style="text-align: left">Name</th>
				<th style="text-align: left">Email</th>
			</tr>
		</thead>
		<tbody>
	    	{foreach from=$all_users item=user}
    		<tr>
        		<td style="color: grey; text-align: right" class="id">{$user.user_id}</td>
				<td class="login">{$user.login}</td>
				<td class="screename">{$user.screename}</td>
				<td class="email">{$user.email}</td>
			</tr>
			{/foreach}
		</tbody>		
    </table>
	<button type="button" class="add_user_button" style="margin: 10px; padding: 5px 20px">Add user</button>
	<button type="button" class="edit_user_button" style="margin: 10px; padding: 5px 20px" disabled="disabled">Edit user</button>
{/if}			 
</td>

{include file="inc_footer.tpl"}
