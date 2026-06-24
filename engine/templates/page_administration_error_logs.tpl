{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-error-logs-page">
    <div class="panel administration-content-panel administration-error-logs-panel">
        <div class="panel-heading administration-content-heading">
            <span class="administration-content-heading-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
            <span>Error logs</span>
        </div>
        <div class="panel-body">
            <div class="administration-error-logs-hero">
                <div class="administration-error-logs-eyebrow">Server diagnostics</div>
                <h3 class="administration-error-logs-title">Inspect recent application and runtime errors in one place</h3>
                <p class="administration-error-logs-text">This view reads configured server log files directly from the application environment. Use it to quickly confirm recent warnings, fatal errors, and runtime exceptions without leaving the administration panel.</p>
            </div>

            <div class="row administration-error-logs-grid">
                <div class="col-md-4 administration-error-logs-column">
                    <div class="panel panel-default administration-error-logs-subpanel">
                        <div class="panel-heading administration-error-logs-subheading">
                            <span><i class="fa fa-list" aria-hidden="true"></i> Available log sources</span>
                        </div>
                        <div class="panel-body">
                            <div class="administration-error-logs-source-list">
                                {foreach from=$admin_error_logs_overview item=source}
                                    <a class="administration-error-logs-source-card {if $admin_error_logs_selected_source==$source.key}is-active{/if}" href="index.php?page=administration_error_logs&amp;source={$source.key|escape}&amp;lines={$admin_error_logs_selected_lines|escape}{if $admin_error_logs_query neq ''}&amp;q={$admin_error_logs_query|escape:'url'}{/if}">
                                        <div class="administration-error-logs-source-main">
                                            <span class="administration-error-logs-source-title">{$source.title|escape}</span>
                                            <span class="administration-error-logs-source-path" title="{$source.path|escape}">{$source.path|escape}</span>
                                        </div>
                                        <div class="administration-error-logs-source-meta">
                                            {if !$source.exists}
                                                <span class="administration-error-logs-status administration-error-logs-status-missing">Missing</span>
                                            {elseif !$source.readable}
                                                <span class="administration-error-logs-status administration-error-logs-status-unreadable">Unreadable</span>
                                            {else}
                                                <span class="administration-error-logs-status administration-error-logs-status-ok">Readable</span>
                                            {/if}
                                            {if $source.modified_at}
                                                <small>Updated: {$source.modified_at|escape}</small>
                                            {/if}
                                        </div>
                                    </a>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 administration-error-logs-column">
                    <div class="panel panel-default administration-error-logs-subpanel">
                        <div class="panel-heading administration-error-logs-subheading">
                            <span><i class="fa fa-filter" aria-hidden="true"></i> Log viewer</span>
                        </div>
                        <div class="panel-body">
                            <form class="administration-error-logs-toolbar" method="get" action="index.php">
                                <input type="hidden" name="page" value="administration_error_logs">
                                <div class="form-group">
                                    <label for="admin_error_logs_source">Source</label>
                                    <select id="admin_error_logs_source" name="source" class="form-control">
                                        {foreach from=$admin_error_logs_overview item=source}
                                            <option value="{$source.key|escape}" {if $admin_error_logs_selected_source==$source.key}selected="selected"{/if}>{$source.title|escape}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="admin_error_logs_lines">Lines</label>
                                    <select id="admin_error_logs_lines" name="lines" class="form-control">
                                        {foreach from=[100,200,500,1000] item=line_option}
                                            <option value="{$line_option}" {if $admin_error_logs_selected_lines==$line_option}selected="selected"{/if}>{$line_option}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="form-group administration-error-logs-search">
                                    <label for="admin_error_logs_query">Search</label>
                                    <input id="admin_error_logs_query" type="text" name="q" value="{$admin_error_logs_query|escape}" class="form-control" placeholder="Filter by text">
                                </div>
                                <div class="administration-error-logs-actions">
                                    <button type="submit" class="btn btn-primary">Refresh</button>
                                </div>
                            </form>

                            {if $admin_error_logs_data.source}
                                <div class="administration-error-logs-current-source">
                                    <span class="administration-error-logs-current-title">{$admin_error_logs_data.source.title|escape}</span>
                                    <span class="administration-error-logs-current-path">{$admin_error_logs_data.source.path|escape}</span>
                                </div>
                            {/if}

                            {if $admin_error_logs_data.error}
                                <div class="home-corpora-empty administration-error-logs-empty">
                                    <i class="fa fa-warning" aria-hidden="true"></i>
                                    <span>{$admin_error_logs_data.error|escape}</span>
                                </div>
                            {else}
                                <div class="administration-error-logs-viewer">
                                    {foreach from=$admin_error_logs_data.lines item=line}
                                        <div class="administration-error-logs-line administration-error-logs-line-{$line.level|escape}">
                                            <span class="administration-error-logs-level administration-error-logs-level-{$line.level|escape}">{$line.level|upper}</span>
                                            <code>{$line.raw|escape}</code>
                                        </div>
                                    {/foreach}
                                    {if $admin_error_logs_data.lines|@count==0}
                                        <div class="home-corpora-empty administration-error-logs-empty">
                                            <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                            <span>No log lines matched the current filter.</span>
                                        </div>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_administration_bottom.tpl"}
{include file="inc_footer.tpl"}
