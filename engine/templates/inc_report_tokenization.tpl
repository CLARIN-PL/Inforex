<table style="width: 100%">
	<tr>
		<td style="vertical-align: top">
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">			
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Content</div>
					<div id="content" style="padding: 5px;" class="annotations scrolling">{$content_inline|format_annotations}</div>
					<pre>
					<div id="tmp">
						
					</div>
					</pre>
				</div>
			</div>
		</td>
		<td style="vertical-align: top; width: 400px;">
			<div class="column" id="widget_annotation">
				<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Load tokenization</div>
					<div style="padding: 2px;">
						<div class="scrolling" style="overflow: auto">				
						      <h1>From XCES file</h1>
							<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=tokenization&amp;id={$report_id}" enctype="multipart/form-data">
							     Select and upload XCES file:
								<input type="file" name="xcesFile" />
								<input type="hidden" name="action" value="report_set_tokens"/>
								<input type="hidden" id="report_id" value="{$row.id}"/>
								<input type="submit" value="Submit"/>
							</form>
							<h1>Using TaKIPI Web Service</h1>
							<button id="takipiwsProcess">Run TaKIPI-WS</button>
							<div id="messageBox">
								{if $message}
									{$message}
								{/if}
							</div>
						</div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>


