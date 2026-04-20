{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables user-profile-page">
	<div class="panel administration-content-panel user-profile-panel">
		<div class="panel-heading administration-content-heading">
			<span class="administration-content-heading-icon"><i class="fa fa-user-circle" aria-hidden="true"></i></span>
			<span>Your profile</span>
			{if $user.role.loggedin}
				<button type="button" class="btn btn-primary user-profile-header-action" data-toggle="modal" data-target="#password_change_modal">
					<i class="fa fa-lock" aria-hidden="true"></i>
					Change password
				</button>
			{/if}
		</div>
		<div class="panel-body">

			{include file="inc_system_messages.tpl"}

			<div class="row user-profile-grid">
				<div class="col-md-12 user-profile-column">
					<div class="panel panel-default user-profile-card">
						<div class="panel-heading user-profile-card-heading">
							<span class="user-profile-card-icon"><i class="fa fa-shield" aria-hidden="true"></i></span>
							<span>Your system roles</span>
						</div>
						<div class="panel-body">
							<div class="user-profile-role-list">
								{foreach from=$user.role item=description key=role}
									<span class="user-profile-role-badge user-profile-role-badge-system" title="{$description|escape}">{$role}</span>
								{/foreach}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="panel panel-default user-profile-card user-profile-corpora-card">
				<div class="panel-heading user-profile-card-heading">
					<span class="user-profile-card-icon"><i class="fa fa-database" aria-hidden="true"></i></span>
					<span>Your corpora roles</span>
					<span class="user-profile-counter">{$corpus_roles|@count}</span>
				</div>
				<div class="panel-body">
					{if count($corpus_roles)>10}
						<div class="user-profile-toolbar">
							<label for="corpora_filter">Filter</label>
							<input class="form-control" id="corpora_filter" type="text" placeholder="Search corpora...">
						</div>
					{/if}
					<div class="administration-table-wrapper user-profile-table-wrapper">
						<table id="corpora_table" class="table table-striped table-hover sortable administration-table user-profile-table">
							<thead>
								<tr>
									<th>Id</th>
									<th>Corpus name</th>
									<th>Roles</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$corpus_roles item=corpus}
								<tr>
									<td class="column_id">{$corpus.corpus_id}</td>
									<td>{$corpus.corpus_name}</td>
									<td>
										<div class="user-profile-role-list">
											{foreach from=$corpus.roles item = role}
												<span class="user-profile-role-badge user-profile-role-badge-corpus" title="{$role.description|escape}">{$role.role}</span>
											{/foreach}
										</div>
									</td>
								</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{if $user.role.loggedin}
<div class="modal fade settingsModal administration-form-modal user-profile-password-modal" id="password_change_modal" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="password_change_form user-profile-password-form" action="index.php?page=user_roles" method="post">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fa fa-lock" aria-hidden="true"></i> Change password</h4>
				</div>
				<div class="modal-body">
					<input type="hidden" name="action" value="user_password_change"/>
					<div class="form-group">
						<label for="user_old_pass">Old password</label>
						<input id="user_old_pass" class="form-control password_change" type="password" name="old_pass" />
					</div>
					<div class="form-group">
						<label for="user_new_pass1">New password</label>
						<input id="user_new_pass1" class="form-control password_change" type="password" name="new_pass1" maxlength="20"/>
					</div>
					<div class="form-group">
						<label for="user_new_pass2">Repeat password</label>
						<input id="user_new_pass2" class="form-control password_change" type="password" name="new_pass2" maxlength="20"/>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<input type="submit" class="btn btn-primary password_change" value="Change password" disabled="disabled" />
				</div>
			</form>
		</div>
	</div>
</div>
{/if}

{include file="inc_footer.tpl"}
