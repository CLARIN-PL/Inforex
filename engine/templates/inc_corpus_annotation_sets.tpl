{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables corpus-settings-annotation-sets">
    <div class="row corpus-settings-annotation-sets-grid">
        <div class="col-md-10 col-md-offset-1 corpus-settings-annotation-sets-column">
        <div class="panel administration-content-panel corpus-settings-annotation-sets-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
                <span>Annotation sets</span>
            </div>
            <div class="panel-body">
                <div class="administration-table-wrapper corpus-settings-annotation-sets-table-wrapper">
                <table class="table table-striped table-hover administration-table corpus-settings-annotation-sets-table" id="corpus_set_annotation_sets_corpora" cellspacing="1">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th title="Number of annotations in the corpus">Count</th>
                        <th>Use</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$annotationsList item=set}
                        <tr>
                            <td class="corpus-settings-annotation-set-id">{$set.id}</td>
                            <td><span class="corpus-settings-annotation-set-name">{$set.name}</span></td>
                            <td><span class="corpus-settings-annotation-set-count">{$set.count_ann}</span></td>
                            <td class="corpus-settings-annotation-set-use-cell {if $set.cid}corpus-settings-annotation-set-use-cell-active{/if}">
                                <label class="corpus-settings-annotation-set-checkbox" title="Use annotation set">
                                    <input class="annotationSet" type="checkbox" annotation_set_id="{$set.id}" {if $set.cid} checked="checked" {/if}/>
                                    <span aria-hidden="true"></span>
                                    <span class="sr-only">Use annotation set</span>
                                </label>
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
