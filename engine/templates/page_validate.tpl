{include file="inc_header.tpl"}

<td class="table_cell_content">

{if $errors|@count == 0}
	<h2>Nie znaleziono żadnego błędu w adnotacjach</h2>
{else}
	<ol>
	{foreach from=$errors item="row"}
		<li><pre>{$row}</pre></li>
	{/foreach}
	</ol>
{/if}

</td>

{include file="inc_footer.tpl"}
