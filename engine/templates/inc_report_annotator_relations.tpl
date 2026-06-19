{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-info report-relations-accordion-panel">
	<div class="panel-heading" role="tab" id="headingRelations">
		<h4 class="panel-title">
			<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseRelations" aria-expanded="false" aria-controls="collapseRelations">
				Relations</a>
		</h4>
	</div>
	<div id="collapseRelations" class="panel-collapse collapse {if $active_accordion=="collapseRelations"}in{/if}">
		<div class="scrollingAccordion">
		<div id="relationList" class="annotations scrolling">
			<div class="report-relations-toolbar">
				<button
					type="button"
					class="btn btn-xs btn-primary report-relations-graph-trigger"
					data-toggle="modal"
					data-target="#relationsGraphModal"
					data-relation-list-disabled="{if $relation_list_disabled}1{else}0{/if}"
				>
					<i class="fa fa-sitemap" aria-hidden="true"></i>
					<span>View graph</span>
				</button>
			</div>
			{if $relation_list_disabled}
				<div class="alert alert-warning" style="margin: 10px;">
					Relation list was disabled for this preview because the document contains {$relation_count} relations.
					The text preview is still available.
				</div>
			{else}
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
					<tr
						class="relation-graph-row"
						data-relation-id="{$relation.id}"
						data-relation-name="{$relation.name|escape:'html'}"
						data-source-id="{$relation.source_id}"
						data-source-type="{$relation.source_type|escape:'html'}"
						data-source-text="{$relation.source_text|escape:'html'}"
						data-target-id="{$relation.target_id}"
						data-target-type="{$relation.target_type|escape:'html'}"
						data-target-text="{$relation.target_text|escape:'html'}"
						title="Open this relation in graph view"
					>
						<td class="relation_type_switcher" id="{$relation.id}">{$relation.name}</td>
						<td sourcegroupid={$relation.source_group_id} sourcesubgroupid={$relation.source_annotation_subset_id}><span class="ann annotation_set_{$relation.source_group_id} {$relation.source_type}" groupid="{$relation.source_group_id}" subgroupid="{$relation.source_annotation_subset_id}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
						<td targetgroupid={$relation.target_group_id} targetsubgroupid={$relation.target_annotation_subset_id}><span class="ann annotation_set_{$relation.target_group_id} {$relation.target_type}" groupid="{$relation.target_group_id}" subgroupid="{$relation.target_annotation_subset_id}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
					</tr>
				{/foreach}
				</tbody>
			</table>
			{/if}
		</div>
		</div>
	</div>
</div>

<div class="modal fade report-relations-graph-modal" id="relationsGraphModal" tabindex="-1" role="dialog" aria-labelledby="relationsGraphModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<div class="report-relations-graph-header-main">
					<h4 class="modal-title" id="relationsGraphModalLabel"><i class="fa fa-sitemap" aria-hidden="true"></i> Relations graph</h4>
					<div class="report-relations-graph-summary"></div>
				</div>
				<button type="button" class="close report-relations-graph-close-top" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="report-relations-graph-topbar">
					<div class="report-relations-graph-legend"></div>
					<div class="btn-group btn-group-xs report-relations-graph-layout-switch" role="group" aria-label="Graph layout">
						<button type="button" class="btn btn-default is-active" data-layout="compact" title="Compact layout" aria-label="Compact layout">
							<i class="fa fa-compress" aria-hidden="true"></i>
						</button>
						<button type="button" class="btn btn-default" data-layout="expanded" title="Expanded layout" aria-label="Expanded layout">
							<i class="fa fa-expand" aria-hidden="true"></i>
						</button>
					</div>
				</div>
				<div class="report-relations-graph-empty" style="display: none;"></div>
				<div class="report-relations-graph-canvas-wrap">
					<svg class="report-relations-graph-canvas" viewBox="0 0 960 620" preserveAspectRatio="xMidYMid meet"></svg>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default report-relations-graph-close-bottom" data-dismiss="modal">
					<i class="fa fa-times" aria-hidden="true"></i>
					<span>Close</span>
				</button>
			</div>
		</div>
	</div>
</div>
