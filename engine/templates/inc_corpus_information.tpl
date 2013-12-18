{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table class="tablesorter" cellspacing="1" id="corpusElementsContainer" style="width: 600px; margin: 10px">
	<tr>
		<th id="name">Name: </th>
		<td>{$corpus.name}</td>
		{if isCorpusOwner() || "admin"|has_role}
		<td>
			<div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
				<a href="#" class="edit" style="margin: 2px">edit</a>
			</div>
		</td>
		{/if}
	</tr>
	<tr>		
		<th id="user_id">Owner:</th> 
		<td>{$owner.screename}</td>
		{if isCorpusOwner() || "admin"|has_role}
		<td>
			<div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
				<a href="#" class="edit" style="margin: 2px">edit</a>
			</div>
		</td>
		{/if}
	</tr>
	<tr>		
		<th id="public">Access:</th> 
		<td>{if $corpus.public}public{else}restricted{/if}</td>
		{if isCorpusOwner() || "admin"|has_role}
		<td>
			<div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
				<a href="#" class="edit" style="margin: 2px">edit</a>
			</div>
		</td>
		{/if}
	</tr>
	<tr>		
		<th id="description">Description:</th> 
		<td>{$corpus.description}</td>
		{if isCorpusOwner() || "admin"|has_role}
		<td>
			<div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
				<a href="#" type="button" class="edit" style="margin: 2px">edit</a>
			</div>
		</td>
		{/if}
	</tr>		
</table>