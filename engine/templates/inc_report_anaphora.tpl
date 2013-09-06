{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<table style="width: 100%">
	<tr>
		<td style="vertical-align: top">
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">			
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="content" style="padding: 5px;" class="annotations scrolling">{$content_inline|format_annotations}</div>
				</div>
			</div>
		</td>
		<td style="vertical-align: top; width: 450px;">
		    <div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">List of anaphora relations:</div>
		    <div style="padding: 2px;" class="annotations">
				<table class="tablesorter" cellspacing="1" style="width: 100%">
				    <thead>
				        <tr>
				            <th>Source</th>
				            <th style="width: 200px">Relation</th>
				            <th>Target</th>
				        </tr>
				    </thead>
				    <tbody>
				    
				    </tbody>
				</table>
			</div>
		</td>
	</tr>
</table>
<input type="hidden" id="report_id" value="{$row.id}"/>