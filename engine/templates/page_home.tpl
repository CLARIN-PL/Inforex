{include file="inc_header.tpl"}

<td class="table_cell_content">

	<h1>Dostępne korpusy:</h1>
	<ul>
		{foreach from=$corpus_set item=corpus}
			{if $corpus.public || $user}
			<li><a href="?corpus={$corpus.id}&amp;page=browse">{$corpus.name}</a> &mdash; {$corpus.description}</li>
			{/if}
		{/foreach}
	</ul>

</td>

{include file="inc_footer.tpl"}
