<table style="width: 100%">
	<tr>
		<td style="vertical-align: top">
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">			
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Treść raportu:</div>
					<div id="content" style="padding: 5px; white-space: pre-wrap" class="annotations scrolling">{$content_inline|format_annotations}</div>
				</div>
			</div>
		</td>
		<td style="vertical-align: top; width: 400px;">
			<div class="column" id="widget_annotation">
			{include file="inc_widget_annotation_list.tpl"}
			</div>
		</td>
	</tr>
</table>
<input type="hidden" id="report_id" value="{$row.id}"/>