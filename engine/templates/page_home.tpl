{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="container-fluid">
	<div class="row">
		<div class="col-md-6" style="padding: 0">
			{if $corpus_public}
				<div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
					<div class="panel-heading">Public corpora</div>
					<div class="panel-body scrolling">
                        <div class="navbar-collapse collapse">
                            <form class="navbar-form search-form">
                                <div class="form-group" style="display:inline;">
                                    <div class="input-group" style="display:table;">
                                        <span class="input-group-addon" style="width:1%;"><span class="glyphicon glyphicon-search"></span></span>
                                        <input title = "Type at least 3 characters to search..." class="search_input form-control" name="public_corpora_table" placeholder="Search Here" autocomplete="off" type="text">
                                    </div>
                                </div>
                            </form>
                        </div>
						<table class="table table-striped" id="public" cellspacing="1">
							<thead>
							<tr>
								<th style="text-align: left; width: 25px">ID</th>
								<th style="text-align: left; width: 150px">Name</th>
								<th style="text-align: left">Description</th>
								<th style="text-align: right; width: 50px">Documents</th>
							</tr>
							</thead>
							<tbody id = "public_corpora_table">
							{foreach from=$corpus_public item=corpus}
							<tr>
								<td style="color: grey; text-align: right">{$corpus.id}</td>
								<td><a href="?corpus={$corpus.id}&amp;page=corpus_start">{$corpus.name}</a></td>
								<td>{$corpus.description}</td>
								<td style="text-align: right">{$corpus.reports}</td>
							</tr>
							{/foreach}
							</tbody>
						</table>
					</div>
				</div>
			{/if}
		</div>

		<div class="col-md-6" style="padding: 0">
			<div class="panel panel-primary scrollingWrapper" style="margin: 5px">
				<div class="panel-heading">Private corpora</div>
				<div class="panel-body">
                    {if $user_id}
                        <div class="navbar-collapse collapse">
                            <form class="navbar-form search-form">
                                <div class="form-group" style="display:inline;">
                                    <div class="input-group" style="display:table;">
                                        <span class="input-group-addon" style="width:1%;"><span class="glyphicon glyphicon-search"></span></span>
                                        <input title = "Type at least 3 characters to search..." class="search_input form-control" name="private_corpora_table" placeholder="Search Here" autocomplete="off"  type="text">
                                    </div>
                                </div>
                            </form>
                        </div>
                    {/if}
					<div class="scrolling">
					{if $corpus_private}
						<table class="table table-striped" id="user_corpora" cellspacing="1">
							<thead>
							<tr>
								<th style="text-align: left; width: 25px">ID</th>
								<th style="text-align: left; width: 200px">Name</th>
								<th style="text-align: left">Description</th>
								<th style="text-align: right; width: 50px">Documents</th>
								<th style="text-align: center; width: 150px">Owner</th>
							</tr>
							</thead>
							<tbody id = "private_corpora_table">
							{foreach from=$corpus_private item=corpus}
							<tr>
								<td style="color: grey; text-align: right">{$corpus.id}</td>
								<td><a href="?corpus={$corpus.id}&amp;page=corpus_start">{$corpus.name}</a></td>
								<td>{$corpus.description}</td>
								<td style="text-align: right">{$corpus.reports}</td>
								<td style="text-align: center;">{$corpus.screename}</td>
							</tr>
							{/foreach}
							</tbody>
						</table>
					{else}
						<div class="infobox-light">
							{if !$user_id && !($config->federationLoginUrl)}
								<button href="#" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#loginForm" >Login</button> to see the list.
							{else}
								No corpora available, please log in.
							{/if}
						</div>
					{/if}
					</div>
				</div>
				<div class="panel-footer">
                    {if $user_id}
                        <div style="clear: both;">
                            <button type="button" class="btn btn-primary add_corpora_button" data-toggle="modal" data-target="#createCorpus">Create a new corpus</button>
                        </div>
                    {/if}
				</div>
			</div>
        </div>
	</div>
</div>

<div class="modal fade createCorpusModal" id="createCorpus" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Create new corpora</h4>
			</div>
			<div class="modal-body">
				<form id = "create_corpus_form">
					<div class="form-group">
						<label for="corpus_name">Name: <span class = "required_field">*</span></label>
						<input class="form-control" name = "corpus_name" id="corpus_name" required>
					</div>
					<div class="form-group">
						<label for="corpus_description">Description: <span class = "required_field">*</span></label>
						<textarea class="form-control" name = "corpus_description" rows="5" id="corpus_description" required></textarea>
					</div>
					<div class ="form-group">
						<label for="elementPublic">Public</label>
						<input id="elementPublic" type="checkbox"> <small>(access for not logged users)</small>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary confirmCorpus">Confirm</button>
			</div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
