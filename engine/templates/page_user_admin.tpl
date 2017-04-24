{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="panel panel-primary" style="margin: 5px; visibility: visible;">
	<div class="panel-heading">Users</div>
	<div class="panel-body scrollingWrapper">

	<div class="navbar navbar-default">
		<div class="container">
			<div class="navbar-collapse collapse" id="searchbar">
				<form class="navbar-form search-form">
					<div class="form-group" style="display:inline;">
						<div class="input-group" style="display:table;">
							<span class="input-group-addon" style="width:1%;"><span class="glyphicon glyphicon-search"></span></span>
							<input class="form-control" name="search" placeholder="Search Here" autocomplete="off" autofocus="autofocus" type="text">
						</div>
					</div>
				</form>
			</div><!--/.nav-collapse -->
		</div>
	</div>

{if "admin"|has_role}
	{include file="inc_system_messages.tpl"}
	<div id="toolbar"></div>
	<div class="scrolling">
		<table id="usersTable" class="table table-stripped" cellspacing="1"
			   data-toggle="table"
			   data-search="true"
			   data-filter-control="false"
			   data-toolbar="#toolbar">
			<thead>
				<tr>
					<th style="text-align: left; width: 20px;">ID</th>
					<th style="text-align: left; width: 200px;" data-field="login" data-sortable="true">Login</th>
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
	</div>
{/if}
	</div>
	<div class="panel-footer">
		<button type="button" class="btn btn-primary add_user_button">Add user</button>
	</div>
</div>

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}
