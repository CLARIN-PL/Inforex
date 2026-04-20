{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}

{if $corpus.public || $user}
    <div class="container-fluid admin_tables corpus-documents-page">
    <div class="row corpus-documents-grid">
        <div class="col-md-10 scrollingWrapper corpus-documents-main-column">
            <div class="panel administration-content-panel corpus-documents-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
                    <span>Corpus documents</span>
                </div>
                <div class="panel-body">
            <div class="flexigrid corpus-documents-flexigrid">
                <table id="table-documents">
                    <tr>
                        <td style="vertical-align: middle;">
                            <div class="corpus-documents-loading">Loading ... <img title="" src="gfx/flag_4.png"><input type="checkbox"></div>
                        </td>
                    </tr>
                </table>
                <script type="text/javascript">
                    var init_from = {$from};
                    var colModel = [
                        {ldelim}
                            display: "<input class='select_all' type='checkbox' name='select_action'>",
                            name: "checkbox_action",
                            width: 30,
                            align: 'center'
			{rdelim},
                        {foreach from=$columns item=c}
                            {if $c->isVisible() || $c->isPinned()}
                                {ldelim}
                                display: "{$c->getHeader()}",
                                name: "{$c->getKey()}",
                                width: {$c->getWidth()},
                                sortable: false,
                                align: '{$c->getAlign()}'
				{rdelim},
                            {/if}
                        {/foreach}
                    ];
                </script>
            </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 corpus-documents-sidebar-column">
            <div class="scrollingWrapper panel-group corpus-documents-sidebar" id="accordion">

                <div class="panel corpus-documents-side-panel">
                    <div class="panel-heading corpus-documents-side-heading" id="headingColumns">
                        <div class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseColumns"><i class="fa fa-columns" aria-hidden="true"></i> Table columns</a>
                        </div>
                    </div>
                    <div id="collapseColumns" class="panel-collapse collapse">
                        <form action="index.php" method="get">
                            <input type="hidden" name="page" value="{$page}">
                            <input type="hidden" name="corpus" value="{$corpus.id}">
                            <div class="scrollingAccordion">
                                <div id="table-columns" class="panel-body scrolling">
                                    {foreach from=$columns item=c}
                                        <div class="checkbox">
                                            <label title="{$c->getDescription()}">
                                                <input type="checkbox" name="columns[]" value="{$c->getKey()}"{if $c->isVisible() || $c->isPinned()} checked{/if}{if $c->isPinned()} disabled="true"{/if}>
                                                <b>{$c->getHeader()}</b> {if $c->getName()} &ndash; {$c->getName()}{/if}
                                            </label>
                                        </div>
                                    {/foreach}
                                </div>
                                <div class="panel-footer corpus-documents-side-footer">
                                    <input type="submit" class="btn btn-primary"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {if $filter_active|@count gt 0}
                    <div class="panel corpus-documents-side-panel">
                        <div class="panel-heading corpus-documents-side-heading" id="headingActive">
                            <div class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseActive"><i class="fa fa-filter" aria-hidden="true"></i> Active filters ({$filter_active|@count})</a>
                            </div>
                        </div>
                        <div id="collapseActive" class="panel-collapse collapse in">
                            <div class="scrollingAccordion">
                                <div id="filter_menu_active" class="scrolling">
                                    {foreach from=$filter_active item=filter}
                                        <div class="filter_box">
                                            <div class="header">
                                                <a class="cancel" href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}=&amp;filter_order={$filter->getOrderCancel()}">cancel</a>
                                                <a href="#"><span class="active">{$filter->getName()}</span></a>
                                            </div>
                                            <div id="filter_{$filter->getKey()}" class="options">
                                                {include file=$filter->getTemplate()}
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <div class="panel corpus-documents-side-panel">
                    <div class="panel-heading corpus-documents-side-heading" id="headingAvailable">
                        <div class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseAvailable"><i class="fa fa-sliders" aria-hidden="true"></i> Available filters</a>
                        </div>
                    </div>
                    <div id="collapseAvailable" class="panel-collapse collapse {if empty($filter)} in{/if}">
                        <div class="scrollingAccordion">
                            <div id="filter_menu" class="scrolling">
                                {foreach from=$filter_notset item=filter}
                                    <div class="filter_box">
                                        <div class="header">
                                            {if $filter->getDescription() != ""}
                                                <i class="fa fa-info-circle" aria-hidden="true" title="{$filter->getDescription()}"></i>
                                            {else}
                                                <i class="fa fa-circle-thin corpus-documents-muted-icon" aria-hidden="true"></i>
                                            {/if}

                                            <a href="#" class="toggle_simple{if $filter->isLazyLoadable() && !$filter->hasItemsLoaded()} lazy_filter_toggle{/if}" label="#filter_{$filter->getKey()}">
                                                <i class="fa fa-chevron-down corpus-documents-filter-toggle-icon" aria-hidden="true"></i>
                                                <span class="active">{$filter->getName()}</span>
                                            </a>
                                        </div>
                                        <div id="filter_{$filter->getKey()}" class="options" style="display: none"
                                             data-filter-key="{$filter->getKey()}"
                                             data-filter-lazy="{if $filter->isLazyLoadable() && !$filter->hasItemsLoaded()}1{else}0{/if}"
                                             data-filter-loaded="{if $filter->hasItemsLoaded()}1{else}0{/if}">
                                            {include file=$filter->getTemplate()}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel corpus-documents-side-panel" id="selection_menu">
                    <div class="panel-heading corpus-documents-side-heading" id="headingBatch">
                        <div class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseBatch">
                                <i class="fa fa-tasks" aria-hidden="true"></i> Batch operations
                            </a>
                        </div>
                    </div>
                    <div id="collapseBatch" class="panel-collapse collapse">
                        <div class="scrollingAccordion">
                            <div class="panel-body scrolling corpus-documents-batch-body">
                                <div class="panel corpus-documents-batch-card">
                                    <div class="panel-heading corpus-documents-batch-card-heading">
                                        <b>Selected documents: </b>
                                        <span id="cell_annotation_wait" style="display: none;"><img src="gfx/ajax.gif"/></span>
                                        <span id="selectedRows"></span>
                                    </div>
                                    <div class="panel-footer corpus-documents-batch-actions">
                                        <button id="select_everything" title="Select every document matching current filter."
                                                class="btn btn-xs btn-info corpus-documents-secondary-button">Select all
                                        </button>
                                        <button id="clear_all" title="Unselect all documents in this corpus."
                                                class="btn btn-xs btn-info corpus-documents-secondary-button">Clear all
                                        </button>
                                    </div>
                                </div>

                                <div class="panel corpus-documents-batch-card">
                                    <div class="panel-heading corpus-documents-batch-card-heading">Operations</div>
                                    <div class="panel-body">
                                        <div class="panel corpus-documents-operation-panel">
                                            <div class="panel-heading corpus-documents-operation-heading"><a data-toggle="collapse" href="#collapseOperationFlag">Set flag status</a></div>
                                            <div class="panel-body panel-collapse collapse" id="collapseOperationFlag">
                                                <div class="form-group">
                                                    <label for="selected_flags">Flag name</label>
                                                    <select class="form-control" id="selected_flags">
                                                        {if empty($corpus_flag_ids)}
                                                            <option value="" disabled selected>-Flag-</option>
                                                        {/if}

                                                        <option value="" selected="selected">-Flag-</option>
                                                        {foreach from=$corpus_flag_ids  item="set"}
                                                            <option value="{$set.corpora_flag_id}">{$set.name}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="selected_action">Change flag value to</label>
                                                    <select id="selected_action" class="form-control" name="selected_flags">
                                                        {if empty($available_flags)}
                                                            <option value="" disabled selected>-Status-</option>
                                                        {/if}
                                                        <option value="" selected="selected">-Status-</option>
                                                        {foreach from=$available_flags  item="set"}
                                                            <option value="{$set.flag_id}">{$set.name}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel corpus-documents-operation-panel">
                                            <div class="panel-heading corpus-documents-operation-heading"><a data-toggle="collapse" href="#collapseOperationSubcorpus">Set subcorpus</a></div>
                                            <div class="panel-body panel-collapse collapse" id="collapseOperationSubcorpus">
                                                <div class="form-group">
                                                    <label for="selected_subcorpus">Change subcorpus to</label>
                                                    <select id="selected_subcorpus" class="form-control" name="selected_subcorpora">
                                                        {if empty($subcorpora)}
                                                            <option value="" disabled selected>-Subcorpus-</option>
                                                        {/if}
                                                        <option value="-1" selected="selected">-Subcorpus-</option>
                                                        {foreach from=$subcorpora  item="set"}
                                                            <option value="{$set.subcorpus_id}">{$set.name}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel corpus-documents-operation-panel">
                                            <div class="panel-heading corpus-documents-operation-heading"><a data-toggle="collapse" href="#collapseOperationDeletion">Delete</a></div>
                                            <div class="panel-body panel-collapse collapse" id="collapseOperationDeletion">
                                                <div class="form-group">
                                                    <div class="alert alert-danger" role="alert">
                                                    <label title="">
                                                        <input type="checkbox" id="selected_deletion" name="deletion"/>
                                                        Delete selected documents
                                                    </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer corpus-documents-side-footer">
                                <button id="selection_action" class="btn btn-primary disabled" disabled>Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
{else}
    {include file="inc_no_access.tpl"}
{/if}
{include file="inc_footer.tpl"}
