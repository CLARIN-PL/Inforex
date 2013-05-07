{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table class="tablesorter" cellspacing="1" id="corpus_set_corpus_event_groups" style="width: 300px; margin: 10px">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>Description</th>
			<th>Assign</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$eventList item=set}
		<tr>
			<td>{$set.id}</td>
			<td>{$set.name}</td>
			<td>{$set.description}</td>
			<td><input class="setEventGroup" type="checkbox" event_group_id="{$set.id}" {if $set.cid} checked="checked" {/if}/></td>
		</tr>
		{/foreach}
	</tbody>
</table>