<table style="width: 100%">
	<tr>
		<td style="width: 40%; vertical-align: top">
			<div class="column""
				<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Scans:</div>	
					<div class="scrolling">
						{foreach from=$images item=image}
							<b>{$image.original_name}</b><br/>
							<img src="image.php?id={$image.image_id}" style="width: 100%"/><hr/>
						{/foreach}
					</div>
				</div>
			</div>
		</td>
		<td style="vertical-align: top">
			<div class="ui-widget ui-widget-content ui-corner-all" style="margin: 5px">			
			<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Transcription:</div>
				<div class="markitup">	
					<textarea id="report_content" class="scrolling">{$row.content}</textarea>
				</div>
			</div>
		</td>
	</tr>
</table>

