{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<table style="width: 100%">
	<tr>
	<td style="width: 50%; vertical-align: top; padding-right: 10px">
	{if $corpus_public}
		<div class="panel panel-primary" style="margin: 5px">
			<div class="panel-heading">Public corpora</div>
			<div class="panel-body">
				<table class="table table-striped" id="public" cellspacing="1">
					<thead>
					<tr>
						<th style="text-align: left; width: 25px">ID</th>
						<th style="text-align: left; width: 150px">Name</th>
						<th style="text-align: left">Description</th>
						<th style="text-align: right; width: 50px">Documents</th>
					</tr>
					</thead>
					<tbody>
					{foreach from=$corpus_public item=corpus}
					<tr>
						<td style="color: grey; text-align: right">{$corpus.id}</td>
						<td><a href="?corpus={$corpus.id}&amp;page=start">{$corpus.name}</a></td>
						<td>{$corpus.description}</td>
						<td style="text-align: right">{$corpus.reports}</td>
					</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	{/if}
	</td>
		<td style="width: 50%; vertical-align: top">
			<div class="panel panel-primary" style="margin: 5px">
				<div class="panel-heading">Private corpora</div>
				<div class="panel-body">

					{if $corpus_private}
					<div class="panel panel-default">
						<div class="panel-heading">Your corpora</div>
						<div class="panel-body">

						<table class="table table-striped" id="user_corpora" cellspacing="1">
							<thead>
							<tr>
								<th style="text-align: left; width: 25px">ID</th>
								<th style="text-align: left; width: 150px">Name</th>
								<th style="text-align: left">Description</th>
								<th style="text-align: right; width: 50px">Documents</th>
							</tr>
							</thead>
							<tbody>
							{foreach from=$corpus_private item=corpus}
							{if $corpus.user_id == $user.user_id}
							<tr>
								<td style="color: grey; text-align: right">{$corpus.id}</td>
								<td><a href="?corpus={$corpus.id}&amp;page=start">{$corpus.name}</a></td>
								<td>{$corpus.description}</td>
								<td style="text-align: right">{$corpus.reports}</td>
							</tr>
							{/if}
							{/foreach}
							</tbody>
						</table>
						</div>
						<div class="panel-footer">
							{if "admin"|has_role || "create_corpus"|has_role}
							<div style="clear: both;">
								<button type="button" class="btn btn-primary add_corpora_button">Create a new corpus</button>
							</div>
						{/if}
						</div>
					</div>

					<div class="panel panel-default">
						<div class="panel-heading">Other private corpora you have access to</div>
						<div class="panel-body">

						<table class="table table-striped" id="restricted" cellspacing="1">
							<thead>
							<tr>
								<th style="text-align: left; width: 25px">ID</th>
								<th style="text-align: left; width: 150px">Name</th>
								<th style="text-align: left">Description</th>
								<th style="text-align: right; width: 50px">Documents</th>
							</tr>
							</thead>
							<tbody>
							{foreach from=$corpus_private item=corpus}
							{if $corpus.user_id != $user.user_id}
							<tr>
								<td style="color: grey; text-align: right">{$corpus.id}</td>
								<td><a href="?corpus={$corpus.id}&amp;page=start">{$corpus.name}</a></td>
								<td>{$corpus.description}</td>
								<td style="text-align: right">{$corpus.reports}</td>
							</tr>
							{/if}
							{/foreach}
							</tbody>
						</table>
						</div>
					</div>
					{else}
						<div class="infobox-light">
							{if !$user_id}
								<a href="#" class="login_link">Log in</a> to see the list.
							{else}
								No corpora available.
							{/if}
						</div>
					{/if}
				</div>
			</div>
		</td>
	</tr>
</table>

<br style="clear: both"/>

{include file="inc_footer.tpl"}
