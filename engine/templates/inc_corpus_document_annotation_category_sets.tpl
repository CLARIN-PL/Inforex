{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div class="container-fluid admin_tables corpus-settings-annotation-sets">
    <div class="row corpus-settings-annotation-sets-grid">
        <div class="col-md-10 col-md-offset-1 corpus-settings-annotation-sets-column">
            <form method="POST" class="panel administration-content-panel corpus-settings-annotation-sets-panel document-annotation-category-sets-form">
                <input type="hidden" name="action" value="document_annotation_category_sets_save"/>
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
                    <span>Document annotation category sets</span>
                </div>
                <div class="panel-body">
                    <p>Select which annotation sets should be available in the <strong>Document annotation categories</strong> report perspective.</p>
                    {if !$documentAnnotationCategoryPerspectiveEnabled}
                        <div class="alert alert-warning">
                            The <strong>Document annotation categories</strong> report perspective is not active for this corpus yet.
                            Enable it first in <a href="index.php?page=corpus_settings&amp;corpus={$corpus.id}&amp;subpage=perspectives">Perspectives</a>.
                        </div>
                    {/if}
                    <div class="administration-table-wrapper corpus-settings-annotation-sets-table-wrapper">
                        <table class="table table-striped table-hover administration-table corpus-settings-annotation-sets-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Use in perspective</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$documentAnnotationCategoryPerspectiveSets item=set}
                                <tr>
                                    <td class="corpus-settings-annotation-set-id">{$set.annotation_set_id}</td>
                                    <td><span class="corpus-settings-annotation-set-name">{$set.name}</span></td>
                                    <td>{$set.description}</td>
                                    <td class="corpus-settings-annotation-set-use-cell {if $set.selected_for_perspective}corpus-settings-annotation-set-use-cell-active{/if}">
                                        <label class="corpus-settings-annotation-set-checkbox" title="Use annotation set in document annotation categories">
                                            <input class="documentAnnotationPerspectiveSet" type="checkbox" name="annotation_set_ids[]" value="{$set.annotation_set_id}" {if $set.selected_for_perspective}checked="checked"{/if}/>
                                            <span aria-hidden="true"></span>
                                            <span class="sr-only">Use annotation set in document annotation categories</span>
                                        </label>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer">
                    <button type="submit" class="btn btn-primary document-annotation-category-sets-save" disabled="disabled">
                        <i class="fa fa-save" aria-hidden="true"></i> Save configuration
                    </button>
                    <span class="document-annotation-category-sets-status text-muted" style="margin-left: 10px;">No unsaved changes</span>
                </div>
            </form>
        </div>
    </div>
</div>
