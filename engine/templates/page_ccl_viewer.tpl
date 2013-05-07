{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header.tpl"}
{include file="inc_system_messages.tpl"}
<h1>CCL Viewier</h1>

<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content:</div>
					<div id="content">
						<div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
						      <div style="margin: 5px" class="contentBox">{$content_inline|format_annotations}</div>
						</div>
						<div id="rightContent" style="{if !$showRight}display: none{/if};" class="annotations scrolling content rightPanel">
						      <div style="margin: 5px" class="contentBox">{$content_inline2|format_annotations}</div>
						</div>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>
		</td>
		<td style="width: 300px; vertical-align: top; overflow: auto; ">
			<div id="cell_annotation_wait" style="display: none;">
				Loading data
				<img src="gfx/ajax.gif" />
			</div>
			<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">
		 		
		 		{include file="inc_upload_document.tpl"}
		 		
		 		{include file="inc_report_annotator_configuration.tpl"}
				
                {include file="inc_report_annotator_annotations.tpl"}
                
                {include file="inc_report_annotator_relations.tpl"}		 		
				
				<h3 style="display:none"><a>Tmp</a></h3>
				<div style="display:none">
					Tmp
				</div>				
			</div>
		</td>
	</tr>
</table>

{include file="inc_footer.tpl"}