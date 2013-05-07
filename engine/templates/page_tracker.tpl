{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

{$annotation_count}

<table>
	<tr>
		<th>Od</th>
		<th>Do</th>
		<th>Czas (min.)</th>
		<th>Anotacji</th>
		<th>Łączny czas (h)</th>
	</tr>
{foreach from=$tracker item=track}
	<tr>
		<td>{$track.from}</td>
		<td>{$track.to}</td>
		<td style="text-align: right">{$track.time}</td>
		<td style="text-align: right">{$track.count}</td>
		<td style="text-align: right">{$track.sum}</td>
	</tr>
{/foreach}
</table>

{include file="inc_footer.tpl"}