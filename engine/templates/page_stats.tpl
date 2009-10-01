{include file="inc_header.tpl"}
{include file="inc_menu.tpl"}

<td class="table_cell_content">

<h1>Statystyki korpusu</h1>

<table>
	<tr>
		<td>Liczba raportów:</td> 
		<td style="text-align: right"><b>{$report_count}</b></td>
		<td><small>raporty oznaczone jako sprawdzone</small></td>
	</tr>
	<tr>
		<td>Liczba tokenów:</td> 
		<td style="text-align: right"> <b>{$token_count}</b></td>
		<td><small>tokeny dzielone po białych znakach</small></td>
	</tr>
	<tr>
		<td>Liczba znaków:</td> 
		<td style="text-align: right"> <b>{$char_count}</b></td>
		<td><small>bez białych znaków</small></td>
	</tr>
	<tr>
		<td>Liczba adnotacji:</td> 
		<td style="text-align: right"> <b>{$annotation_count}</b></td>
		<td><small></small></td>
	</tr>
</td>
{include file="inc_footer.tpl"}