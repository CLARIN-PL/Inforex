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
						<div id="leftContent" style="padding: 5px;float:left; width:49%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">{$content_inline|format_annotations}</div>
						<div id="rightContent" style="padding: 5px;width:49%" class="annotations scrolling content">{$content_inline2|format_annotations}</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
		<td style="width: 360px; vertical-align: top; overflow: auto; ">
            <div class="ui-widget ui-widget-content ui-corner-all">
                <div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Creation of a new relation:</div>
                <div id="relationTypesList" class="annotations ">
                    <table class="tablesorter annotations" cellspacing="1">
                        <thead>
                            <tr>
                                <th style="text-align: center; width: 49%">Source</th>
                                <th></th>
                                <th style="text-align: center; width: 49%">Target</th>
                            </tr>
                         </thead>
                         <tbody>
                            <tr>
                                <td style="text-align: center" id="anaphoraSource"></td>
                                <td style="width: 20px; text-align: center; font-size: 12px; font-weight: bold">↦</td>
                                <td style="text-align: center" id="anaphoraTarget"></td>
                            </tr>
                         </tbody>
                    </table>
                    <ul style="margin-left: 15px; padding-left: 5px;">
                        {foreach from=$availableRelations item=relation}
                            <li>
                                {* <input type="radio" relation_id="{$relation.id}" name="quickAdd"/> *}
                                <span title="{$relation.description}" class="addRelation token hiddenAnnotation" style="cursor:pointer; color: navy;" relation_id="{$relation.id}">{$relation.name}</span><br/>
                                <div style="margin-left: 0px; margin-bottom: 5px;"><small>{$relation.description}</small></div>
                            </li>
                        {/foreach}
                        {* <li><input type="radio" relation_id="0" name="quickAdd" value="0" checked="checked"/>&nbsp;<i>disable quick mode</i></li> *}
                     </ul>                          
                </div>
            </div>
			<div id="cell_annotation_wait" style="display: none;">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
				<input type="hidden" id="report_id" value="{$row.id}"/>
			</div>
			<div class="ui-widget ui-widget-content ui-corner-all">
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">List of anaphora relations:</div>
				<div id="relationList" class="annotations relationsContainer scrolling">
					<table class="tablesorter" cellspacing="1" style="font-size: 8pt">
						<thead>
							<tr>
								<th>Source</th>
								<th>Relation</th>
								<th>Target</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="relationListContainer">
						{foreach from=$allrelations item=relation}
							<tr>
								<td><span class="{$relation.source_type}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
								<td>{$relation.name}</td>
								<td><span class="{$relation.target_type}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
								<td class="relationDelete" source_id="{$relation.source_id}" target_id="{$relation.target_id}" relation_id="{$relation.id}" type_id="{$relation.relation_type_id}" style="cursor:pointer">X</td>
							</tr>
						{/foreach}							
						</tbody>
					</table>	
				</div>
			</div>
		</td>
	</tr>
</table>

