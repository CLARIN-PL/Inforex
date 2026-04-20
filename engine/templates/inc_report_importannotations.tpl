{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
<div id="col-content" class="col-main {if $flags_active}col-md-6{else}col-md-7{/if} scrollingWrapper report-preview-content-column report-importannotations-content-column">
    <div class="panel panel-primary administration-content-panel report-preview-content-panel report-importannotations-content-panel">
        <div class="panel-heading administration-content-heading report-preview-panel-heading report-importannotations-heading">
            <span class="administration-content-heading-icon report-preview-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
            <span>Document content</span>
        </div>
        <div class="panel-body report-preview-content-body report-importannotations-content-body">
            <div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if};" class="annotations scrolling content report-preview-document-content report-importannotations-document-content">
                <div class="contentBox {$report.format} report-preview-content-box report-importannotations-content-box">{$content|format_annotations}</div>
            </div>
        </div>
    </div>
</div>
<div id="col-config" class="col-md-2 scrollingWrapper report-importannotations-config-column report-preview-config-column">
    <div class="panel panel-primary administration-content-panel report-importannotations-panel">
        <div class="panel-heading administration-content-heading report-importannotations-heading">
            <span class="administration-content-heading-icon report-importannotations-heading-icon"><i class="fa fa-cog" aria-hidden="true"></i></span>
            <span>View configuration</span>
        </div>
        <div class="panel-body scrolling report-importannotations-config-body">
            <div class = "form-group">
                <label for = "annotation_set">Annotation set</label>
                <select name = "annotation_set" id = "view_annotation_set" class = "form-control import_anns_conf">
                    <option>-</option>
                    {foreach from = $annotation_sets item = annotation_set}
                        <option {if $annotation_set.annotation_set_id == $selected_set} selected {/if} value = {$annotation_set.annotation_set_id}>{$annotation_set.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class = "form-group">
                <label for = "annotation_stage">Stage</label>
                <select name = "annotation_stage" id = "view_annotation_stage" class = "form-control import_anns_conf">
                    {foreach from = $stages item = id key = stage}
                        <option {if $id == $selected_stage} selected {/if}  value = "{$id}">{$stage}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
</div>
<div class="col-md-3 scrollingWrapper report-importannotations-import-column">
    <div class="panel panel-primary administration-content-panel report-importannotations-panel">
        <div class="panel-heading administration-content-heading report-importannotations-heading">
            <span class="administration-content-heading-icon report-importannotations-heading-icon"><i class="fa fa-upload" aria-hidden="true"></i></span>
            <span>Import annotations</span>
        </div>
        <div class="panel-body scrolling report-importannotations-import-body">
            <div class="report-importannotations-card">
                <div class="report-importannotations-card-heading">
                    <i class="fa fa-file-code-o" aria-hidden="true"></i>
                    <span>From CCL file</span>
                </div>
                <div class="report-importannotations-card-body">
                    <form id="import_from_ccl_form" method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage={$subpage}&amp;id={$report_id}" enctype="multipart/form-data">
                        <div class="form-group report-importannotations-file-group">
                            <label>Select CCL file</label>
                            <input id="cclFile" class="form-control report-importannotations-file-input" type="file" name="cclFile" />
                            <input type="hidden" name="action" value="import_annotations_ccl"/>
                            <input type="hidden" id="report_id" value="{$row.id}"/>
                        </div>
                        <div class = "form-group">
                            <label for = "annotation_set">Annotation set</label>
                            <select name = "annotation_set" id = "annotation_set" class = "form-control">
                                <option value = "-1">-</option>
                                {foreach from = $annotation_sets item = annotation_set}
                                    <option value = {$annotation_set.annotation_set_id}>{$annotation_set.name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class = "form-group">
                            <label for = "annotation_stage">Stage</label>
                            <select name = "annotation_stage" id = "annotation_stage" class = "form-control">
                                <option value = "new">New</option>
                                <option value = "final">Final</option>
                                <option value = "agreement">Agreement</option>
                            </select>
                        </div>
                        <div class = "form-group">
                            <label for = "annotation_source">Source</label>
                            <select name = "annotation_source" id = "annotation_source" class = "form-control">
                                <option value = "user">User</option>
                                <option value = "bootstrapping">Bootstraping</option>
                                <option value = "auto">Auto</option>
                            </select>
                        </div>
                        <div class = "form-group" id="annotation_user_form_group">
                            <label for = "annotation_user_id">User</label>
                            <select name = "annotation_user_id" id = "annotation_user_id" class = "form-control">
                                <option value="{$logged_user.user_id}" selected>{$logged_user.screename}</option>
                                {foreach from=$users item=user}
                                    <option value ="{$user.user_id}">{$user.screename}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="report-importannotations-options">
                            <div class="report-importannotations-options-title">Options</div>
                            <div class="checkbox report-importannotations-checkbox">
                                <label><input type="checkbox" name="ignore_duplicates" value="ignore_duplicates"> Ignore duplicated annotations</label>
                            </div>
                            <div class="checkbox report-importannotations-checkbox">
                                <label><input type="checkbox" name="ignore_unknown_types" value="ignore_unknown_types"> Ignore unknown annotations types</label>
                            </div>
                        </div>

                        <div class="form-group report-importannotations-actions">
                            <button id="import_annotations_btn" class="btn btn-primary report-importannotations-submit" type="submit">
                                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                <span>Import</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade in" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Error</h4>
            </div>
            <div class="modal-body">
                test
            </div>
        </div>
    </div>
</div>
