{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
<div class = "col-md-2 scrollingWrapper">
    <div class = "panel panel-primary">
        <div class = "panel-heading">View configuration</div>
        <div class = "panel-body scrolling">
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
<div id="col-content" class="col-main {if $flags_active}col-md-6{else}col-md-7{/if} scrollingWrapper">
    <div class="panel panel-primary">
        <div class="panel-heading">Document content</div>
        <div class="panel-body" style="padding: 0">
            <div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
                <div style="margin: 5px" class="contentBox {$report.format}">{$content|format_annotations}</div>
            </div>
        </div>
    </div>
</div>
<div class = "col-md-3 scrollingWrapper">
    <div class = "panel panel-primary">
        <div class = "panel-heading">Import annotations</div>
        <div class = "panel-body scrolling">
            <div class = "panel panel-default">
                <div class = "panel-heading">From CCL file</div>
                <div class = "panel-body">
                    <form id = "import_from_ccl_form" method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=importannotations&amp;id={$report_id}" enctype="multipart/form-data">
                        <div class = "form-group">
                            <label>Select CCL file</label>
                            <input id = "cclFile" class="btn btn-default" type="file" name="cclFile" />
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
                        <hr>
                        <div class = "form-group">
                            <label>Options</label>
                            <div class="checkbox">
                                <label><input type="checkbox" name = "ignore_duplicates" value="ignore_duplicates">Ignore duplicated annotations</label>
                            </div>
                            <div class="checkbox">
                                <label><input type="checkbox" name = "ignore_unknown_types" value="ignore_unknown_types">Ignore unknown annotations types</label>
                            </div>
                        </div>

                        <div class = "form-group">
                            <input id = "import_annotations_btn" class="btn btn-primary" type="submit" value="Submit"/>
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