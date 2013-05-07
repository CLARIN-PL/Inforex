{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

{if $errors|@count == 0}
	<h2>Nie znaleziono żadnego błędu w adnotacjach</h2>
{else}
	<ol>
	{foreach from=$errors item="row"}
		<li>
			<a href="index.php?page=report&amp;subpage=annotator&amp;id={$row.report_id}">edytuj anotacje</a>
			<pre>{$row.msg}</pre>
		</li>
	{/foreach}
	</ol>
{/if}

</td>

{include file="inc_footer.tpl"}
