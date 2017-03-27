{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_system_messages.tpl"}

<div class="row">
	<div class="col-md-8 scrollingWrapper">
		<div class="panel panel-primary">
			<div class="panel-heading">Document content</div>
			<div class="panel-body">
				<div class="column" id="widget_text">
					<div id="leftContent" style="float:left; width: {if $showRight}50%; border-right: 1px solid #E0CFC2;{else}100%;{/if}" class="annotations scrolling content">
						  <div style="margin: 5px" class="contentBox">{$content_inline|format_annotations}</div>
					</div>
					<div id="rightContent" style="{if !$showRight}display: none{/if};" class="annotations scrolling content rightPanel">
						  <div style="margin: 5px" class="contentBox">{$content_inline2|format_annotations}</div>
					</div>
					<div style="clear:both"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4 scrollingWrapper">
		<div class="panel-group" id="accordion"  role="tablist" aria-multiselectable="false">
			{include file="inc_upload_document.tpl"}
			{include file="inc_report_annotator_configuration.tpl" show=false}
			{include file="inc_report_annotator_annotations.tpl"}
			{include file="inc_report_annotator_relations.tpl"}
		</div>
	</div>

</div>

{include file="inc_footer.tpl"}