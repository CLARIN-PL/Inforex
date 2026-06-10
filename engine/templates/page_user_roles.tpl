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
							{if $Config.oidcEnabled}
								<hr>
								<p><strong>Authentication:</strong> Keycloak</p>
								{if $user.auth_username}<p><strong>Keycloak username:</strong> {$user.auth_username|escape}</p>{/if}
								{if $user.auth_email}<p><strong>Keycloak email:</strong> {$user.auth_email|escape}</p>{/if}
							{/if}
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

{include file="inc_footer.tpl"}
