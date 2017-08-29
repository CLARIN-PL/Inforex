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
		<table id="usersTable" class="table table-striped" cellspacing="1"
			   data-toggle="table"
			   data-search="true"
			   data-filter-control="false"
			   data-toolbar="#toolbar">
			<thead>
				<tr>
					<th style="text-align: left; width: 20px;">ID</th>
					<th style="text-align: left;" data-field="login" data-sortable="true">Login</th>
					<th style="text-align: left;">Name</th>
					<th style="text-align: left;">Email</th>
					<th style="text-align: left;">Roles</th>
                    <th style="text-align: left;">Last activity</th>
					<th style="text-align: left;">Actions</th>
				</tr>
			</thead>
			<tbody id = "usersTableBody">
				{foreach from=$all_users item=user}
				<tr>
					<td style="color: grey; text-align: right" class="id">{$user.user_id}</td>
					<td class="login">{$user.login}</td>
					<td class="screename">{$user.screename}</td>
					<td class="email">{$user.email}</td>
					<td class="user_roles">{$user.roles}</td>
                    <td>{$user.last_activity}</td>
					<td><a href="#" class="edit_user_button" data-toggle="modal" data-target="#edit_user_modal"><button class = "btn btn-primary">Edit</button></a></td>
				</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{/if}
	</div>
	<div class="panel-footer">
		<button type="button" class="btn btn-primary add_user_button" data-toggle="modal" data-target="#create_user_modal">Add user</button>
	</div>
</div>

<div class="modal fade settingsModal" id="create_user_modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add new user</h4>
			</div>
			<div class="modal-body">
				<form id = "create_user_form" action="index.php?page=user_admin" method="post">
					<input type="hidden" name="action" value="user_add">
					<div class="form-group">
						<label for="create_user_login">Login: <span class = "required_field">*</span></label>
						<input type = "text" class="form-control" name = "login" id="create_user_login">
					</div>
					<div class="form-group">
						<label for="create_user_username">User name: <span class = "required_field">*</span></label>
						<input type = "text" class="form-control" name = "name" id="create_user_username">
					</div>
					<div class="form-group">
						<label for="create_user_email">Email: <span class = "required_field">*</span></label>
						<input type = "text" class="form-control" name = "email" id="create_user_email">
					</div>
					<div class="form-group">
						<label for="create_user_password">Password: <span class = "required_field">*</span></label>
						<input class="form-control" type = "password" name = "password" id="create_user_password">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary confirm_create_user">Confirm</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade settingsModal" id="edit_user_modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit user</h4>
			</div>
			<div class="modal-body">
				<form id = "edit_user_form" action="index.php?page=user_admin" method="post">
					<input type="hidden" name="action" value="user_edit">
					<input type="hidden" name="user_id" value="" id = "user_id">
					<div class="form-group">
						<label for="edit_user_login">Login: <span class = "required_field">*</span></label>
						<input class="form-control" name = "login" id="edit_user_login">
					</div>
					<div class="form-group">
						<label for="edit_user_username">User name: <span class = "required_field">*</span></label>
						<input class="form-control" name = "name" id="edit_user_username">
					</div>
					<div class="form-group">
						<label for="edit_user_email">Email: <span class = "required_field">*</span></label>
						<input class="form-control" name = "email" id="edit_user_email">
					</div>
					<div class="form-group">
						<label for="edit_user_password">Password: <span class = "required_field">*</span></label>
						<input class="form-control" type = "password" name = "password" id="edit_user_password">
					</div>
					<div class = "form-group roles">

					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary confirm_edit_user">Confirm</button>
			</div>
		</div>
	</div>
</div>

{include file="inc_administration_bottom.tpl"}
{include file="inc_footer.tpl"}
