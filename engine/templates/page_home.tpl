{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables home-corpora-page">
	<div class="row home-corpora-grid">
		<div class="col-md-6 home-corpora-column">
			<div class="panel scrollingWrapper administration-content-panel home-corpora-panel">
				<div class="panel-heading administration-content-heading">
					<span class="administration-content-heading-icon"><i class="fa fa-globe" aria-hidden="true"></i></span>
					<span>Public corpora</span>
					<span class="home-corpora-counter">{$corpus_public|@count}</span>
				</div>
				<div class="panel-body">
					{if $corpus_public}
						<div class="home-corpora-toolbar">
							<label for="home_public_search">Filter</label>
							<div class="input-group home-corpora-search">
								<span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
								<input id="home_public_search" title="Type at least 3 characters to search..." class="search_input form-control" name="public_corpora_table" placeholder="Search public corpora..." autocomplete="off" type="text">
							</div>
						</div>
						<div class="administration-table-wrapper home-corpora-table-wrapper">
							<table class="table table-striped table-hover administration-table home-corpora-table" id="public" cellspacing="1">
								<thead>
								<tr>
									<th class="td-right home-corpora-id-column">ID</th>
									<th>Name</th>
									<th>Description</th>
									<th class="td-right home-corpora-count-column" title="Documents">Docs</th>
								</tr>
								</thead>
								<tbody id="public_corpora_table">
								{foreach from=$corpus_public item=corpus}
									<tr>
										<td class="column_id td-right">{$corpus.id}</td>
										<td><a class="home-corpus-link" href="?corpus={$corpus.id}&amp;page=corpus_start">{$corpus.name}</a></td>
										<td><div class="home-corpora-description" title="{$corpus.description|escape}">{$corpus.description}</div></td>
										<td class="td-right"><span class="home-corpora-documents-badge">{$corpus.reports}</span></td>
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
					{else}
						<div class="home-corpora-empty">
							<i class="fa fa-folder-open-o" aria-hidden="true"></i>
							{if !$user_id && !$Config.federationLoginUrl}
								<span>
									{if $Config.oidcEnabled}
										<a href="index.php?page=login_oidc" class="btn btn-success btn-sm">Federated login</a>
									{else}
										<button href="#" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#loginForm">Login</button>
									{/if}
									to see the list.
								</span>
							{else}
								<span>No public corpora available.</span>
							{/if}
						</div>
					{/if}
				</div>
			</div>
		</div>

		<div class="col-md-6 home-corpora-column">
			<div class="panel scrollingWrapper administration-content-panel home-corpora-panel">
				<div class="panel-heading administration-content-heading">
					<span class="administration-content-heading-icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
					<span>Private corpora</span>
					<span class="home-corpora-counter">{$corpus_private|@count}</span>
					{if $user_id}
						<button type="button" class="btn btn-primary add_corpora_button home-corpora-create-button" data-toggle="modal" data-target="#createCorpus">
							<i class="fa fa-plus" aria-hidden="true"></i>
							Create corpus
						</button>
					{/if}
				</div>
				<div class="panel-body">
					{if $corpus_private}
						<div class="home-corpora-toolbar">
							<label for="home_private_search">Filter</label>
							<div class="input-group home-corpora-search">
								<span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
								<input id="home_private_search" title="Type at least 3 characters to search..." class="search_input form-control" name="private_corpora_table" placeholder="Search private corpora..." autocomplete="off" type="text">
							</div>
						</div>
						<div class="administration-table-wrapper home-corpora-table-wrapper">
							<table class="table table-striped table-hover administration-table home-corpora-table home-private-corpora-table" id="user_corpora" cellspacing="1">
								<thead>
								<tr>
									<th class="td-right home-corpora-id-column">ID</th>
									<th>Name</th>
									<th>Description</th>
									<th class="td-right home-corpora-count-column" title="Documents">Docs</th>
									<th class="td-center home-corpora-owner-column">Owner</th>
								</tr>
								</thead>
								<tbody id="private_corpora_table">
								{foreach from=$corpus_private item=corpus}
									<tr>
										<td class="column_id td-right">{$corpus.id}</td>
										<td><a class="home-corpus-link" href="?corpus={$corpus.id}&amp;page=corpus_start">{$corpus.name}</a></td>
										<td><div class="home-corpora-description" title="{$corpus.description|escape}">{$corpus.description}</div></td>
										<td class="td-right"><span class="home-corpora-documents-badge">{$corpus.reports}</span></td>
										<td class="td-center">
											<span class="administration-owner-initials" title="{$corpus.screename|escape}">{$corpus.owner_initials|escape}</span>
										</td>
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
						<div class="home-corpora-pagination" id="private_corpora_pagination">
							<div class="home-corpora-pagination-info" id="private_corpora_pagination_info"></div>
							<div class="home-corpora-pagination-controls" id="private_corpora_pagination_controls"></div>
						</div>
					{else}
						<div class="home-corpora-empty">
							<i class="fa fa-folder-open-o" aria-hidden="true"></i>
							{if !$user_id && !$Config.federationLoginUrl}
								<span>
									{if $Config.oidcEnabled}
										<a href="index.php?page=login_oidc" class="btn btn-success btn-sm">Federated login</a>
									{else}
										<button href="#" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#loginForm">Login</button>
									{/if}
									to see the list.
								</span>
							{else}
								<span>No corpora available, please log in.</span>
							{/if}
						</div>
					{/if}
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade createCorpusModal administration-form-modal home-corpora-modal" id="createCorpus" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"><i class="fa fa-folder-open" aria-hidden="true"></i> Create new corpus</h4>
			</div>
			<div class="modal-body">
				<form id="create_corpus_form">
					<div class="form-group">
						<label for="corpus_name">Name: <span class="required_field">*</span></label>
						<input class="form-control" name="corpus_name" id="corpus_name" required>
					</div>
					<div class="form-group">
						<label for="corpus_description">Description: <span class="required_field">*</span></label>
						<textarea class="form-control administration-compact-textarea" name="corpus_description" rows="4" id="corpus_description" required></textarea>
					</div>
					<div class="form-group home-corpora-public-field">
						<label class="home-corpora-checkbox">
							<input id="elementPublic" type="checkbox">
							<span>Public</span>
						</label>
						<small>Access for not logged users</small>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary confirmCorpus">Create corpus</button>
			</div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
