{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table class="tablesorter" cellspacing="1" id="flagsListContainer" style="width: 300px; margin: 10px">
	<thead>
		<tr>
			<th>Id</th>
			<th>Name</th>
			<th>Short</th>
			<th>Sort</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$flagsList item=set}
		<tr>
			<td>{$set.id}</td>
			<td>{$set.name}</td>
			<td>{$set.short}</td>
			<td>{$set.sort}</td>
		</tr>					
		{/foreach}
	</tbody>
</table>
<div class="tableOptions ui-widget ui-widget-content ui-corner-all" style="width: 300px; margin: 10px" element="flag" parent="flagsListContainer">
	<span class="create" action="corpus_add_flag"><a href="#">(create)</a></span>
	<span class="edit" style="display:none"><a href="#">(edit)</a></span>
	<span class="delete" style="display:none"><a href="#">(delete)</a></span>
</div>