{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table class="tablesorter" cellspacing="1" id="corpus_set_annotation_sets_corpora" style="width: 300px; margin: 10px">
	<thead>
		<tr>
			<th>Id</th>
			<th>Description</th>
			<th>Count</th>
			<th>Assign</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$annotationsList item=set}
		<tr>
			<td>{$set.id}</td>
			<td>{$set.description}</td>
			<td>{$set.count_ann}</td>
			<td><input class="annotationSet" type="checkbox" annotation_set_id="{$set.id}" {if $set.cid} checked="checked" {/if}/></td>
		</tr>
		{/foreach}
	</tbody>
</table>