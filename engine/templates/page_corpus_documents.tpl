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
        <div class="col-md-10 scrollingWrapper">
            <div class="flexigrid" >
                <table id="table-documents">
                  <tr>
                      <td style="vertical-align: middle;"><div>Loading ... <img style="vertical-align: baseline" title="" src="gfx/flag_4.png"><input type="checkbox"></div></td>
                  </tr>
                </table>
                <script type="text/javascript">

                var init_from = {$from};

                var colModel = [
                {foreach from=$columns item=c key=k}
                    {if preg_match("/^flag/",$k)}
                        {literal}{{/literal}display: "{$c.short}", name : "{$k|lower}", width : 40, sortable : true, align: 'center'{literal}}{/literal},
                    {elseif preg_match("/found_base_form/", $k)}
                        {literal}{{/literal}display: "{$c}", name : "{$k|lower}", width : 200, sortable : true, align: 'center'{literal}}{/literal},
                    {elseif $c=="Subcorpus"}
                        {literal}{{/literal}display: "{$c}", name : "{$k|lower}", width : 100, sortable : true, align: 'left'{literal}}{/literal},
                    {elseif $c=="checkbox"}
                        {literal}{{/literal}display: "<input class = 'select_all' type='checkbox' name='select_action'>", name: "{$k|lower}", width: 50,  align: 'center'{literal}}{/literal},

                    {else}
                        {if !preg_match("/lp/", $k)}
                            {literal}{{/literal}display: "{$c}", name : "{$k|lower}",

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

                            sortable : true{literal}}{/literal},
                        {/if}

                    {/if}
                {/foreach}
                ];
                </script>
            </div>
        </div>
        <div class="col-md-2 scrollingWrapper">

            {if $filter_order|@count>0}
            <div class="panel panel-info">
                <div class="panel-heading">Active filters</div>
                <div id="filter_menu_active" class="panel-body scrolling">
                    {foreach from=$filter_order item=filter_type}
                        {include file="inc_filter.tpl"}
                    {/foreach}
                </div>
            </div>
            {/if}

            <div class="panel panel-info">
                <div class="panel-heading">Available filters</div>
                <div id="filter_menu" class="panel-body scrolling">
                    {foreach from=$filter_notset item=filter_type}
                        {include file="inc_filter.tpl"}
                    {/foreach}
                </div>
            </div>

            <div id="selection_menu" style ="overflow-y:auto; margin-top:20px;">
                <div class="panel panel-info">
                    <div class="panel-heading">Batch operations</div>
                    <div class="panel-body">
                        <div style="margin-bottom: 30px;">

                            <div style ="display: inline;">

                                <div id="cell_annotation_wait" style = "display: none;">
                            Trwa wczytywanie danych
                            <img src="gfx/ajax.gif" />
                                </div>
                                <p id = "selectedRows"></p>
                                <button id = "select_everything" title = "Select every document matching current filter." class="btn btn-xs btn-info">Select all</button>
                                <button id = "clear_all" title = "Unselect all documents in this corpus."  class="btn btn-xs btn-info">Clear all</button>
                            </div>
                            <button id = "show_selected" title = "Display the list of all selected documents." class="btn btn-xs btn-info">Show selected</button><br/><br/>

                            <select style = "width: 120px;" id = "selected_flags">
                                {if empty($corpus_flag_ids)}
                                <option value="" disabled selected>-Flag-</option>
                                {/if}

                                <option value="" selected="selected">-Flag-</option>
                                {foreach from=$corpus_flag_ids  item="set"}
                                    <option value="{$set.id}">{$set.name}</option>
                                    </optgroup>
                                {/foreach}
                            </select>
                            <select style = "width: 120px;" id = "selected_action" name="selected_flags">
                                {if empty($available_flags)}
                                <option value="" disabled selected>-Status-</option>
                                {/if}
                                    <option value="" selected="selected">-Status-</option>
                                {foreach from=$available_flags  item="set"}
                                    <option value="{$set.flag_id}">{$set.name}</option>
                                    </optgroup>
                                {/foreach}
                            </select>
                            <br>
                            <select style = "width: 120px;" id = "selected_subcorpus" name="selected_subcorpora">
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
                    <div class="panel-footer">
                        <button id = "selection_action" class="btn btn-primary disabled" disabled>Submit</button>
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