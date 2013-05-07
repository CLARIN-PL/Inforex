{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{if $exceptions|@count > 0}

<div class="infobox-light">The document could not be displayed due to structure errors.</div>

{else}

<table style="width: 100%; margin-top: 5px;" class="scrolling-pane">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="edit_content">
						<div id="leftContent" style="float:left; width: 50%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
						      <div style="margin: 5px" class="contentBox">{$content_html|format_annotations}</div>
						</div>
						
						<div id="rightContent" class="annotations scrolling content rightPanel">
                            <textarea name="content" id="report_content">{$content_source|escape}</textarea>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
	</tr>
</table>

{/if}