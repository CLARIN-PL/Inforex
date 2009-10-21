<table style="width: 100%">
	<tr>
		<td style="vertical-align: top">
			<div class="ui-widget ui-widget-content ui-corner-all">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Treść raportu:</div>
				<div id="content" style="padding: 5px;">
					{$row.content|format_annotations}
				</div>
			</div>
		</td>
		<td style="vertical-align: top; width: 300px;">
			{include file="inc_widget_annotation_list.tpl"}
		</td>
	</tr>
</table>

<input type="hidden" id="report_id" value="{$row.id}"/>