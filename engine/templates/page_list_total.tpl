{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Raporty</h1>
<hr/>
<table>
	<tr>
		<th>Rok</th>
		<th>Miesiąc</th>
		<th>Liczba raportów</th>
		<th colspan="3">Sprawdzone</th>
		<th colspan="3">Zaakceptowane</th>
	</tr>
{foreach from=$rows item=r name=list}
	<tr class="{if ($smarty.foreach.list.index%2==0)}even{else}odd{/if} month_{$r.month}">
		{assign var=progress value=$r.s/$r.count*100|string_format:"%01.0f"}
		{if ($r.sz==0)}{assign var=progress_f value=0}{else}{assign var=progress_f value=$r.szf/$r.sz*100|string_format:"%01.0f"}{/if}
		<td>{$r.year}</td>
		<td style="text-align: right;">{$r.month}</td>
		<td style="text-align: right;">{$r.count}</td>
		<td style="text-align: right;">{$r.s}</td>
		<td style="text-align: right; {if ($progress<100)}color: red{/if}">{$r.s/$r.count*100|string_format:"%01.2f"}%</td>
		<td><div style="width: 100px; height: 10px; background: #B3C7FF">
				<div style="width: {$progress}%; background: #3366FF; height: 10px"> </div>
			</div>
		</td>
		<td style="text-align: right">{$r.sz}</td>
		<td style="text-align: right">{$r.szf}</td>
		<td><div style="width: 100px; height: 10px; background: #fc8">
				<div style="width: {$progress_f}%; background: orange; height: 10px"> </div>
			</div>
		</td>
		<td><a href="index.php?page=list&amp;year={$r.year}&amp;month={$r.month}">raporty >></a></td>
	</tr>
{/foreach}
</table>
</td>

{include file="inc_footer.tpl"}