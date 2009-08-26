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
		<th>Treść</t>
		<td>		
			<input type="button" value="amount" class="an"/>
			<input type="button" value="company" class="an"/>
			<input type="button" value="date" class="an"/>
			<input type="button" value="person" class="an"/>
			<textarea name="content" style="width: 100%; height: 300px;" wrap="on" id="edit">{$content_formated}</textarea>
			<input type="submit" value="Zapisz" name="formatowanie" id="formating"/>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
$(".an").click(function(){ldelim}
	var obj = $("#edit").getSelection();
	var type = $(this).val();
	$("#edit").replaceSelection("<an:" + type + ">" + obj.text + "</an>");
{rdelim});
</script>



