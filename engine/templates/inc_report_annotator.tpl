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

<div class="col-main {if $flags_active}col-md-7{else}col-md-8{/if} scrollingWrapper report-preview-content-column report-annotator-content-column" id="col-main">
	<div class="panel panel-primary administration-content-panel report-preview-content-panel report-annotator-content-panel">
		<div class="panel-heading administration-content-heading report-preview-panel-heading report-annotator-heading">
			<span class="administration-content-heading-icon report-preview-heading-icon report-annotator-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div id="widget_text" class="panel-body column report-annotator-content-body">
			<div id="content">
				<div id="leftContent" style="width: {if $showRight}50%{else}100%{/if};" class="annotations scrolling content report-preview-document-content report-annotator-document-content">
				  <div class="contentBox {$report.format} report-preview-content-box report-annotator-content-box">{$content|format_annotations}</div>
				</div>{*
				<div id="rightContent" style="{if !$showRight}display: none{/if};" class="annotations scrolling content rightPanel">
					  <div style="margin: 5px" class="contentBox {$report.format}">{$content_inline2|format_annotations}</div>
				</div>*}
				<div style="clear:both"></div>
			</div>
		</div>
	</div>
</div>

<div id="columnAnnotation" class="col-md-4 scrollingWrapper report-annotator-details-column" style="display: none;">
	<div class="">
		<div id="annotationLoading" class="administration-wsd-loading report-annotator-loading" style="display: none;">
			<img src="gfx/ajax.gif" alt="Loading"/>
			<span>Loading annotation data...</span>
		</div>


		<div id="annotationEditor">
			<div id="annotation-details" class="panel panel-primary administration-content-panel report-annotator-details-panel">
				<div class="panel-heading administration-content-heading report-annotator-details-heading">
					<span class="administration-content-heading-icon report-annotator-heading-icon"><i class="fa fa-tag" aria-hidden="true"></i></span>
					<span>Annotation details</span>
					<a href="#" class="btn btn-xs btn-primary annotation_redo report-annotator-close-button" title="Close annotation editor"><i class="fa fa-window-close" aria-hidden="true"></i></a>
				</div>
				<div class="panel-body report-annotator-details-body">
					<table class="table table-striped report-annotator-details-table" cellspacing="1">
						<tr>
							<th>Id:</th>
							<td class="value" id="annotation_id">-</td>
						</tr>
						<tr>
							<th>Text:</th>
							<td class="value" id="annotation_text">-</td>
						</tr>
						<tr>
							<th>Type:</th>
							<td style="vertical-align: top">
								<span id="annotation_redo_type" class="value" annotation-type-id=""></span>
								<input type="hidden" id="annotation_redo_type_id"/>
                                <div class="report-annotator-type-change" {if $annotation_mode == 'relation_agreement'}style="display: none;"{/if}><a href="#" id="changeAnnotationType" data-toggle="popover" title="change type"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></div>
                            </td>
						</tr>
						<tr>
							<th>Lemma:</th>
							<td style="vertical-align: middle">
								<input type="text" id="annotation_lemma" class="form-control" value=""/>
							</td>
						</tr>
					</table>
				</div>
				<div class="panel-footer report-annotator-footer">
					<input type="button" value="Save and close" class="btn btn-sm btn-primary report-annotator-save-button" id="annotation_save" disabled="true"/>
                    {if $annotation_mode != 'relation_agreement'}
						<a href="#" type="button" id="annotation_delete" class="btn btn-sm btn-danger report-annotator-delete-button" title="Delete annotation"><i class="fa fa-trash" aria-hidden="true"></i></a>
                    {/if}
				</div>
			</div>

			<div id="annotation-relations" class="panel panel-default report-annotator-relations-panel">
				<div class="panel-heading report-annotator-card-heading"><i class="fa fa-link" aria-hidden="true"></i> Annotation relations</div>
				<div class="panel-body report-annotator-relations-body">
					<div class="annotations relationsContainer scrolling">
						<table class="table table-striped relations report-annotator-relations-table" cellspacing="1">
							<thead>
								<tr>
									<th>Id</th>
									<th>Relation type</th>
									<th>Target annotation</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer report-annotator-footer">
					<input type="button" value="Cancel" class="btn btn-sm btn-warning relation-cancel" style="display: none"/>
					<div class="dropup relation-types">
						<button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Add relation
							<span class="caret"></span></button>
						<ul class="dropdown-menu"></ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div id="col-config" class="col-md-4 scrollingWrapper report-preview-config-column report-annotator-config-column">
	<div class="panel-group report-preview-accordion report-annotator-accordion" id="accordion" role="tablist" aria-multiselectable="true">
		{include file="inc_report_annotator_configuration.tpl" show=true}
        {include file="inc_report_annotator_annotation_pad.tpl"}
		{include file="inc_report_annotator_annotations.tpl"}
		{include file="inc_report_annotator_relations.tpl"}
	</div>
</div>
