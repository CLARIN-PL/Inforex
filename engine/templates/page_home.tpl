{include file="inc_header.tpl"}

<td class="table_cell_content">

	<h1>Available corpora:</h1>
	<table>
		<tr>
			<th style="text-align: left">Name</th>
			<th style="text-align: left">Description</th>
			<th style="text-align: left">Access</th>
		</tr>
	{foreach from=$corpus_set item=corpus}
		{if $corpus.public || $user}
		<tr>
			<td><a href="?corpus={$corpus.id}&amp;page=browse">{$corpus.name}</a></td>
			<td>&mdash; {$corpus.description}</td>
			<td>{if $corpus.public}public{else}private{/if}</td>		
		</tr>
		{/if}
	{/foreach}
	</table>

</td>

{include file="inc_footer.tpl"}
