{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
	<div id="relation_layers">
		<div style="overflow-y:auto" class="">
		<table class="table table-striped" cellspacing="1" style="width: 100%">
			<thead>
                <tr>
                    <th>Relation set or type</th>
                    <th style="text-align:center; width: 100px" title="Dynamically show/hide layer">Display</th>
                </tr>
			</thead>
			<tbody>
		    {foreach from=$relation_types item=set key=k name=groups}
			    <tr class="relationLayerRow hiddenRow" id = "relation_set_{$set.relation_set_id}" relation_setid="{$set.groupid}">
			    	<td style="vertical-align: middle;" class="layersList">
			    		<span class="count" title="Number of selected annotation types from this layer" style="float: right; font-size: 10px; color: #445967; font-weight: normal;">
                        {if $set.number_of_uses != 0}
                            <strong>({$set.number_of_uses})</strong>
                        {/if}
                        </span>
			    		<span class="relationToggleLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span>
			    		<span class="layerName" style="clear:both">{$set.relation_set_name}</span>
			    	</td>
			    	<td style="vertical-align: middle;text-align:center">
			    		<input name="relationLayerId-{$k}" type="checkbox" class="relation_group_cb" />
			    	</td>
			    </tr>
		    	{foreach from=$set item=subset key=sk name=subsets}
                    {if isset($subset.relation_set_id)}
                        <tr class="relationSublayerRow" relation_typeid="{$subset.id}" relation_set_id = "{$set.relation_set_id}" style = "display: none;">
                            <td style="vertical-align: middle;" class="layersList">
                                <span class="count" title="Number of selected annotation types from this type" style="float: right; font-size: 10px; color: #445967; font-weight: normal;"></span>
                                <span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
                                <span class="layerName" style="margin-left:10px; clear:both">{$subset.name}</span>
                            </td>
                            <td style="vertical-align: middle;text-align:center">
                                <input name="relationTypeId-{$subset.id}" type="checkbox" class="relation_type_cb" />
                            </td>
                        </tr>
                    {/if}
                {/foreach}
            {/foreach}
			{if $relation_types|@count==0}
				<tr>
					<td colspan="2"><i>No layers or types to display</i></td>
				</tr>
			{/if}		    
		    </tbody>		
    	</table>	
    	</div>    
	</div>	