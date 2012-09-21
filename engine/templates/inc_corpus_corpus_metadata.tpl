<table class="tablesorter" cellspacing="1" id="extListContainer" style="width: 300px; margin: 10px; {if not $extList} display:none {/if}">
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
<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="width: 300px; margin: 10px; {if not $extList}display:none{/if}" element="ext" parent="extListContainer">
	<span class="ext_edit" action="add"><a href="#">(create)</a></span>
	<span class="ext_edit" action="edit" style="display:none"><a href="#">(edit)</a></span>
</div>
<button type="button" class="ext_edit" action="add_table" style="margin: 10px; padding: 5px 20px; {if $extList}display:none{/if}">Add custom metadata</button>