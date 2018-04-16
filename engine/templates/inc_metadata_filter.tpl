{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div class = "selected_status" id = {$status}></div>
<div class = "selected_subcorpus" id = {$subcorpus}></div>
<div class="panel panel-default">
    <div class="panel-heading">
        Common filters
    </div>
    <div class="panel-body scrolling" style="padding: 5px">

        {capture name=link_ext_filters assign=link_ext_filters}{foreach from=$filters item=filter}{if $filter.selected}&amp;filter_{$filter.name}={$filter.selected}{/if}{/foreach}{/capture}

        <table class="table table-stripped" cellspacing="1">
            {if $statuses}
                <tr>
                    <th>Status:</th>
                    <td>
                        {foreach from=$statuses item=s}
                            {if $s.id == $selected_filters.status}
                                <em>{$s.status}</em>
                            {else}
                                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;status={$s.id}">{$s.status}</a>
                            {/if},
                        {/foreach}
                        {if $selected_filters.status==0}
                            <em>all</em>
                        {else}
                            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;status=0">all</a>
                        {/if}
                    </td>
                </tr>
            {/if}
            {if $flags}
                <tr>
                    <th>Flags:</th>
                    <td>
                        <select name="corpus_flag_id" class = "corpus_flag_id" style="font-size: 12px">
                            <option value = "-" style="font-style: italic">Select flag</option>
                            {foreach from=$corpus_flags item=flag}
                                <option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$selected_filters.flags.flag}selected="selected"{/if} title="{$flag.name}"><em>{$flag.short}</em></option>
                            {/foreach}
                        </select>
                        <select name="flag_id" class = "flag_type" style="font-size: 12px">
                            <option value = "-" style="font-style: italic">type</option>
                            {foreach from=$flags item=flag}
                                <option value="{$flag.flag_id}" style="background-image:url(gfx/flag_{$flag.flag_id}.png); background-repeat: no-repeat; padding-left: 20px;" {if $flag.flag_id==$selected_filters.flags.flag_status}selected="selected"{/if}>{$flag.name}</option>
                            {/foreach}
                        </select>
                        {if $flag_set}
                            <i class="fa fa-times cancel_flags" aria-hidden="true"></i>
                        {/if}
                    </td>
                </tr>
            {/if}
            {if $features}
                {foreach from=$features item=feature}
                    <tr>
                        {assign var=field_name  value=$feature.field}
                        <th>{$feature.field}:</th>
                        <td>
                            {if $feature.type == 'text'}
                                {foreach from = $feature.data item = meta}
                                    {if $selected_filters.metadata.$field_name == $meta.name}
                                        <em>{$meta.name}</em> ,
                                    {else}
                                        <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value={$meta.name}">{$meta.name}</a> ,
                                    {/if}
                                {/foreach}
                                {if $selected_filters.metadata.$field_name == "0" || !isset($selected_filters.metadata.$field_name)}
                                    <em>all</em>
                                {else}
                                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value=0">all</a>
                                {/if}
                            {else}
                                {foreach from = $feature.field_values item = value}
                                    {if $selected_filters.metadata.$field_name == $value}
                                        <em>{$value}</em> ,
                                    {else}
                                        <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value={$value}">{$value}</a> ,
                                    {/if}
                                {/foreach}
                                {if $selected_filters.metadata.$field_name == "0" || !isset($selected_filters.metadata.$field_name)}
                                    <em>all</em>
                                {else}
                                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value=0">all</a>
                                {/if}
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            {/if}
        </table>

        {if $filters|@count>0}
            <h2>Custom filters</h2>
            <table class="tablesorter" cellspacing="1">
                {foreach from=$filters item=filter}
                    <tr>
                        <th style="width: 100px">{$filter.name}</th>
                        <td>
                            {assign var=filter_set  value=0}
                            {foreach from=$filter.values item=value key=key name=values}
                                {if $smarty.foreach.values.index > 0},{/if}
                                {if $key==$filter.selected}
                                    {assign var=filter_set value=1}
                                    <em>{$value}</em>
                                    <input type="hidden" name="filter_{$filter.name}" value="{$key}"/>
                                {else}
                                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$subcorpus}&amp;filter_{$filter.name}={$key}">{$value}</a>
                                {/if}
                            {/foreach}

                            {if $filter.all}
                                ,
                                {if $filter_set==0}
                                    <em>wszystkie</em>
                                {else}
                                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;ctag={$ctag}&amp;subcorpus={$subcorpus}">wszystkie</a>
                                {/if}
                            {/if}
                        </td>
                    </tr>
                {/foreach}
            </table>
        {/if}
    </div>
</div>