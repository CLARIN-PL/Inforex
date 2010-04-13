{include file="inc_header.tpl"}

<td class="table_cell_content" style="background: #f3f3f3">
<div class="ui-widget">
<form action="index.php?page=document_edit&amp;corpus={$corpus.id}&amp;id={$row.id}" method="post">
	<dl>
		<dt>Tytuł:</dt>
		<dd><input name="title" value="{$title}" style="border: 1px solid #3C769D; width: 700px"/>
		<dt>Źródło:</dt>
		<dd><input name="link" value="{$link}" style="border: 1px solid #3C769D; width: 700px"/>
		<dt>Treść:</dt>
		<dd><textarea id="report_content" name="content">{$content}</textarea></dd>
		<dt>Data:</dt>
		<dd><input name="date" value="{$date}" style="border: 1px solid #3C769D; width: 80px"/>
		<dd style="margin-top: 10px"><input type="submit" value="Zapisz"/>
	</dl>
	<input type="hidden" name="action" value="document_save"/>
</form>
</div>
</td>

{include file="inc_footer.tpl"}
