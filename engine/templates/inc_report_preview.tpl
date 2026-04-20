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

<div id="col-content" class="col-main {if $flags_active}col-md-8{else}col-md-9{/if} scrollingWrapper report-preview-content-column">
    <div class="panel panel-primary administration-content-panel report-preview-content-panel">
        <div class="panel-heading administration-content-heading report-preview-panel-heading">
            <span class="administration-content-heading-icon report-preview-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
            <span>Document content</span>
        </div>
        <div class="panel-body report-preview-content-body">
            <div id="leftContent" style="width: {if $showRight}50%{else}100%{/if};" class="annotations scrolling content report-preview-document-content">
                  <div class="contentBox {$report.format} report-preview-content-box">{$content|format_annotations}</div>
            </div>
        </div>
    </div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper report-preview-config-column">
    <div id="cell_annotation_wait" class="report-preview-loading" style="display: none;">
        <span>Trwa wczytywanie danych</span>
        <img src="gfx/ajax.gif" />
    </div>
    <div class="panel-group report-preview-accordion" id="accordion" role="tablist" aria-multiselectable="true">
        {include file="inc_report_annotator_configuration.tpl"}
        {include file="inc_report_annotator_annotations.tpl"}
        {include file="inc_report_annotator_relations.tpl"}
    </div>
</div>
