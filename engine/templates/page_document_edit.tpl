{include file="inc_header.tpl"}

<td class="table_cell_content">
<form action="index.php?page=document_edit&amp;corpus={$corpus.id}&amp;id={$row.id}" method="post">
	<dl>
		<dt>Tytuł:</dt>
		<dd><input name="title" value="{$report.title}" style="border: 1px solid #3C769D; width: 700px"/>
		<dt>Treść:</dt>
		<dd><textarea id="report_content"></textarea></dd>
		<dd><input type="submit" value="Zapisz"/>
	</dl>
	<input type="hidden" name="action" value="document_save"/>
</form>

</td>

{include file="inc_footer.tpl"}
