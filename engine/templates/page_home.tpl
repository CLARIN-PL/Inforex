{include file="inc_header.tpl"}

<div style="width: 49%; float: right">
	<h1>Corpora with restricted access</h1>
{if $corpus_private}
	<table class="tablesorter" id="restricted" cellspacing="1">
		<tr>
	        <th style="text-align: left; width: 15px">ID</th>
			<th style="text-align: left; width: 150px">Name</th>
			<th style="text-align: left">Description</th>
			<th style="text-align: right; width: 50px">Documents</th>
		</tr>
	    {foreach from=$corpus_private item=corpus}
	    <tr>
	        <td style="color: grey; text-align: right">{$corpus.id}</td>
			<td><a href="?corpus={$corpus.id}&amp;page=browse">{$corpus.name}</a></td>
			<td>{$corpus.description}</td>
			<td style="text-align: right">{$corpus.reports}</td>
		</tr>
		{/foreach}	    
	</table>
{else}
<div class="infobox-light"><a href="#" class="login_link">Log in</a> to see the list.</div>
{/if}
</div>

{if $corpus_public}
<div style="width: 49%; margin-bottom: 10px; ">
	<h1>Public corpora</h1>
	<table class="tablesorter" id="public" cellspacing="1">
		<tr>
	        <th style="text-align: left; width: 15px">ID</th>
			<th style="text-align: left; width: 150px">Name</th>
			<th style="text-align: left">Description</th>
			<th style="text-align: right; width: 50px">Documents</th>
		</tr>
	    {foreach from=$corpus_public item=corpus}
	    <tr>
	        <td style="color: grey; text-align: right">{$corpus.id}</td>
			<td><a href="?corpus={$corpus.id}&amp;page=browse">{$corpus.name}</a></td>
			<td>{$corpus.description}</td>
			<td style="text-align: right">{$corpus.reports}</td>
		</tr>
		{/foreach}
	</table>
</div>
{/if}

{if "admin"|has_role}
<button type="button" class="add_corpora_button" style="margin: 10px; padding: 5px 20px">Create new corpora</button>
{/if}

{include file="inc_footer.tpl"}
