{include file="inc_header.tpl"}

<h1>List of corpora</h1>
<table class="tablesorter" cellspacing="1">
	<tr>
        <th style="text-align: left">ID</th>
		<th style="text-align: left">Name</th>
		<th style="text-align: left">Description</th>
		<th style="text-align: center">Access</th>
		<th style="text-align: right">Documents</th>
	</tr>
    {foreach from=$corpus_set item=corpus}
	<tr>
        <td style="color: grey; text-align: right">{$corpus.id}</td>
		<td><a href="?corpus={$corpus.id}&amp;page=browse">{$corpus.name}</a></td>
		<td>{$corpus.description}</td>
		<td style="text-align: center">{if $corpus.public}public{else}private{/if}</td>		
		<td style="text-align: right">{$corpus.reports}</td>
	</tr>
    {/foreach}
</table>

<br/>
{include file="inc_footer.tpl"}
