{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 

{if true or $smarty.cookies.accordionActive=="cell_annotation_layers_header"}
<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
	<span class="ui-icon ui-icon-triangle-1-s"></span>
	<a tabindex="-1" href="#">View configuration</a>
</h3>
<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{else}
<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
	<span class="ui-icon ui-icon-triangle-1-e"></span>
	<a tabindex="-1" href="#">View configuration</a>
</h3>
<div style="vertical-align: top; padding: 5px; display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
{/if}
	<div id="annotation_layers" class="scrolling">
		<div style="padding: 5px; overflow-y:auto" class="">
		<table class="tablesorter" cellspacing="1">
			<thead>
			<tr>
		    	<th colspan="2" style="text-align: center">Annotation layers</th>
		    </tr>
			<tr>
				<th>Layer</th>
				<th style="text-align:center" title="Dynamically show/hide layer" >Show</th>
			</tr>
            
			</thead>
			<tbody>
		    {foreach from=$annotation_types item=set key=k name=groups}
			    <tr class="layerRow hiddenRow" setid="{$set.groupid}">
			    	<td style="vertical-align: middle;font-weight:bold" class="layersList"><span class="toggleLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span><span class="layerName" style="clear:both"><a href="#">{$set.name}</a></span></td>
			    	<td style="vertical-align: middle;text-align:center">
			    		<input name="layerId-{$k}" type="checkbox" class="group_cb" /> 
			    	</td>
			    </tr>
		    	{foreach from=$set item=subset key=sk name=subsets}
					{if $sk != "name"}
					<tr class="sublayerRow" subsetid="{$sk}" style="display:none">
							<td style="vertical-align: middle;font-weight:bold" class="layersList">
								<span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
								<span class="toggleSubLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span>
								<span class="layerName" style="margin-left:10px;clear:both"><a href="#">{$subset.name}</a></span>
							</td>
			    			<td style="vertical-align: middle;text-align:center">
			    				<input name="subsetId-{$sk}" type="checkbox" class="subset_cb" /> 
			    			</td>
					</tr>
					{foreach from=$subset item=type key=tk name=types}
						{if $tk != "name"}
						<tr class="typelayerRow" typeid="{$tk}" style="display:none">
								<td style="vertical-align: middle;font-weight:bold" class="layersList">
									<span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
									<span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span>
									<span class="layerName" style="margin-left:20px;clear:both;font-weight:normal;"><a href="#">{$type}</a></span>
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
		    </tbody>		
		    <tfoot>
			    <tr><th colspan="2"></th></tr>
				<tr>
					<th colspan="2" style="text-align: center"><button id="applyLayer" style="margin: 1px; font-size: 0.9em">Apply</button></th>
			    </tr>
		    </tfoot>		    			    
    	</table>	
    	</div>    
	</div>	
</div>