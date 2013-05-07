{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotations:</div>
	<div style="padding: 2px;">
		<div class="scrolling" style="overflow: auto">				
			<table id="annotations" class="tablesorter" cellspacing="1">
				<thead>
				<tr>
					<th style="width: 25px">Id</td>
					<th>Type</th>
					<th>Text</th>
					<th>Author</th>
				</tr>
				</thead>
				<tbody>
				{foreach from=$annotations item=ann}
				<tr class="an_row" label="an{$ann.id}">
					<td style="text-align: center">{$ann.id}</td>
					<td>{$ann.type}</td>
					<td>{$ann.text}</td>
					<td>{$ann.screename}</td>
				</tr>
				{foreachelse}
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
