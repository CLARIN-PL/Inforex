<table class="tablesorter" cellspacing="1" id="corpusElementsContainer" style="width: 300px; margin: 10px">
	<tr>
		<th id="name">Name: </th>
		<td>{$corpus.name}</td>
	</tr>
	<tr>		
		<th id="user_id">Owner:</th> 
		<td>{$owner.screename}</td>
	</tr>
	<tr>		
		<th id="public">Access:</th> 
		<td>{if $corpus.public}public{else}restricted{/if}</td>
	</tr>
	<tr>		
		<th id="description">Description:</th> 
		<td>{$corpus.description}</td>
	</tr>
	<tr>		
		<th id="ext">Metadata:</th> 
		<td>{$corpus.ext}</td>
	</tr>	
</table>
{if isCorpusOwner() || "admin"|has_role}
<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="width: 300px; margin: 10px; display:none" element="corpus_details" parent="corpusElementsContainer">
	<span class="edit" style="display:none"><a href="#">(edit)</a></span>
</div>
{/if}