{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div class = "selected_status_id" id = {$status}></div>
<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <h5 style = "float: left;"><i class="fa fa-filter" aria-hidden="true"></i> Filter</h5>
        <button style = "float: right;" class = "btn btn-default" id = "copy_url" data-toggle="modal" data-target="#url_modal"><i class="fa fa-link" aria-hidden="true"></i> <span>Copy URL</span></button>
    </div>
    <div class="panel-body scrolling" style="padding: 5px">

        {capture name=link_ext_filters assign=link_ext_filters}{foreach from=$filters item=filter}{if $filter.selected}&amp;filter_{$filter.name}={$filter.selected}{/if}{/foreach}{/capture}

        {if $statuses}
            <div class="metadata-filter-section">
                <div class="metadata-filter-section-title">Status</div>
                <div class="metadata-filter-statuses">
                    {if $selected_filters.status==0}
                        <em>All</em>
                    {else}
                        <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;status=0">All</a>
                    {/if}
                    {foreach from=$statuses item=s}
                        {if $s.id == $selected_filters.status}
                            <em class="selected_status" id="{$s.id}">{$s.status}</em>
                        {else}
                            <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;status={$s.id}">{$s.status}</a>
                        {/if}
                    {/foreach}
                </div>
            </div>
        {/if}
        {if $flags}
            <div class="metadata-filter-section">
                <div class="metadata-filter-section-title">Flags</div>
                <div class="metadata-filter-controls">
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
                </div>
            </div>
        {/if}

        {if $features}
            {foreach from=$features item=feature}
                {assign var=field_name  value=$feature.field}
                <div class="metadata-filter-section">
                    <div class="metadata-filter-section-title">{$feature.field}</div>
                    <div class="metadata-filter-values">
                        {if $feature.type == 'text'}
                            {if $selected_filters.metadata.$field_name == "0" || !isset($selected_filters.metadata.$field_name)}
                                <em>All</em>
                            {else}
                                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value=0">All</a>
                            {/if}
                            {foreach from = $feature.data item = meta}
                                {if $selected_filters.metadata.$field_name == $meta.name}
                                    <em class = "selected_metadata" id = "{$feature.field}">{$meta.name|capitalize}</em>
                                {else}
                                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value={$meta.name}">{$meta.name|capitalize}</a>
                                {/if}
                            {/foreach}
                        {else}
                            {if $selected_filters.metadata.$field_name == "0" || !isset($selected_filters.metadata.$field_name)}
                                <em>All</em>
                            {else}
                                <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value=0">All</a>
                            {/if}
                            {foreach from = $feature.field_values item = value}
                                {if $selected_filters.metadata.$field_name == $value}
                                    <em class = "selected_metadata" id = "{$feature.field}">{$value|capitalize}</em>
                                {else}
                                    <a href="index.php?page={$page}&amp;corpus={$corpus.id}&amp;metadata={$feature.field}&amp;value={$value}">{$value|capitalize}</a>
                                {/if}
                            {/foreach}
                        {/if}
                    </div>
                </div>
            {/foreach}
        {/if}

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
