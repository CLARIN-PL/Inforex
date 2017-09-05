-{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<table id="relation-sets" class="table table-striped">
	<thead>
	<tr>
		<th>Relation set</th>
		<th style="text-align: center; width: 100px">Display</th>
	</tr>
	</thead>
    {foreach from=$relation_sets item=rel_set}
		<tr>
			<td>{$rel_set.name}</td>
			<td style="text-align: center"><input class="relation_sets" type="checkbox" value="{$rel_set.relation_set_id}" {if $rel_set.active}checked="checked"{/if} /></td>
		</tr>
    {/foreach}
</table>
