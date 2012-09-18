<button type="button" class="delete_corpora_button" style="margin: 10px; padding: 5px 20px">Delete corpora</button>
<input id="corpus_name" type="hidden" value="{$corpus.name}" />
<input id="corpus_id" type="hidden" value="{$corpus.id}" />
<input id="corpus_description" type="hidden" value="{$corpus.description}" />
<table id="delete_corpora" style="width: 300px; margin: 10px; display: none">
	<thead>
		<tr>
			<th>Action</th>
			<th>Status</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Delete reports</td>
			<td>wait</td>
		</tr>					
	</tbody>
</table>