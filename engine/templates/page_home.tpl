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
		<div class="panel panel-primary scrollingWrapper" style="margin: 5px">
			<div class="panel-heading">Public corpora</div>
			<div class="panel-body scrolling">
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
			<div class="panel panel-primary scrollingWrapper" style="margin: 5px">
				<div class="panel-heading">Private corpora</div>
				<div class="panel-body">
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
							<tbody>
							{foreach from=$corpus_private item=corpus}
							<tr>
								<td style="color: grey; text-align: right">{$corpus.id}</td>
								<td><a href="?corpus={$corpus.id}&amp;page=start">{$corpus.name}</a></td>
								<td>{$corpus.description}</td>
								<td style="text-align: right">{$corpus.reports}</td>
								<td style="text-align: center;">{$corpus.screename}</td>
							</tr>
							{/foreach}
							</tbody>
						</table>
					{else}
						<div class="infobox-light">
							{if !$user_id}
								<button href="#" type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#loginForm" >Login</button> to see the list.
							{else}
								No corpora available.
							{/if}
						</div>
					{/if}
					</div>
				</div>
                {if $corpus_private}
				<div class="panel-footer">
                    {if "admin"|has_role || "create_corpus"|has_role}
						<div style="clear: both;">
							<button type="button" class="btn btn-primary add_corpora_button">Create a new corpus</button>
						</div>
                    {/if}
				</div>
				{/if}
			</div>
		</td>
	</tr>
</table>

<br style="clear: both"/>

{include file="inc_footer.tpl"}
