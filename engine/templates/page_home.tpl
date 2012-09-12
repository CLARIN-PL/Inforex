{include file="inc_header.tpl"}

{if $corpus_private}
<h1>Corpora with restricted access</h1>
<table class="tablesorter" cellspacing="1">
	<tr>
        <th style="text-align: left">ID</th>
		<th style="text-align: left">Name</th>
		<th style="text-align: left">Description</th>
		<th style="text-align: right">Documents</th>
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
<br/>
{/if}

{if $corpus_public}
<h1>Public corpora</h1>
<table class="tablesorter" cellspacing="1">
	<tr>
        <th style="text-align: left">ID</th>
		<th style="text-align: left">Name</th>
		<th style="text-align: left">Description</th>
		<th style="text-align: right">Documents</th>
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
{/if}
<br/>
{include file="inc_footer.tpl"}
