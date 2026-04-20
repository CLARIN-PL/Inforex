{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
<div class="container-fluid admin_tables public-annotations-page administration-schema">
    <div class="row administration-schema-grid public-annotations-grid">
        <div class="col-md-5 tableContainer administration-schema-column public-annotations-column" id="annotationSetsContainer">
            <div class="panel scrollingWrapper administration-content-panel administration-schema-panel public-annotations-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
                    <span>Annotation sets</span>
                </div>
                <div class="tableContent panel-body scrolling">
                    <table class="table table-striped administration-table administration-schema-table public-annotations-table public-annotation-sets-table" id="annotationSetsTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="td-center">Owner</th>
                            <th class="td-center" colspan="2">Public corpora</th>
                            <th class="td-center">All corpora</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$annotationSets item=set}
                            {if $set.public == 1}
                                <tr id="{$set.id}">
                                    <td class="public-annotation-name">{$set.name}</td>
                                    <td>
                                        <div class="annotation_description administration-description-preview" title="{$set.description|escape}">{$set.description}</div>
                                    </td>
                                    <td class="td-center">
                                        <span class="administration-owner-initials" title="{$set.screename|escape}">{$set.owner_initials|escape}</span>
                                    </td>
                                    <td class="td-right">
                                        <span class="public-annotations-count-badge">{$set.count_public}</span>
                                    </td>
                                    <td>
                                        {if $set.count_public > 0}
                                            <a title="Show a list of public corpora using this annotation set." href="#" class="show_public public-corpora-button">
                                                <i class="fa fa-list-ul" aria-hidden="true"></i>
                                                <span class="sr-only">Show public corpora</span>
                                            </a>
                                        {/if}
                                    </td>
                                    <td class="td-center"><span class="public-annotations-count-badge public-annotations-count-badge-muted">{$set.count_ann}</span></td>
                                </tr>
                            {/if}
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3 tableContainer administration-schema-column public-annotations-column" id="annotationSubsetsContainer" style="visibility: hidden;">
            <div class="panel scrollingWrapper administration-content-panel administration-schema-panel public-annotations-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
                    <span>Annotation subsets</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationSubsetsTable" class="table table-striped administration-table administration-schema-table public-annotations-table public-annotation-subsets-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 tableContainer administration-schema-column public-annotations-column" id="annotationTypesContainer" style="visibility: hidden;">
            <div class="panel scrollingWrapper administration-content-panel administration-schema-panel public-annotations-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-bookmark" aria-hidden="true"></i></span>
                    <span>Categories</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationTypesTable" class="table table-striped administration-table administration-schema-table public-annotations-table public-annotation-types-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th>Symbolic name</th>
                                <th>Description</th>
                                <th class="td-center">Used</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade settingsModal administration-form-modal public-annotations-modal" id="browse_public_corpora_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-database" aria-hidden="true"></i> Public corpora</h4>
                </div>
                <div class="modal-body">
                    <div class="administration-table-wrapper public-annotations-modal-table">
                        <table class="table table-striped administration-table public-annotations-table" cellspacing="1">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th class="td-center">Uses</th>
                                </tr>
                            </thead>
                            <tbody id="public_corpora_table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
