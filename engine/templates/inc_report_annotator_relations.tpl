{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{if $smarty.cookies.accordionActive=="cell_relation_list_header"}
<h3 id="cell_relation_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
	<span class="ui-icon ui-icon-triangle-1-s"></span>
	<a tabindex="-1" href="#">Relation list</a>
</h3>
<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{else}
<h3 id="cell_relation_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
	<span class="ui-icon ui-icon-triangle-1-e"></span>
	<a tabindex="-1" href="#">Relation list</a>
</h3>
<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{/if}					
	<div id="relationList" class="annotations">
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