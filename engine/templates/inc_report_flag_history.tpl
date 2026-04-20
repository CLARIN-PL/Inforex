{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
<div id="col-history" class="{if $flags_active}col-md-8{else}col-md-9{/if} scrollingWrapper report-flag-history-content-column">
    <div class="panel panel-primary administration-content-panel report-flag-history-content-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
            <span>Flag history</span>
        </div>
        <div class="panel-body report-viewer-content-body report-flag-history-content-body">
            <div class="scrolling report-flag-history-table-wrap">
                <table id="flag_history" class="table table-striped table-hover administration-table report-flag-history-table" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th>Flag</th>
                        <th class="text-center">Change</th>
                        <th>User</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$flag_history item=flag}
                        <tr>
                            <td class="report-flag-history-flag">{$flag.flag}</td>
                            <td class="report-flag-history-change-cell">
                                <div class="report-flag-history-change">
                                    <span class="report-flag-history-state" title="{if $flag.old_status != null}{$flag.old_status}{else}not ready{/if}">
                                        <img src="gfx/flag_{if $flag.old_status != null}{$flag.old_status_id}{else}-1{/if}.png" alt="">
                                    </span>
                                    <span class="report-flag-history-arrow"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></span>
                                    <span class="report-flag-history-state" title="{$flag.new_status}">
                                        <img src="gfx/flag_{$flag.new_status_id}.png" alt="">
                                    </span>
                                </div>
                            </td>
                            <td class="report-flag-history-user">{$flag.screename}</td>
                            <td class="report-flag-history-date">{$flag.date}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper report-flag-history-filter-column">
    <div class="panel panel-info administration-content-panel report-flag-history-filter-panel">
        <div class="panel-heading administration-content-heading report-config-heading report-flag-history-filter-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-filter" aria-hidden="true"></i></span>
            <span>Filters</span>
        </div>
        <div class="panel-body report-viewer-content-body report-flag-history-filter-body">
            <div class="scrolling report-flag-history-filter-scroll">
                <div class="report-flag-history-filter-group">
                    <label for="user_filter">User</label>
                    <select class="form-control report-flag-history-select" id="user_filter" name="user">
                        <option value="-">-select-</option>
                        {foreach from=$users item=user}
                            <option value="{$user.user_id}">{$user.screename}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="report-flag-history-filter-group">
                    <label for="flag_filter">Flag</label>
                    <select class="form-control report-flag-history-select" id="flag_filter" name="flag">
                        <option value="-">-select-</option>
                        {foreach from=$flags item=flag}
                            <option value="{$flag.corpora_flag_id}">{$flag.short}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-footer report-flag-history-filter-footer">
            <button id="apply_history_filters" class="btn btn-primary report-flag-history-apply-button">
                <i class="fa fa-check" aria-hidden="true"></i>
                <span>Apply</span>
            </button>
        </div>
    </div>
</div>
