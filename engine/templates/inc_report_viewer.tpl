<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="edit_content">
						<div id="leftContent" style="float:left; width: 50%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
						      <div style="margin: 5px" class="contentBox">{$content_inline|format_annotations}</div>
						</div>
						
						<div id="rightContent" class="annotations scrolling content rightPanel">
                            <textarea name="content" id="report_content">{$content_inline|escape}</textarea>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>