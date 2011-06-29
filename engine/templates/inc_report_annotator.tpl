<div id="dialog" title="Błąd" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
		<span class="message"></span>
	</p>
	<p><i><a href="">Refresh page.</a></i></p>
</div>
 
<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="content">
						<div id="leftContent" style="padding: 5px;float:left; width:49%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">{$content_inline|format_annotations}</div>
						<div id="rightContent" style="padding: 5px;width:49%" class="annotations scrolling content">{$content_inline2|format_annotations}</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
		<td style="width: 300px; vertical-align: top; overflow: auto; ">
			<div id="cell_annotation_wait" style="display: none;">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
			</div>
			<div id="rightPanelEventEdit" style="vertical-align: top; display: none">
				<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">	
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Options</div>
					<input type="button" value="Go back" id="cancelEvent"/>
					<input type="button" value="Delete event" id="deleteEvent"/>
				</div>				
				<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">	
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Event details</div>
					<table style="font-size: 8pt">
						<tr>
							<th style="text-align: right">Id:</th>
							<td id="eventDetailsId" eventid="0">-</td>
						</tr>
						<tr>
							<th style="text-align: right">Type:</th>
							<td id="eventDetailsType" typeid="0"></td>
						</tr>
					</table>
				</div>					
				<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Slots list</div>
					<div class="annotations slotsContainer scrolling">
						<table id="eventSlotsTable" class="tablesorter">
							<thead>
								<tr>
									<th>id</th>
									<th>type</th>
									<th>annotation</th>
									<th style="text-align:center">X</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>	
					</div>
					<div> 
						<div id="eventEditOptions">
							<select id="eventTypeSlots"></select>
							<button id="addEventSlot">+</button>
						</div>			
						<div id="addAnnotationContainer" style="display:none">
							Select annotation or <button id="cancelAddAnnotation">Cancel</button>
						</div>						
					</div>
					
				</div>
			</div>
			<div id="rightPanelEdit" style="vertical-align: top; display: none;">
				<div id="cell_annotation_edit">
					<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">	
						<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation Editor</div>
						<div style="float: right; line-height: 22px;">[<a href="#" id="annotation_delete" style="color:red">delete annotation</a>]</div>
						<input type="button" value="Close annotation editor" class="annotation_redo"/>
					</div>				
						
					<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff; margin-top: 5px;">	
						<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation details</div>
						<table style="font-size: 8pt">
							<tr>
								<th style="vertical-align: top; text-align: right">Text:</th>
								<td class="value" id="annotation_text">-</td>
							</tr>
							<tr>
								<th style="vertical-align: top; text-align: right">Type:</th>
								<td style="vertical-align: top">
									<span id="annotation_redo_type" class="value"></span>
									<div style="float:right">&nbsp;&nbsp;<a href="#" id="changeAnnotationType">(change)</a></div><div style="clear:both"></div>
									<div id="annotation_type" style="display:none">
								</td>
							</tr>
							<tr>
								<th style="vertical-align: top; text-align: right" title="To change annotation range use following shorcuts">Range:</th>
								<td style="color: DimGray">
									<b>Ctrl + &larr;/&rarr;</b> for left border.<br/>
									<b>Ctrl + Shift + &larr;/&rarr;</b> for right border.
								</td>
							</tr>
						</table>																	
						<div>						
							<input type="button" value="Cancel" class="annotation_redo"/>
							<input type="button" value="Save" id="annotation_save" disabled="true"/>							
						</div>
					</div>
					
					<div id="relationsPanel" class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff; margin-top: 5px;">			
						<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Relation list</div>
						<div class="annotations relationsContainer scrolling">
							<div>
								<input type="button" value="Add relation" id="relation_add"/>
								<div id="relation_select" style="display:none">
									<label for="relation_type">1. Choose type: </label>
									<select id="relation_type"></select> <br/>
									2. Select target annotation or <input type="button" value="Cancel" id="relation_cancel"/>
								</div>							
							</div>
							<table id="relation_table" class="tablesorter" cellspacing="1" style="font-size: 8pt">
								<thead>
									<tr>
										<th>Relation type</th>
										<th>Target annotation</th>
										<th>X</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>						
					</div>
				</div>			
			</div>
			
		 	<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">
		 		{if $smarty.cookies.accordionActive=="cell_annotation_layers_header"}
		 		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotation layers</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{else}
		 		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation layers</a>
		 		</h3>
				<div style="vertical-align: top; padding: 5px; display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{/if}
					<div id="annotation_layers" class="scrolling">
						<div style="padding: 5px; overflow-y:auto" class="">
						<table class="tablesorter" cellspacing="1">
							<thead>
							<tr>
								<th rowspan="2">Layer</th>
								<th colspan="3" style="text-align:center" title="Physically show/hide layer -- reload page is required to rebuild document structure" >Display</th>
								<th rowspan="2" style="text-align:center" title="Dynamically show/hide layer" >Show</th>
							</tr>
                            <tr>
                                <th style="text-align:center" title="None" >None</th>
                                <th style="text-align:center" title="Left" >Left</th>
                                <th style="text-align:center" title="Right" >Right</th>
                            </tr>
							</thead>
							<tbody>
						    {foreach from=$annotation_types item=set key=k name=groups}
							    <tr class="layerRow hiddenRow" setid="{$set.groupid}">
							    	<td style="vertical-align: middle;font-weight:bold"><span class="toggleLayer ui-icon ui-icon-circlesmall-plus" style="float:left"></span><span class="layerName" style="clear:both">{$k}</span></td>
							    	<td style="vertical-align: middle;text-align:center"><input name="layerId{$set.groupid}" type="checkbox" class="clearLayer"/></td>
							    	<td style="vertical-align: middle;text-align:center"><input name="layerId{$set.groupid}" type="radio" class="leftLayer" /> </td>
							    	<td style="vertical-align: middle;text-align:center"><input name="layerId{$set.groupid}" type="radio" checked="checked" class="rightLayer" /> </td>
							    	<td style="vertical-align: middle;text-align:center"><input name="layerId{$set.groupid}" type="checkbox" class="hideLayer" /> </td>
							    </tr>  
						    	{foreach from=$set item=subset key=k2}
							    	{if $k2!="groupid" && $k2!="none"}
							    	<tr class="sublayerRow" subsetid="{$subset.subsetid}" style="display:none">
								    	<td style="vertical-align: middle"><span class="ui-icon ui-icon-carat-1-sw" style="float:left"></span><span class="layerName" style="clear:both">{$k2}</span></td>
								    	<td style="vertical-align: middle;text-align:center"><input name="sublayerId{$subset.subsetid}" type="radio" class="clearSublayer"/></td>
								    	<td style="vertical-align: middle;text-align:center"><input name="sublayerId{$subset.subsetid}" type="radio" class="leftSublayer" /> </td>
								    	<td style="vertical-align: middle;text-align:center"><input name="sublayerId{$subset.subsetid}" type="radio" checked="checked" class="rightSublayer" /> </td>
								    	<td style="vertical-align: middle;text-align:center"><input name="sublayerId{$subset.subsetid}" type="checkbox" class="hideSublayer" /> </td>
							    	</tr>
							    	{/if}
						    	{/foreach}
						    {/foreach}
						    </tbody>
						    <tfoot>
						      <tr>
						          <th></th>
						          <th colspan="3" style="text-align: center"><button id="applyLayer" style="margin: 1px; font-size: 0.9em">Apply</button></th>
						          <th></th>
						      </tr>
						    </tfoot>				    
				    	</table>
				    	</div>
			    	</div>		 		
				</div>
		 		{if $smarty.cookies.accordionActive=="cell_annotation_add_header"}
		 		<h3 id="cell_annotation_add_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotation pad</a>
		 			
		 		</h3>
				<div id="cell_annotation_add" style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{else}
		 		<h3 id="cell_annotation_add_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation pad</a>
		 			
		 		</h3>
				<div id="cell_annotation_add" style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{/if}				
					<div class="column" id="widget_annotation">
						<div style="padding: 5px;" class="annotations scrolling">
							<button id="quick_add_cancel" style="display:none">Cancel quick add</button>
							<input type="radio" name="default_annotation" id="default_annotation_zero" style="display: none;" value="" checked="checked"/>
						    {foreach from=$annotation_types item=set key=k name=groups}		
						    	<div>
						    		&raquo; <a href="" label="#gr{$smarty.foreach.groups.index}" class="toggle_cookie"><b>{$k}</b> <small style="color: #777">[show/hide]</small></a>
						    	</div>
						    	<div id="gr{$smarty.foreach.groups.index}" groupid="{$set.groupid}">
						    		<ul style="margin: 0px; padding: 0 30px">
										{foreach from=$set item=set key=set_name name=subsets}
										{if $set_name != "groupid"}
							    			{if $set_name != "none"}
												<li subsetid="{$set.subsetid}">
							    				<a href="#" class="toggle_cookie" label="#gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}"><b>{$set_name}</b> <small style="color: #777">[show/hide]</small></a>
												<ul style="padding: 0px 10px; margin: 0px" id="gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}">
											{/if}					
											{foreach from=$set item=type key=subsetname}
												{if $subsetname!="subsetid"}
												<li>
													<div>
														<input type="radio" name="default_annotation" value="{$type.name}" style="vertical-align: text-bottom" title="quick annotation &mdash; adds annotation for every selected text"/>
														<span class="{$type.name}" groupid="{$type.groupid}">
															<a href="#" type="button" value="{$type.name}" class="an" style="color: #555" title="{$type.description}">
															{if $type.short_description==null}
																{$type.name}
															{else}
																{$type.short_description}
															{/if}
															</a>
														</span>
													</div>
												</li>
												{/if}
											{/foreach}
							    			{if $set_name != "none"}
												</ul>
												</li>
											{/if}
										{/if} 
										{/foreach}
									</ul>		
								</div>
							{/foreach}
							<span id="add_annotation_status"></span>
							<input type="hidden" id="report_id" value="{$row.id}"/>
						</div>
					</div>
				</div>

				
		 		{if $smarty.cookies.accordionActive=="cell_annotation_list_header"}
		 		<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotation list</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation list</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}					
					<div id="annotationList" class="annotations scrolling" style="height: 200px">
						<var id="annotationsCount">0</var> annotation(s) on disabled/hidden layers
						{foreach from=$sets key=setName item=set}
							<h1>{$setName}</h1>
							{foreach from=$set key=subsetName item=subset}
								{if $subsetName!="groupid"}
									<h3 style="background: #bbb">{$subsetName}</h3>
									
									<table class="tablesorter" cellspacing="1">
									   <thead>
									       <tr>
										       <th>Annotation</th>
										       <th>Type</th>
										       <th>Stage</th>
										       <th>Source</th>
                                               <th>X</th>
										   </tr>
									   </thead>
									   <tbody>
											{foreach from=$subset key=typeName item=type}
												{foreach from=$type key=annkey item=annotation}											
													{if is_array($annotation) }
													<tr>
													    <td><span class="{$annotation.type}" title="an#{$annotation.id}:{$annotation.type}">{$annotation.text}</span></td>
													    <td>{$annotation.type}</td>
                                                        <td><small>{$annotation.stage}</small></td>
                                                        <td><small>{$annotation.source}</small></td>
                                                        <td style="text-align: center"><a href="#">x</a></td>
													</tr>
													{/if}										
												{/foreach}
											{/foreach}
									   </tbody>
									</table>
								{/if}
							{/foreach}
						{/foreach}
					</div>
				</div>
				
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
									<th>Jednostka źródłowa</th>
									<th>Nazwa relacji</th>
									<th>Jednostka docelowa</th>
								</tr>
							</thead>
							<tbody>
							{foreach from=$allrelations item=relation}
								<tr>
									<td><span class="{$relation.source_type}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
									<td>{$relation.name}</td>
									<td><span class="{$relation.target_type}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
								</tr>
							{/foreach}							
							</tbody>
						</table>	
					</div>
				</div>
		 		{if $smarty.cookies.accordionActive=="cell_event_list_header"}
		 		<h3 id="cell_event_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Event list</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_event_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Event list</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}					
					<div id="eventList" class="annotations" style="overflow-y:auto" >
						<table id="eventTable" class="tablesorter" cellspacing="1" style="font-size: 8pt">
							<thead>
								<tr>
									<th>id</th>
									<th>group</th>
									<th>type</th>
									<th>slots</th>
								<tr>
							</thead>
							<tbody>
								{foreach from=$events item=event}
									<tr><td><a href="#" eventid="{$event.report_event_id}" typeid="{$event.event_type_id}">#{$event.report_event_id}</a></td><td>{$event.groupname}</td><td>{$event.typename}</td><td>{$event.slots}</td></tr>
								{/foreach}							
							</tbody>
						</table>			
					</div>
						<div id="eventOptionPanel">
							<select id="eventGroups">
							{foreach from=$event_groups item=group}
								<option value="{$group.name}" groupId="{$group.event_group_id}">{$group.name}</option>
							{/foreach}
							</select>
							<select id="eventGroupTypes">
							</select>
							<button id="addEvent">+</button>
						</div>
				</div>
				
				{*
		 		{if $smarty.cookies.accordionActive=="cell_flags_header"}
		 		<h3 id="cell_flags_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Flags</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_flags_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Flags</a>
		 		</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}					
					<div id="flagList" class="annotations" style="overflow-y:auto" >
						<table id="flagTable" class="tablesorter">
							<thead>
								<tr>
									<th>id</th>
									<th>name</th>
									<th>status</th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$corporaflags item=corporaflag}
									<tr>
										<td>{$corporaflag.id}</td>
										<td>{$corporaflag.name}</td>
										<td>
											<select class="setFlag" cflagid="{$corporaflag.id}">
											{foreach from=$flags item=flag}
												<option value="{$flag.name}" flagid="{$flag.id}" {if $corporaflag.flag_id==$flag.id}selected="selected"{/if}>{$flag.name}</option>
											{/foreach}
											</select>
										</td>
									</tr>
								{/foreach}						
							</tbody>
						</table>	
					</div>
				</div>	
				*}			
				
				<h3 style="display:none"><a>Tmp</a></h3>
				<div style="display:none">
					Tmp
				</div>
				
			</div>
		</td>
	</tr>
</table>

