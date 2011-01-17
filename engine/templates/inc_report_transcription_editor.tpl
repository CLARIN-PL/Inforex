<div class="height_fix">
	<b>Treść dokumentu:</b>
</div>

<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">

	<div id="frame_editor">
		<textarea id="report_content" name="content">{$row.content|escape}</textarea>
	</div>
	<div style="padding: 5px" class="height_fix">
		<input type="submit" class="submit button" name="name" value="Save" id="save" disabled="disabled"/>
	</div>

	<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
	<input type="hidden" value="document_content_update" name="action"/>
</form>	