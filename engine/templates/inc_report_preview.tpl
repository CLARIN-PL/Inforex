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
			<div class="ui-widget ui-widget-content ui-corner-all" style="background: PeachPuff">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Layers:</div>
				<div style="padding: 2px;">
					<div id="layersList" class="scrolling" style="overflow: auto">	
						<table id="annotations" class="tablesorter" cellspacing="1">
							<thead>
							<tr>
								<th>Name</th>
							</tr>
							</thead>
							<tbody>
							{foreach from=$layers item=layer}
							<tr>
								<td>
								   {if $previewLayer==$layer.annotation_set_id}
								       <em>{$layer.description}</em>
								   {else}
								       <a href="index.php?page=report&amp;subpage=preview&amp;corpus={$corpus.id}&amp;id={$row.id}&amp;previewLayer={$layer.annotation_set_id}">{$layer.description}</a>
								   {/if}
								</td>
							</tr>
							{/foreach} 
							</tbody>
						</table>
					</div>
				</div>
			</div>
		
			<div class="column scrolling" id="widget_annotation">
			{include file="inc_widget_annotation_list.tpl"}
			</div>
		</td>
	</tr>
</table>
<input type="hidden" id="report_id" value="{$row.id}"/>