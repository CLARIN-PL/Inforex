{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="dialog" title="Błąd" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
		<span class="message"></span>
	</p>
	<p><i><a href="">Refresh page.</a></i></p>
</div>
 
<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="content">
						<div id="leftContent" style="width: 100%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
							{if $content_inline|strpos:"sentence"}
						    	<div style="margin: 5px" class="contentBox">{$content_inline}</div>
						    {else}
						    	<div style="margin: 5px" class="contentBox">Content has to be split into sentences before using this perspective</div>
					      	{/if}
						</div>
					</div>
				</div>
			</div>
		</td>
		<td style="width: 330px; vertical-align: top; overflow: none; ">
			<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">
		 		{include file="inc_report_annotation_lemma_config.tpl"}
			</div>
		</td>
	</tr>
</table>

