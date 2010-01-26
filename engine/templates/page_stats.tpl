{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Statystyki korpusu</h1>

<table>
	<tr>
		<th style="width: 150px"></th>
		<th style="width: 100px">Wszystkie</th>
		<th style="width: 100px">Sprawdzone</th>
		<th></th>
	</tr>
	<tr>
		<td>Liczba raportów:</td> 
		<td style="text-align: right"><b>{$all.report_count}</b></td>
		<td style="text-align: right"><b>{$checked.report_count}</b></td>
		<td><small>raporty oznaczone jako sprawdzone</small></td>
	</tr>
	<tr>
		<td>Liczba tokenów:</td> 
		<td style="text-align: right"><b>{$all.token_count}</b></td>
		<td style="text-align: right"> <b>{$checked.token_count}</b></td>
		<td><small>tokeny dzielone po białych znakach</small></td>
	</tr>
	<tr>
		<td>Liczba znaków:</td> 
		<td style="text-align: right"><b>{$all.char_count}</b></td>
		<td style="text-align: right"> <b>{$checked.char_count}</b></td>
		<td><small>bez białych znaków</small></td>
	</tr>
	<tr>
		<td style="width: 150px">Liczba adnotacji:</td>
		<td style="width: 100px; text-align: right"> <b>{$annotation_count}</b>
	</td>
</table>

<h2>Liczba adnotacji wg. rodzaju</h2>
<table>
{foreach from=$tags item=tag}
<tr>
	<td>{$tag.type}</td><td>{$tag.count}</td>
</tr>
{/foreach}
</table>

{include file="inc_footer.tpl"}