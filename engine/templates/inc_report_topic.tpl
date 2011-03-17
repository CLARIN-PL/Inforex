<h1>{$row.title}</h1>
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
		<td style="vertical-align: top; width: 400px;">
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">			
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Topic:</div>
					<ul class="topics">
					{foreach from=$topics item=topic}					
						<li><a href="#" id="topic_{$topic.id}"{if $row.type==$topic.id} class="marked"{/if}>{$topic.name}</a></li>
					{/foreach}
					</ul>
				</div>
			</div>
		</td>
	</tr>
</table>
<input type="hidden" id="report_id" value="{$row.id}"/>