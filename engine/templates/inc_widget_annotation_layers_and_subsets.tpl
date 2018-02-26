{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div style="overflow-y: auto; height: 200px">
	<table class="annotation_layers_and_subsets" cellspacing="0" cellpadding="0" style="width: 100%">
		<thead>
		<th>Annotation layers</th>
		<th class = "text-center">Annotation</th>
		<th class = "text-center">Lemma</th>
		<th class = "text-center">Attribute</th>
		</thead>
		<tbody>
        {foreach from=$annotation_types item=set key=k name=groups}
			<tr class="layerRow hiddenRow" setid="{$set.groupid}">
				<td style="vertical-align: middle;font-weight:bold" class="layersList">
					<span class="count" title="Number of selected annotation types from this layer" style="float: right; font-size: 10px; color: #445967; font-weight: normal;"></span>
					<span class="toggleLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span>
					<span class="layerName" style="clear:both">{$set.name}</span>
				</td>
				<td style="vertical-align: middle;text-align:center">
					<input name="layer_ids[]" type="checkbox" class="group_cb" value="{$k}"/>
				</td>
				<td style="vertical-align: middle;text-align:center">
					<input name="layer_lemma_ids[]" type="checkbox" class="lemma_group_cb" value="{$k}"/>
				</td>
				<td style="vertical-align: middle;text-align:center">
					<input name="layer_attribute_ids[]" type="checkbox" class="attribute_group_cb" value="{$k}"/>
				</td>
			</tr>
            {foreach from=$set item=subset key=sk name=subsets}
                {if $sk != "name"}
					<tr class="sublayerRow" subsetid="{$sk}" style="display:none">
						<td style="vertical-align: middle;font-weight:bold" class="layersList">
							<span class="count" title="Number of selected annotation types from this subset" style="float: right; font-size: 10px; color: #445967; font-weight: normal;"></span>
							<span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
							<span class="layerName" style="margin-left:10px;clear:both">{$subset.name}</span>
						</td>
						<td style="vertical-align: middle;text-align:center">
							<input name="subset_ids[]" type="checkbox" class="subset_cb" value="{$sk}" />
						</td>
						<td style="vertical-align: middle;text-align:center">
							<input name="subset_lemma_ids[]" type="checkbox" class="lemma_subset_cb" value="{$sk}" />
						</td>
						<td style="vertical-align: middle;text-align:center">
							<input name="subset_attribute_ids[]" type="checkbox" class="lemma_attribute_cb" value="{$sk}" />
						</td>
					</tr>
                {/if}
            {/foreach}
        {/foreach}
        {if $annotation_types|@count==0}
			<tr>
				<td colspan="2"><i>No layers, subsets nor types to display</i></td>
			</tr>
        {/if}
		</tbody>
	</table>
</div>