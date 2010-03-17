{if $status==1}
<div style="background: gold; padding: 4px; border: 1px solid darkorange">
<form method="post" action="index.php?page=report&amp;id={$row.id}" style="display: inline">
	<input type="submit" value="Formatuj i zapisz jako sprawdzony" name="formatowanie_quick" id="formating"/>
	<input type="hidden" name="next_report_id" value="{$row_next}"/>	
</form>
<span style="color: #444"> &mdash; zmień status raportu na <i>Sprawdzony</i>, przeformatuj treść raportu i przejdź do następnego.</span>
</div>
{/if}

<form method="post" action="index.php?page=report&amp;id={$row.id}">
<table id="report">
	<tr>
		<th style="vertical-align: middle">Status:</th>
		<td>{$select_status}</td>
	</tr>
	<tr>
		<th style="vertical-align: middle">Typ:</th>
		<td>{$select_type}</td>
	</tr>
	<tr>
		<th>Tytuł:</th>
		<td><b>{$row.title}</b></td>
	</tr>
	<tr>
		<th>Firma:</th>
		<td>{$row.company}</td>
	</tr>
	<tr>
		<th>Link:</th>
		<td><a href="{$row.link}" target="_blank">{$row.link}</a></td>
	</tr>				
	<tr>
		<th>Treść</t>
		<td>
			<textarea name="content" style="width: 100%; height: 300px;" wrap="on" id="edit">{$row.content}</textarea>
			<input type="submit" value="Zapisz" name="formatowanie" id="formating"/>
			<input type="hidden" value="{$row.id}" id="report_id"/>
		</td>
	</tr>
</table>
</form>



