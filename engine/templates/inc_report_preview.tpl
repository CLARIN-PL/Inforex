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
 
<div id="col-content" class="col-md-8 scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="padding: 0">
			<div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
				  <div style="margin: 5px" class="contentBox {$report.format}">{$content_inline|format_annotations}</div>
			</div>
			<div id="rightContent" style="{if !$showRight}display: none{/if};" class="annotations scrolling content rightPanel">
				  <div style="margin: 5px" class="contentBox">{$content_inline2|format_annotations}</div>
			</div>
			<div style="clear:both"></div>
		</div>
	</div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper">
	<div id="cell_annotation_wait" style="display: none;">
		Trwa wczytywanie danych
		<img src="gfx/ajax.gif" />
	</div>
	<div class="panel-group" id="accordion"  role="tablist" aria-multiselectable="true">
		{include file="inc_report_annotator_configuration.tpl" show=true}
		{include file="inc_report_annotator_annotations.tpl"}
		{include file="inc_report_annotator_relations.tpl"}
	</div>
</div>
