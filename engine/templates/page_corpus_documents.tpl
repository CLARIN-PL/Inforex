{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}

<div style="margin: 5px;">
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
                            {foreach from=$columns item=c key=k}
                            {if preg_match("/^flag/",$k)}
                                {literal}{
                                {/literal}display: "{$c.short}",
                                name: "{$k|lower}",
                                width: 40,
                                sortable: true,
                                align: 'center'{literal}}{/literal},
                            {elseif preg_match("/found_base_form/", $k)}
                                {literal}{
                                {/literal}display: "{$c}",
                                name: "{$k|lower}",
                                width: 200,
                                sortable: true,
                                align: 'center'{literal}}{/literal},
                            {elseif $c=="Subcorpus"}
                                {literal}{
                                {/literal}display: "{$c}",
                                name: "{$k|lower}",
                                width: 100,
                                sortable: true,
                                align: 'left'{literal}}{/literal},
                            {elseif $c=="checkbox"}
                                {literal}{
                                {/literal}display: "<input class = 'select_all' type='checkbox' name='select_action'>",
                                name: "{$k|lower}",
                                width: 50,
                                align: 'center'{literal}}{/literal},

                            {else}
                            {if !preg_match("/lp/", $k)}
                                {literal}{
                                {/literal}display: "{$c}", name: "{$k|lower}",

                                {if preg_match("/title/", $k)}
                                width: 50, align: 'left',
                                {elseif preg_match("/tokenization/", $k)}
                                width: 150, align: 'center',
                                {elseif preg_match("/suicide_place/", $k)}
                                width: 120, align: 'center',
                                {elseif preg_match("/source/", $k)}
                                width: 60, align: 'center',
                                {else}
                                width: 50, align: 'center',
                                {/if}

                                sortable: true{literal}}{/literal},
                            {/if}

                            {/if}
                            {/foreach}
                        ];
                    </script>
                </div>
            </div>
            <div class="col-md-2">
                <div class="scrollingWrapper panel-group" id="accordion">
                    {if $filter_order|@count gt 0}
                        <div class="panel panel-info">
                            <div class="panel-heading" id="headingActive">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseActive">
                                        Active filters
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseActive" class="panel-collapse collapse in">
                                <div class="scrollingAccordion">
                                    <div id="filter_menu_active" class="scrolling">
                                        {foreach from=$filter_order item=filter_type}
                                            {include file="inc_filter.tpl"}
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                    <div class="panel panel-info">
                        <div class="panel-heading" id="headingAvailable">
                            <div class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseAvailable">
                                    Available filters
                                </a>
                            </div>
                        </div>
                        <div id="collapseAvailable"
                             class="panel-collapse collapse {if empty($filter_order)} in{/if}">
                            <div class="scrollingAccordion">
                                <div id="filter_menu" class="panel-body scrolling">
                                    {foreach from=$filter_notset item=filter_type}
                                        {include file="inc_filter.tpl"}
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
</div>
{include file="inc_footer.tpl"}