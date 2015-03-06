{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_administration_top.tpl"}         

{if "admin"|has_role}
    <div class="buttons_box" style="margin-bottom: 4px">
        <button type="button" class="add_user_button">Add user</button>
    </div>
	{include file="inc_system_messages.tpl"}
		
	<table id="usersTable" class="tablesorter" cellspacing="1">
		<thead>
			<tr>
    	    	<th style="text-align: left; width: 20px;">ID</th>
				<th style="text-align: left; width: 200px;">Login</th>
				<th style="text-align: left; width: 200px;">Name</th>
				<th style="text-align: left; width: 200px;">Email</th>
				<th style="text-align: left;">Roles</th>
				<th style="text-align: left; width: 50px;">Actions</th>
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
				<td style="text-align: center"><a href="#" class="edit_user_button">edit</a></td>
			</tr>
			{/foreach}
		</tbody>		
    </table>		
{/if}			 

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}
