<table style="width: 100%">
<tr>
	<td style="vertical-align: top">
		<div class="ui-widget ui-widget-content ui-corner-all">			
			<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Treść raportu:</div>
			<div id="content" style="padding: 5px;">
			</div>
		</div>	
	</td>
	<td style="width: 300px; vertical-align: top">
		<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Szczegóły tokenu:</div>
		<div id="token" style="padding: 5px;">
			<code></code>			
		</div>		
	</td>
</tr>
</table>

<input type="hidden" id="report_content" value="{$row.content}"/>
<input type="hidden" id="report_id" value="{$row.id}"/>