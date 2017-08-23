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

<div class="col-main {if $flags_active}col-md-8{else}col-md-9{/if} scrollingWrapper" id="col-main">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div id="widget_text" class="panel-body column" style="padding: 0">
			<div id="content">
				<div id="leftContent" style="float:left; width: {if $showRight}50%; border-right: 1px solid #E0CFC2{else}100%;{/if}" class="annotations scrolling content">
				  <div style="margin: 5px" class="contentBox {$report.format}">{$content|format_annotations}</div>
				</div>{*
				<div id="rightContent" style="{if !$showRight}display: none{/if};" class="annotations scrolling content rightPanel">
					  <div style="margin: 5px" class="contentBox {$report.format}">{$content_inline2|format_annotations}</div>
				</div>*}
				<div style="clear:both"></div>
			</div>
		</div>
	</div>
</div>

<div id="columnAnnotation" class="col-md-3 scrollingWrapper" style="display: none;">
	<div class="">
		<div id="annotationLoading" style="display: none;">
			Loading data ... <img src="gfx/ajax.gif" />
		</div>


		<div id="annotationEditor">
			<div id="annotation-details" class="panel panel-primary">
				<div class="panel-heading">Annotation details</div>
				<div class="panel-body" style="padding: 0">
					<table style="font-size: 8pt" class="table table-striped" cellspacing="1">
						<tr>
							<th style="vertical-align: top; text-align: right; width: 100px">Id:</th>
							<td class="value" id="annotation_id">-</td>
						</tr>
						<tr>
							<th style="vertical-align: top; text-align: right; width: 100px">Text:</th>
							<td class="value" id="annotation_text">-</td>
						</tr>
						<tr>
							<th style="vertical-align: top; text-align: right">Type:</th>
							<td style="vertical-align: top">
								<span id="annotation_redo_type" class="value" annotation-type-id=""></span>
								<div style="float:right">&nbsp;&nbsp;<a href="#" id="changeAnnotationType" data-toggle="popover">(change)</a></div>
							</td>
						</tr>
						<tr>
							<th style="vertical-align: middle; text-align: right">Lemma:</th>
							<td style="vertical-align: middle">
								<input type="text" id="annotation_lemma" value=""/>
							</td>
						</tr>
						<tr>
							<th style="vertical-align: top; text-align: right">Attributes:</th>
							<td style="vertical-align: top">
								<span id="shared_attribute" class="value"></span>
							</td>
						</tr>
						{*
						<tr>
							<th style="vertical-align: top; text-align: right" title="To change annotation range use following shorcuts">Range:</th>
							<td style="color: DimGray">
								<b>Ctrl + &larr;/&rarr;</b> for left border.<br/>
								<b>Ctrl + Shift + &larr;/&rarr;</b> for right border.
							</td>
						</tr>
						*}
					</table>
				</div>
				<div class="panel-footer">
					<input type="button" value="Save and close" class="btn btn-sm btn-primary" id="annotation_save" disabled="true"/>
					<input type="button" value="Close" class="btn btn-sm btn-warning annotation_redo"/>
					<input type="button" style="float: right" id="annotation_delete" class="btn btn-sm btn-danger" value="Delete annotation"/>
				</div>
			</div>

			<div id="annotation-relations" class="panel panel-default">
				<div class="panel-heading">Annotation relations</div>
				<div class="panel-body" style="padding: 0">
					<div class="annotations relationsContainer scrolling">
						<table class="table table-striped relations" cellspacing="1" style="font-size: 8pt">
							<thead>
								<tr>
									<th>Id</th>
									<th>Relation type</th>
									<th>Target annotation</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<input type="button" value="Cancel" class="btn btn-sm btn-warning relation-cancel" style="display: none"/>
					<div class="dropup relation-types">
						<button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Add relation
							<span class="caret"></span></button>
						<ul class="dropdown-menu">
							<li><a href="#">HTML</a></li>
							<li><a href="#">CSS</a></li>
							<li><a href="#">JavaScript</a></li>
							<li><a href="#">HTML</a></li>
							<li><a href="#">CSS</a></li>
							<li><a href="#">JavaScript</a></li>
							<li><a href="#">HTML</a></li>
							<li><a href="#">CSS</a></li>
							<li><a href="#">JavaScript</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div id="col-config" class="col-md-3 scrollingWrapper">
	<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
		{include file="inc_report_annotator_configuration.tpl" show=true}
		{include file="inc_report_annotator_annotation_pad.tpl"}
		{include file="inc_report_annotator_annotations.tpl"}
		{include file="inc_report_annotator_relations.tpl"}
	</div>
</div>

                {*

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
            </div>
            *}
