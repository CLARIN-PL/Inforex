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
		<td style="vertical-align: top; width: 450px;">
		    <div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">List of anaphora relations:</div>
		    <div style="padding: 2px;" class="annotations">
				<table class="tablesorter" cellspacing="1" style="width: 100%">
				    <thead>
				        <tr>
				            <th>Source</th>
				            <th style="width: 200px">Relation</th>
				            <th>Target</th>
				        </tr>
				    </thead>
				    <tbody>
				    {foreach from=$relations item=item}
                        <tr>
                            <td style="vertical-align: middle"><span class="{$item.ans_type}">{$item.ans_text}</span></td>
                            <td style="vertical-align: middle"><small>{$item.relation_name}</small></td>
                            <td style="vertical-align: middle"><span class="{$item.ant_type}">{$item.ant_text}</span></td>
                        </tr>
                    {/foreach}
				    </tbody>
				</table>
			</div>
		</td>
	</tr>
</table>
<input type="hidden" id="report_id" value="{$row.id}"/>