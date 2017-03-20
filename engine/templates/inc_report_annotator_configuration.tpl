{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-info">
	<div class="panel-heading" role="tab" id="headingOne">
		<h4 class="panel-title">
			<a data-toggle="collapse" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
				View configuration</a>
		</h4>
	</div>
	<div id="collapseOne" class="panel-collapse collapse {if $show=="1"}in{/if}" style="padding: 2px;">
		<div id="annotation_layers" class="scrolling scrollingAccordion">
		   {* Poniższe opcje dostępne wyłącznie w widoku do edycji anotacji *}
		   {if $subpage=="annotator"}
			<div class="panel panel-default">
				<div class="panel-heading">Working mode</div>
				<div class="panel-body">
				   <input type="hidden" id="annotation_mode" value="{$annotation_mode}"/>
				   <div id="annotation_mode_list">
					   {if "annotate"|has_corpus_role}
						   <div class="radio">
							   <label><input type="radio" class="radio" name="annotation_mode" value="final" title="Work on final annotations"/> public annotations</label>
						   </div>
					   {/if}
					   {if "annotate_agreement"|has_corpus_role}
						   <div class="radio">
							   <label><input type="radio" class="radio" name="annotation_mode" value="agreement" title="Work on annotations for agreement measurement"/> agreement</label>
						   </div>
					   {/if}
				   </div>
				</div>
			</div>
		   {/if}

			<div class="panel panel-default">
				<div class="panel-heading">Annotations</div>
				<div class="panel-body">
				   {if $subpage=="preview"}
				   <div style="margin-left: 5px">
					Stage:
					<select name="stage">
						{foreach from=$stages item=s}
							<option value="{$s}" {if $s==$stage}selected="selected"{/if}>{$s}</option>
						{/foreach}
					</select>
					</div>
				   {/if}

					<div style="padding: 5px; overflow-y:auto" class="">
					<table class="tablesorter" cellspacing="1">
						<thead>
						<tr>
							<th rowspan="2">Layer</th>
							<th colspan="3" style="text-align:center" title="Physically show/hide layer -- reload page is required to rebuild document structure" >Display</th>
							<th rowspan="2" style="text-align:center" title="Dynamically show/hide layer" >Show</th>
						</tr>
						<tr>
							<th style="text-align:center" title="Left" >Left</th>
							<th style="text-align:center" title="Right" >Right
								<input id="showRight" type="checkbox"{if $smarty.cookies.showRight=="true"} checked="checked"{/if} style="vertical-align: middle" title="Show/hide right panel"/>
							</th>
							<th style="text-align:center" title="None" >None</th>
						</tr>
						</thead>
						<tbody>
						{foreach from=$annotation_types item=set key=k name=groups}
							<tr class="layerRow hiddenRow" setid="{$set.groupid}">
								<td style="vertical-align: middle;font-weight:bold" class="layersList"><span class="toggleLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span><span class="layerName" style="clear:both"><a href="#">{$k}</a></span></td>
								<td style="vertical-align: middle;text-align:center"><input name="layerId{$set.groupid}" type="radio" class="leftLayer" /> </td>
								<td style="vertical-align: middle;text-align:center" class="rightPanel"><input name="layerId{$set.groupid}" type="radio" class="rightLayer" /> </td>
								<td style="vertical-align: middle;text-align:center; background-color: #FF9B9B"><input name="layerId{$set.groupid}" type="radio" class="clearLayer"/></td>
								<td style="vertical-align: middle;text-align:center"><input name="layerId{$set.groupid}" type="checkbox" class="hideLayer" /> </td>
							</tr>
							{foreach from=$set item=subset key=k2}
								{if $k2!="groupid" && $k2!="none"}
								<tr class="sublayerRow" subsetid="{$subset.subsetid}" style="display:none">
									<td style="vertical-align: middle"><span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span><span class="layerName" style="clear:both"><a href="#">{$k2}</a></span></td>
									<td style="vertical-align: middle;text-align:center"><input name="sublayerId{$subset.subsetid}" type="radio" checked="checked" class="leftSublayer" /> </td>
									<td style="vertical-align: middle;text-align:center" class="rightPanel"><input name="sublayerId{$subset.subsetid}" type="radio" class="rightSublayer" /> </td>
									<td style="vertical-align: middle;text-align:center; background-color: #FF9B9B"><input name="sublayerId{$subset.subsetid}" type="radio" class="clearSublayer"/></td>
									<td style="vertical-align: middle;text-align:center"><input name="sublayerId{$subset.subsetid}" type="checkbox" class="hideSublayer" /> </td>
								</tr>
								{/if}
							{/foreach}
						{/foreach}
						</tbody>
					</table>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">Relations</div>
				<div class="panel-body">
					{foreach from=$relation_sets item=rel_set}
						<input class="relation_sets" type="checkbox" value="{$rel_set.relation_set_id}" {if $rel_set.active}checked="checked"{/if} />
							{$rel_set.name}</br>
					{/foreach}
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">Other</div>
				<div class="panel-body">
					{if $smarty.cookies.splitSentences=="true"}
					<input id="splitSentences" type="checkbox" checked="checked" style="vertical-align: middle"/>
					{else}
					<input id="splitSentences" type="checkbox"  style="vertical-align: middle"/>
					{/if}
					Display every sentence separately
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<button id="applyLayer" class="btn btn-primary">Apply configuration</button>
		</div>
	</div>
</div>
