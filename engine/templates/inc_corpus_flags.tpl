{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table class="tablesorter" cellspacing="1" id="flagsListContainer" style="width: 500px; margin: 10px">
	<thead>
		<tr>
			<th style="width: 10px">Id</th>
			<th>Name</th>
			<th>Short name</th>
			<th>Description</th>
			<th style="width: 10px; text-align: right">Sort</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$flagsList item=set}
		<tr>
			<td>{$set.id}</td>
			<td class="name">{$set.name}</td>
			<td class="short">{$set.short}</td>
			<td class="description">{$set.description}</td>
			<td class="sort">{$set.sort}</td>
		</tr>					
		{/foreach}
	</tbody>
</table>
<div class="tableOptions" style="width: 300px; margin: 10px" element="flag" parent="flagsListContainer">
	<span class="create" action="corpus_add_flag"><a href="#" class="button">New</a></span>
	<span class="edit" style="display:none"><a href="#" class="button">Edit</a></span>
	<span class="delete" style="display:none"><a href="#" class="button warning">Delete</a></span>
</div>