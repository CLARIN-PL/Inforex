{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
	<div id="annotation_layers">
		<div style="overflow-y:auto" class="">
		<table class="table table-striped" cellspacing="1" style="width: 100%">
			<thead>
                <tr>
                    <th>Annotation set, subset or type</th>
                    <th style="text-align:center; width: 100px" title="Dynamically show/hide layer">Display</th>
                </tr>
			</thead>
			<tbody class="annotationTypesTree" >
				<tr>
					<td colspan="2"><i>No layers, subsets nor types to display</i></td>
				</tr>
			{/if}
			</tbody>    <!-- class=annotationTypesTree -->
    	</table>	
    	</div>    
	</div>	
{include file="inc_widget_annotation_type_tree_set_template.tpl"}
{include file="inc_widget_annotation_type_tree_subset_template.tpl"}
{include file="inc_widget_annotation_type_tree_types_template.tpl"}
