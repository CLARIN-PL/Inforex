{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables corpus-flag-history-page">
    <div class="row corpus-flag-history-grid">
        <div class="col-md-3 corpus-flag-history-column corpus-flag-history-column-filters">
            <div class="panel administration-content-panel corpus-flag-history-panel corpus-flag-history-filters-panel">
                <div class="panel-heading administration-content-heading corpus-flag-history-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-filter" aria-hidden="true"></i></span>
                    <span>Filters</span>
                </div>
                <div class="panel-body corpus-flag-history-panel-body">
                    <div class="corpus-flag-history-filter-group">
                        <label for="user_filter">User</label>
                        <select class="form-control" id="user_filter" name="user">
                            <option value="-">-select-</option>
                            {foreach from=$users item=user}
                                <option value="{$user.user_id}">{$user.screename}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="corpus-flag-history-filter-group">
                        <label for="flag_filter">Flag</label>
                        <select class="form-control" id="flag_filter" name="flag">
                            <option value="-">-select-</option>
                            {foreach from=$flags item=flag}
                                <option value="{$flag.corpora_flag_id}">{$flag.short}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer corpus-flag-history-footer">
                    <button id="apply_history_filters" class="btn btn-primary corpus-flag-history-apply-button">
                        <i class="fa fa-check" aria-hidden="true"></i>
                        Apply
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-9 corpus-flag-history-column">
            <div class="panel administration-content-panel corpus-flag-history-panel">
                <div class="panel-heading administration-content-heading corpus-flag-history-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
                    <span>Flag history</span>
                    <span class="home-corpora-counter corpus-flag-history-counter">{$total_results}</span>
                </div>
                <div class="panel-body corpus-flag-history-panel-body">
                    <form method="get" class="corpus-flag-history-toolbar">
                        <input type="hidden" name="page" value="corpus_flag_history">
                        <input type="hidden" name="corpus" value="{$corpus.id}">
                        <div class="corpus-flag-history-search-group">
                            <label for="flag_history_search">Document / Flag / User</label>
                            <input id="flag_history_search" name="search" type="text" class="form-control" placeholder="Filter by document, flag or user..." value="{$search|escape}">
                        </div>
                        <button type="submit" class="btn btn-default corpus-flag-history-search-button">
                            <i class="fa fa-search" aria-hidden="true"></i>
                            Search
                        </button>
                    </form>
                    <div class="administration-table-wrapper corpus-flag-history-table-wrapper">
                        <table id="flag_history" class="table table-striped table-hover administration-table corpus-flag-history-table" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th class="corpus-flag-history-document-column">Document</th>
                                <th class="corpus-flag-history-flag-column">Flag</th>
                                <th class="corpus-flag-history-status-column">Old status</th>
                                <th class="corpus-flag-history-status-column">New status</th>
                                <th class="corpus-flag-history-user-column">User</th>
                                <th class="corpus-flag-history-date-column">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$flag_history item=flag}
                                <tr>
                                    <td>
                                        <a class="corpus-flag-history-document-link" href="index.php?page=report&amp;corpus={$flag.corpus_id}&amp;subpage=preview&amp;id={$flag.report_id}">{$flag.report_name}</a>
                                    </td>
                                    <td><span class="corpus-flag-history-flag-badge">{$flag.flag}</span></td>
                                    <td class="corpus-flag-history-status-cell">
                                        <img src="gfx/flag_{if $flag.old_status != null}{$flag.old_status_id}{else}-1{/if}.png" title="{$flag.old_status}">
                                    </td>
                                    <td class="corpus-flag-history-status-cell">
                                        <img src="gfx/flag_{$flag.new_status_id}.png" title="{$flag.new_status}">
                                    </td>
                                    <td>{$flag.screename}</td>
                                    <td>
                                        <span class="administration-activities-time corpus-flag-history-time" title="{$flag.date|escape}">
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                                            <span>{$flag.date|date_format:"%Y-%m-%d"}</span>
                                            <small>{$flag.date|date_format:'%H:%M'}</small>
                                        </span>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    {if !$flag_history}
                        <div class="home-corpora-empty corpus-flag-history-empty">
                            <i class="fa fa-flag-o" aria-hidden="true"></i>
                            <span>No flag history entries match the selected filters.</span>
                        </div>
                    {/if}
                </div>
                <div class="panel-footer administration-content-footer corpus-flag-history-table-footer">
                    <div class="home-corpora-pagination corpus-flag-history-pagination">
                        <div class="home-corpora-pagination-info">
                            {if $total_results > 0}
                                Showing {$page_start} to {$page_end} of {$total_results} entries
                            {else}
                                No matching entries
                            {/if}
                        </div>
                        <div class="home-corpora-pagination-controls">
                            <a class="home-corpora-page-button{if $current_page <= 1} disabled{/if}" href="{if $current_page > 1}?page=corpus_flag_history&amp;corpus={$corpus.id}&amp;history_page={$current_page-1}&amp;search={$search|escape:'url'}{else}#{/if}">Previous</a>
                            {foreach from=$page_numbers item=page_number}
                                <a class="home-corpora-page-button{if $page_number == $current_page} active{/if}" href="?page=corpus_flag_history&amp;corpus={$corpus.id}&amp;history_page={$page_number}&amp;search={$search|escape:'url'}">{$page_number}</a>
                            {/foreach}
                            <a class="home-corpora-page-button{if $current_page >= $total_pages} disabled{/if}" href="{if $current_page < $total_pages}?page=corpus_flag_history&amp;corpus={$corpus.id}&amp;history_page={$current_page+1}&amp;search={$search|escape:'url'}{else}#{/if}">Next</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
