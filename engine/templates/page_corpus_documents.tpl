{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}

{if $corpus.public || $user}
    <div class="row">
        <div class="col-md-10 scrollingWrapper" style="margin-bottom: 20px;">
            <div class="flexigrid ">
                <table id="table-documents">
                    <tr>
                        <td style="vertical-align: middle;">
                            <div>Loading ... <img style="vertical-align: baseline" title=""
                                                  src="gfx/flag_4.png"><input type="checkbox"></div>
                        </td>
                    </tr>
                </table>
                <script type="text/javascript">
                    var init_from = {$from};
                    var colModel = [
                            {literal}{
                            {/literal}display: "<input class='select_all' type='checkbox' name='select_action'>",
                            name: "checkbox_action",
                            width: 30,
                            align: 'center'{literal}}{/literal},
                        {foreach from=$columns item=c}
                            {if $c->isVisible() || $c->isPinned()}
                                {literal}{
                                {/literal}display: "{$c->getHeader()}",
                                name: "{$c->getKey()}",
                                width: {$c->getWidth()},
                                sortable: false,
                                align: '{$c->getAlign()}'{literal}}{/literal},
                            {/if}
                        {/foreach}
                    ];
                </script>
            </div>
        </div>
        <div class="col-md-2">
            <div class="scrollingWrapper panel-group" id="accordion">

                <div class="panel panel-info">
                    <div class="panel-heading" id="headingColumns">
                        <div class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseColumns">Table columns</a>
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
                                <div class="panel-footer">
                                    <input type="submit" class="btn btn-primary"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {if $filter_active|@count gt 0}
                    <div class="panel panel-info">
                        <div class="panel-heading" id="headingActive">
                            <div class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseActive">Active filters</a>
                            </div>
                        </div>
                        <div id="collapseActive" class="panel-collapse collapse in">
                            <div class="scrollingAccordion">
                                <div id="filter_menu_active" class="scrolling">
                                    {foreach from=$filter_active item=filter}
                                        <div class="filter_box">
                                            <div class="header">
                                                <a class="cancel" style="float: right" href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;{$filter->getKey()}=">cancel</a>
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

                <div class="panel panel-info">
                    <div class="panel-heading" id="headingAvailable">
                        <div class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseAvailable">Available filters</a>
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
                                                <i class="fa fa-circle-thin" aria-hidden="true" style="color: #eee"></i>
                                            {/if}

                                            <a href="#" class="toggle_simple" label="#filter_{$filter->getKey()}">
                                                <i class="fa fa-chevron-down" aria-hidden="true" style="float: right"></i>
                                                <span class="active">{$filter->getName()}</span>
                                            </a>
                                        </div>
                                        <div id="filter_{$filter->getKey()}" class="options" style="display: none">
                                            {include file=$filter->getTemplate()}
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-info" id="selection_menu">
                    <div class="panel-heading" id="headingBatch">
                        <div class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseBatch">
                                Batch operations
                            </a>
                        </div>
                    </div>
                    <div id="collapseBatch" class="panel-collapse collapse">
                        <div class="scrollingAccordion">
                            <div class="panel-body scrolling">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Selected documents
                                    </div>
                                    <div class="panel-body">
                                        <button id="show_selected" title="Display the list of all selected documents."
                                                class="btn btn-xs btn-warning" style="float: right">Show selected
                                        </button>
                                        <div id="cell_annotation_wait" style="display: none;">Loading data<img src="gfx/ajax.gif"/></div>
                                        <p id="selectedRows"></p>
                                    </div>
                                    <div class="panel-footer">
                                        <button id="select_everything" title="Select every document matching current filter."
                                                class="btn btn-xs btn-info">Select all
                                        </button>
                                        <button id="clear_all" title="Unselect all documents in this corpus."
                                                class="btn btn-xs btn-info">Clear all
                                        </button>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading">Operations</div>
                                    <div class="panel-body">
                                        <div class="panel panel-info">
                                            <div class="panel-heading"><a data-toggle="collapse" href="#collapseOperationFlag">Set flag status</a></div>
                                            <div class="panel-body panel-collapse collapse" id="collapseOperationFlag">
                                                <div class="form-group">
                                                    <label for="selected_flags">Flag name</label>
                                                    <select class="form-control" id="selected_flags">
                                                        {if empty($corpus_flag_ids)}
                                                            <option value="" disabled selected>-Flag-</option>
                                                        {/if}

                                                        <option value="" selected="selected">-Flag-</option>
                                                        {foreach from=$corpus_flag_ids  item="set"}
                                                            <option value="{$set.id}">{$set.name}</option>
                                                            </optgroup>
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
                                                            </optgroup>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="panel panel-info">
                                            <div class="panel-heading"><a data-toggle="collapse" href="#collapseOperationSubcorpus">Set subcorpus</a></div>
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
                                                            </optgroup>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer">
                                <button id="selection_action" class="btn btn-primary disabled" disabled>Submit</button>
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