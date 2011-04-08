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
					<div id="content" style="padding: 5px;" class="annotations scrolling">{$content_inline|format_annotations}</div>
				</div>
			</div>
		</td>
		<td style="width: 280px; vertical-align: top">
			<div id="cell_annotation_wait" style="display: none;width: 280px">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
			</div>
			<div id="rightPanelEventEdit" style="width: 280px; vertical-align: top; display: none">
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
								<th>id</th>
								<th>type</th>
								<th>annotation</th>
								<th style="text-align:center">X</th>
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
				<!--<div class="annotations">
					Lista slotów zdarzenia:
					<table id="eventSlotsTable" class="tablesorter">
						<thead>
							<th>id</th>
							<th>type</th>
							<th>annotation</th>
							<th style="text-align:center">X</th>
						</thead>
						<tbody>
						</tbody>
					</table>	
					<div id="eventEditOptions">
						<select id="eventTypeSlots"></select>
						<button id="addEventSlot">+</button>
					</div>			
					<div id="addAnnotationContainer" style="display:none">
						Wybierz anotację lub <button id="cancelAddAnnotation">Anuluj</button>
					</div>
					
				</div>!-->
			</div>
			<div id="rightPanelEdit" style="width: 280px; vertical-align: top; display: none">
				<div id="cell_annotation_edit">
					<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">	
						<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation Editor</div>
						<div style="float:right">
							<a href="#" id="annotation_delete" style="color:red">delete</a>
						</div>						
						<div style="float:left">						
							<input type="button" value="Go back" id="annotation_redo"/>
							<input type="button" value="Save" id="annotation_save" disabled="true"/>							
						</div>
						<div style="clear:both"></div>
					</div>					
					<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">	
						<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotation details</div>
						<table style="font-size: 8pt">
							<tr>
								<th style="text-align: right">Text:</th>
								<td id="annotation_text">-</td>
							</tr>
							<tr>
								<th style="text-align: right">Type:</th>
								<td><span id="annotation_redo_type"></span><div style="float:right">&nbsp;&nbsp;<a href="#" id="changeAnnotationType">(change)</a></div><div style="clear:both"></div></td>
							</tr>
						</table>
						<div id="annotation_type" style="display:none"></div>{* {$select_annotation_types} *}						
						
					</div>
					<div class="ui-state-highlight ui-corner-all ui-state-error" id="block_message" style="display: none; margin: 2px 0">
						<p>
							<span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>
							Adding annotation is disabled &mdash; <b><span id="block_reason"></span></b>
						</p>
					</div>
					
					<div id="relationsPanel" class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
						<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Relation list</div>
						<div class="annotations relationsContainer scrolling">
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
						<div>
							<input type="button" value="Add relation" id="relation_add"/>
							<div id="relation_select" style="display:none">
								<label for="relation_type">Choose type</label>
								<select id="relation_type"></select> and select target annotation or
								<input type="button" value="Cancel" id="relation_cancel"/>
							</div>							
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
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{else}
		 		<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation layers</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top; padding: 5px; display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{/if}
					<div id="annotation_layers">
						<table class="tablesorter" cellspacing="1">
							<thead>
							<tr>
								<th>Layer</th>
								<th style="text-align:center" title="Dynamically show/hide layer" >Display</th>
								<th style="text-align:center" title="Physically show/hide layer -- reload page is required to rebuild document structure" >Enable</th>
							</tr>
							</thead>
							<tbody>
						    {foreach from=$annotation_types item=set key=k name=groups}
						    <tr>
						    	<td style="vertical-align: middle"><span class="layerName">{$k}</span></td>
						    	<td style="text-align:center"><input name="layerId{$set.groupid}" type="checkbox" class="hideLayer" /> </td>
						    	<td style="text-align:center"><input name="layerId{$set.groupid}" type="checkbox" class="clearLayer"/></td>
						    </tr>  
						    {/foreach}
						    </tbody>
				    
				    	</table>
			    	</div>		 		
				</div>
		 		{if $smarty.cookies.accordionActive=="cell_annotation_add_header"}
		 		<h3 id="cell_annotation_add_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Annotation pad</a>
		 			
		 		</h3>
				<div id="cell_annotation_add" style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
		 		{else}
		 		<h3 id="cell_annotation_add_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation pad</a>
		 			
		 		</h3>
				<div id="cell_annotation_add" style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
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
											<li>
						    				<a href="#" class="toggle_cookie" label="#gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}"><b>{$set_name}</b> <small style="color: #777">[show/hide]</small></a>
											<ul style="padding: 0px 10px; margin: 0px" id="gr{$smarty.foreach.groups.index}s{$smarty.foreach.subsets.index}">
										{/if}					
										{foreach from=$set item=type}
											<li>
												<div>
													<input type="radio" name="default_annotation" value="{$type.name}" style="vertical-align: text-bottom" title="quick annotation &mdash; adds annotation for every selected text"/>
													<span class="{$type.name}" groupid="{$type.groupid}"><a href="#" type="button" value="{$type.name}" class="an" style="color: #555" title="{$type.description}">{$type.name}</a></span>
												</div>
											</li>
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
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_annotation_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Annotation list</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}					
					<div id="annotationList" class="annotations">
						<var id="annotationsCount">0</var> annotation(s) on disabled/hidden layers
						<ul style="padding:10px">
						{foreach from=$sets key=setName item=set}
							<li class="setName" groupid="{$set.groupid}">{$setName}
								<ul class="setContainer" groupid="{$set.groupid}">
								{foreach from=$set key=subsetName item=subset}
									{if $subsetName!="groupid"}
									<li class="subsetName">{$subsetName}
									<ul class="subsetContainer">
									{foreach from=$subset key=typeName item=type}
										<li class="typeName">{$typeName}
										<ul class="typeContainer">
										{foreach from=$type item=annotation}
											<li class="annotationElement">
											
												<span class="{$annotation.type}" title="an#{$annotation.id}:{$annotation.type}">{$annotation.text}</span>
											</li>
										{/foreach}
										</ul>
										</li>
									{/foreach}
									</ul>
									</li>
									{/if}
								{/foreach}
								</ul>
							</li>
						{/foreach}
						<ul>
					</div>
				</div>
				
		 		{if $smarty.cookies.accordionActive=="cell_relation_list_header"}
		 		<h3 id="cell_relation_list_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
		 			<span class="ui-icon ui-icon-triangle-1-s"></span>
		 			<a tabindex="-1" href="#">Relation list</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_relation_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Relation list</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
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
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{else}
		 		<h3 id="cell_event_list_header" class="ui-accordion-header ui-helper-reset ui-state-default ui-corner-all" aria-expanded="false" role="tab" tabindex="-1">
		 			<span class="ui-icon ui-icon-triangle-1-e"></span>
		 			<a tabindex="-1" href="#">Event list</a>
		 		</h3>
				<div style="width: 280px; vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:none" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">
				{/if}					
					<div id="eventList" class="annotations" style="overflow-y:auto" >
						<table id="eventTable" class="tablesorter">
							<thead>
								<th>id</th>
								<th>group</th>
								<th>type</th>
								<th>slots</th>
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
				<h3 style="display:none"><a>Tmp</a></h3>
				<div style="display:none">
					Tmp
				</div>
				
			</div>
		</td>
	</tr>
</table>
</div>

