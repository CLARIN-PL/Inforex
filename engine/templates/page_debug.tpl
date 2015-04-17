{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<h2>Debug</h2>

<table class="tablesorter" cellspacing="1" style="width: auto">
	<thead>
		<tr>
			<th>Variable</th>
			<th>Value</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$variables item=v}
		<tr>
			<td>{$v.name}</td>
			<td style="text-align: right">{$v.value}</td>
		</tr>
	{/foreach}
	</tbody>
</table>


{include file="inc_footer.tpl"}