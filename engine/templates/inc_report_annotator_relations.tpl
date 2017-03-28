{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-info">
	<div class="panel-heading" role="tab" id="headingRelations">
		<h4 class="panel-title">
			<a data-toggle="collapse" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseRelations" aria-expanded="false" aria-controls="collapseRelations">
				Relations</a>
		</h4>
	</div>
	<div id="collapseRelations" class="panel-collapse collapse {if $active_accordion=="collapseRelations"}in{/if}">
		<div class="scrollingAccordion">
		<div id="relationList" class="annotations scrolling">
			<table class="table table-striped" cellspacing="1" style="font-size: 8pt">
				<thead>
					<tr>
						<th>Type</th>
						<th>Source</th>
						<th>Target</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$relations item=relation}
					<tr>
						<td class="relation_type_switcher" id="{$relation.id}">{$relation.name}</td>
						<td sourcegroupid={$relation.source_group_id} sourcesubgroupid={$relation.source_annotation_subset_id}><span class="{$relation.source_type}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
						<td targetgroupid={$relation.target_group_id} targetsubgroupid={$relation.target_annotation_subset_id}><span class="{$relation.target_type}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
