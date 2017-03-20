{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-info">
	<div class="panel-heading" role="tab" id="headingThree">
		<h4 class="panel-title">
			<a data-toggle="collapse" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
				Relations</a>
		</h4>
	</div>
	<div id="collapseThree" class="panel-collapse collapse">
		<div id="relationList" class="annotations scrolling scrollingAccordion">
			<table class="tablesorter" cellspacing="1" style="font-size: 8pt">
				<thead>
					<tr>
						<th>Source</th>
						<th>Relation name</th>
						<th>Target</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$allrelations item=relation}
					<tr>
						<td sourcegroupid={$relation.source_group_id} sourcesubgroupid={$relation.source_annotation_subset_id}><span class="{$relation.source_type}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
						<td class="relation_type_switcher" id="{$relation.id}">{$relation.name}</td>
						<td targetgroupid={$relation.target_group_id} targetsubgroupid={$relation.target_annotation_subset_id}><span class="{$relation.target_type}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
