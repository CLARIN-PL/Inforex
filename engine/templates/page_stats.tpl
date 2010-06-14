{include file="inc_header.tpl"}

<td class="table_cell_content">

<h1>Statystyki korpusu</h1>

<table cellspacing="1" class="formated">
	<thead>
	<tr>
		<th rowspan="1"></th>
		<th colspan="2">Raporty<br/>sprawdzone</th>
		<th rowspan="2">Komentarz</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<th>Liczba raportów:</th>
		{* 
		<td style="text-align: right"><em>{$all.report_count}<em></td>
		<td>szt.</td>
		*}
		<td style="text-align: right"><em>{$checked.report_count}</em></td>
		<td>szt.</td>
		<td><small><i>*sprawdzone czy treść raportu została poprawnie wyłuskana z HTML-a</i></small></td>
	</tr>
	<tr>
		<th>Liczba tokenów:</th> 
		{* 
		<td style="text-align: right"><em>{$all.token_count}</em></td>
		<td>tokenów</td>
		*}
		<td style="text-align: right"><em>{$checked.token_count}</em></td>
		<td>tokenów</td>
		<td><small><i>wartość szacowana bez tokenizacji &mdash; tekst dzielony po białych znakach</i></small></td>
	</tr>
	<tr>
		<th>Liczba znaków:</th> 
		{* 
		<td style="text-align: right">{$all.char_count}</td>
		<td>znaków</td>
		*}
		<td style="text-align: right">{$checked.char_count}</td>
		<td>znaków</td>
		<td><small><i>bez białych znaków</i></small></td>
	</tr>
	<tr>
		<th>Rozmiar:</th> 
		{* 
		<td style="text-align: right"><em>{$all.size}</em></td>
		<td>MB</td>
		*}
		<td style="text-align: right"><em>{$checked.size}</em></td>
		<td>MB</td>
		<td><small><i></i></small></td>
	</tr>
	<tr>
		<th rowspan="2">Średnia długość:</th> 
		{* 
		<td style="text-align: right"><em>{$all.avg_tokens}</em></td>
		<td>tokenów</td>
		*}
		<td style="text-align: right"><em>{$checked.avg_tokens}</em></td>
		<td>tokenów</td>
		<td><small><i>średnia liczba tokenów w raporcie (wartość szacowana bez tokenizacji)</i></small></td>
	</tr>
	<tr>
		{* 
		<td style="text-align: right"><em>{$all.avg_length}</em></td>
		<td>znaków</td>
		*}
		<td style="text-align: right"><em>{$checked.avg_length}</em></td>
		<td>znaków</td>
		<td><small><i>średnia liczba znaków w raporcie</i></small></td>
	</tr>
	</tbody>
</table>
<br/>

{include file="inc_footer.tpl"}