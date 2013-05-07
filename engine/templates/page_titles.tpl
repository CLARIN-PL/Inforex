{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">
<table>
{foreach from=$rows item="row}
	<tr>
		<td style="text-align: right">{$row.c}</td>
		<td style="text-align: left">{$row.title}</td>
	</tr>
{/foreach}
</table>
</td>

{include file="inc_footer.tpl"}
