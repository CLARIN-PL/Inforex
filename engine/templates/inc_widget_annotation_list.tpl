			<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Lista adnotacji:</div>
				<div style="padding: 2px;">				
					<table id="annotations" class="tablesorter" cellspacing="1">
						<thead>
						<tr>
							<th style="width: 25px">id</td>
							<th>typ</th>
							<th>tekst</th>
						</tr>
						</thead>
						<tbody>
						{foreach from=$annotations item=ann}
						<tr class="an_row" label="an{$ann.id}">
							<td style="text-align: center">{$ann.id}</td>
							<td>{$ann.type}</td>
							<td>{$ann.text}</td>
						</tr>
						{foreachelse}
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>
