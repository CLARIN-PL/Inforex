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
			<tbody>
		    {foreach from=$annotation_types item=set key=k name=groups}
			    <tr class="layerRow hiddenRow" setid="{$set.groupid}">
			    	<td style="vertical-align: middle;" class="layersList">
			    		<span class="count" title="Number of selected annotation types from this layer" style="float: right; font-size: 10px; color: #445967; font-weight: normal;"></span>
			    		<span class="toggleLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span>
			    		<span class="layerName" style="clear:both">{$set.name}</span>
			    	</td>
			    	<td style="vertical-align: middle;text-align:center">
			    		<input name="layerId-{$k}" type="checkbox" class="group_cb" /> 
			    	</td>
			    </tr>
		    	{foreach from=$set item=subset key=sk name=subsets}
					{if $sk != "name"}
					<tr class="sublayerRow" subsetid="{$sk}" style="display:none">
						<td style="vertical-align: middle;" class="layersList">
							<span class="count" title="Number of selected annotation types from this subset" style="float: right; font-size: 10px; color: #445967; font-weight: normal;"></span>
							<span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
							<span class="toggleSubLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span>
							<span class="layerName" style="margin-left:10px;clear:both">{$subset.name}</span>
						</td>
			    		<td style="vertical-align: middle;text-align:center">
			    			<input name="subsetId-{$sk}" type="checkbox" class="subset_cb" /> 
			    		</td>
					</tr>
					{foreach from=$subset item=type key=tk name=types}
						{if $tk != "name"}
					<tr class="typelayerRow" typeid="{$tk}" style="display:none">
						<td style="vertical-align: middle;" class="layersList">
							<span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
							<span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
							<span class="layerName" style="margin-left:20px;clear:both;font-weight:normal;">{$type}</span>
						</td>
				    	<td style="vertical-align: middle;text-align:center">
				    		<input name="typeId-{$tk}" type="checkbox" class="leftLayer type_cb" /> 
				    	</td>
					</tr>
						{/if}
					{/foreach}					
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
	</div>	