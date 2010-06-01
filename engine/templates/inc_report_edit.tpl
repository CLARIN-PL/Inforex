<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
	<dl>
		<dt style="clear: left">Status:</dt>
		<dd>{$select_status}</dd>

		<dt style="clear: left">Typ:</dt>
		<dd>{$select_type}</dd>

		<dt style="clear: left">Tytuł:</dt>
		<dd><input name="title" value="{$row.title|escape}" style="border: 1px solid #3C769D; width: 700px"/></dd>

		<dt style="clear: left">Źródło:</dt>
		<dd><input name="link" value="{$row.link|escape}" style="border: 1px solid #3C769D; width: 700px"/></dd>

	<!--
	<tr>
		<th>Firma:</th>
		<td>{$row.company}</td>
	</tr>
	<tr>
		<th>Link:</th>
		<td><a href="{$row.link}" target="_blank">{$row.link}</a></td>
	</tr>
	-->				
		<dt>Treść:</dt>
		<dd>
			<textarea name="content" id="edit">{$row.content|escape}</textarea>
		</dd>
		
		<dd style="margin-top: 10px;">
			<input type="submit" value="Zapisz" name="formatowanie" id="formating"/>
			<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
			<input type="hidden" value="document_save" name="action"/>
		</dd>
	</dl>
</form>



