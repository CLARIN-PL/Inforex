<div style="border: 1px solid #666; padding: 2px; background: #eee">		
	<input type="button" value="amount" class="an"/>
	<input type="button" value="company" class="an"/>
	<input type="button" value="date" class="an"/>
	<input type="button" value="institution" class="an"/>
	<input type="button" value="person" class="an"/><br/>
	<input type="button" value="location" class="an"/>
	<input type="button" value="city" class="an"/>
	<input type="button" value="street" class="an"/>
	<input type="button" value="house_num" class="an"/>
	<input type="button" value="postal" class="an"/>
	<span id="add_annotation_status"></span>
</div>

<form method="post" action="index.php?page=report&amp;id={$row.id}">

	<table style="width: 100%">
	<tr>
		<td style="width: 60%; vertical-align: top">
			<textarea name="content" style="width: 100%;" wrap="on" id="edit" class="autogrow">{$content_formated}</textarea>	
		</td>
		<td style="vertical-align: top">
			{$row.content|format_annotations}	
		</td>
	</tr>
	</table>

	<input type="submit" value="Zapisz" name="formatowanie" id="formating"/>
	<input type="hidden" value="{$row.id}" id="report_id"/>
	<input type="hidden" value="{$row.status}" id="status"/>
	<input type="hidden" value="{$row.type}" id="type"/>
</form>

<script type="text/javascript">
$(".an").click(function(){ldelim}
	insert_annotation($(this).val());
{rdelim});
</script>