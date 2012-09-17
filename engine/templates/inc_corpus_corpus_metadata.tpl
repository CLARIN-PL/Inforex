<table class="tablesorter" cellspacing="1" id="extListContainer" style="width: 300px; margin: 10px">
	<thead>
		<tr>
			<th>Field</th>
			<th>Type</th>
			<th>Null</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$extList item=set}
		<tr>
			<td>{$set.field}</td>
			<td>{$set.type}</td>
			<td>{$set.null}</td>
		</tr>					
		{/foreach}
	</tbody>
</table>
<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="width: 300px; margin: 10px" element="ext" parent="extListContainer">
	<span class="create_ext" action="corpus_edit_ext"><a href="#">(create)</a></span>
</div>