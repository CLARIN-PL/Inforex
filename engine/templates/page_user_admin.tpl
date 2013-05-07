{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_administration_top.tpl"}         

<td class="table_cell_content">
{if "admin"|has_role}
	{include file="inc_system_messages.tpl"}
	
	
	<h1>Users</h1>
	<table id="usersTable" class="tablesorter" cellspacing="1" style="width: 600px">
		<thead>
			<tr>
    	    	<th style="text-align: left">ID</th>
				<th style="text-align: left">Login</th>
				<th style="text-align: left">Name</th>
				<th style="text-align: left">Email</th>
				<th style="text-align: left">Roles</th>
				<th style="text-align: left">Actions</th>
			</tr>
		</thead>
		<tbody>
	    	{foreach from=$all_users item=user}
    		<tr>
        		<td style="color: grey; text-align: right" class="id">{$user.user_id}</td>
				<td class="login">{$user.login}</td>
				<td class="screename">{$user.screename}</td>
				<td class="email">{$user.email}</td>
				<td class="email">{$user.roles}</td>
				<td><a href="#" class="edit_user_button">edit</a></td>
			</tr>
			{/foreach}
		</tbody>		
    </table>
	<button type="button" class="add_user_button" style="margin: 10px; padding: 5px 20px">Add user</button>	
{/if}			 
</td>

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}
