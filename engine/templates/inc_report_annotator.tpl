{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
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
						<div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
                          <div style="margin: 5px" class="contentBox {$report.format}">{$content_inline|format_annotations}</div>
						</div>
						<div id="rightContent" style="{if !$showRight}display: none{/if};" class="annotations scrolling content rightPanel">
						      <div style="margin: 5px" class="contentBox {$report.format}">{$content_inline2|format_annotations}</div>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
		<td style="width: 330px; vertical-align: top; overflow: none; ">
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
								<td style="vertical-align: top; text-align: right">Text:</td>
								<td class="value" id="annotation_text">-</td>
							</tr>
							<tr>
								<td style="vertical-align: top; text-align: right">Type:</td>
								<td style="vertical-align: top">
									<span id="annotation_redo_type" class="value"></span>
									<div style="float:right">&nbsp;&nbsp;<a href="#" id="changeAnnotationType">(change)</a></div><div style="clear:both"></div>
									<div id="annotation_type" style="display:none">
								</td>
							</tr>
							<tr>
								<td style="vertical-align: top; text-align: right">Attribute:</td>
								<td style="vertical-align: top">
									<span id="shared_attribute" class="value"></span>
								</td>
							</tr>
							<tr>
								<td style="vertical-align: top; text-align: right" title="To change annotation range use following shorcuts">Range:</td>
								<td style="color: DimGray">
									<b>Ctrl + &larr;/&rarr;</b> for left border.<br/>
									<b>Ctrl + Shift + &larr;/&rarr;</b> for right border.
								</td>
							</tr>							
							<tr>
    						     <td></td>
    						     <td>
                                    <input type="button" value="Save" id="annotation_save" disabled="true"/>
                                    <input type="button" value="Cancel" class="annotation_redo"/>
                                 </td>
                            </tr>                            
						</table>																	
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
		 		
		 		{include file="inc_report_annotator_configuration.tpl"}
		 		
                {include file="inc_report_annotator_annotation_pad.tpl"}
				
                {include file="inc_report_annotator_annotations.tpl"}
				
		 		{include file="inc_report_annotator_relations.tpl"}
				
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
				
				
				<h3 style="display:none"><a>Tmp</a></h3>
				<div style="display:none">
					Tmp
				</div>
				
			</div>
		</td>
	</tr>
</table>

