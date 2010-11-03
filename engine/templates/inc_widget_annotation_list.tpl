<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
	<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Lista adnotacji:</div>
	<div style="padding: 2px;">
		<div class="scrolling" style="overflow: auto">				
			<table id="annotations" class="tablesorter" cellspacing="1">
				<thead>
				<tr>
					<th style="width: 25px">Id</td>
					<th>Typ</th>
					<th>Tekst</th>
					<th>Autor</th>
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
