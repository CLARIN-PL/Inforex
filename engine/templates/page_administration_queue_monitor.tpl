{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-activity-dashboard">
    <div class="panel administration-content-panel administration-activity-dashboard-panel">
        <div class="panel-heading administration-content-heading">
            <span class="administration-content-heading-icon"><i class="fa fa-tasks" aria-hidden="true"></i></span>
            <span>Queue monitor</span>
        </div>
        <div class="panel-body">
            <div class="administration-activity-dashboard-hero">
                <div class="administration-activity-dashboard-copy">
                    <div class="administration-activity-dashboard-eyebrow">System workload overview</div>
                    <h3 class="administration-activity-dashboard-title">Track export, report, and task-processing queues in one place</h3>
                    <p class="administration-activity-dashboard-text">Use this view to see what is waiting now, what is already being processed, and which queues need attention because of errors or growing backlog.</p>
                </div>
            </div>

            <div class="administration-activity-dashboard-stats administration-activity-dashboard-stats-queues">
                <div class="administration-activity-stat-card administration-activity-stat-card-primary">
                    <span class="administration-activity-stat-label">Queues with work</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_queues.summary.active_queues|default:0}</span>
                    <span class="administration-activity-stat-help">queues with pending or processing items</span>
                </div>
                <div class="administration-activity-stat-card">
                    <span class="administration-activity-stat-label">Waiting now</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_queues.summary.pending_items|default:0}</span>
                    <span class="administration-activity-stat-help">items with status <code>new</code></span>
                </div>
                <div class="administration-activity-stat-card">
                    <span class="administration-activity-stat-label">Processing now</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_queues.summary.processing_items|default:0}</span>
                    <span class="administration-activity-stat-help">items with status <code>process</code></span>
                </div>
                <div class="administration-activity-stat-card">
                    <span class="administration-activity-stat-label">Errors</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_queues.summary.error_items|default:0}</span>
                    <span class="administration-activity-stat-help">items with status <code>error</code></span>
                </div>
            </div>

            <div class="panel panel-default administration-activity-dashboard-subpanel administration-activity-dashboard-queue-panel">
                <div class="panel-heading administration-activity-dashboard-subheading">
                    <span><i class="fa fa-list-ul" aria-hidden="true"></i> All queues</span>
                </div>
                <div class="panel-body">
                    <div class="administration-activity-dashboard-queue-intro">
                        The table below combines asynchronous work handled by export jobs and background tasks, so we can quickly see which parts of the platform are currently under load.
                    </div>

                    <div class="administration-table-wrapper">
                        <table class="table table-striped table-hover administration-table administration-activity-dashboard-table administration-activity-dashboard-queue-table">
                            <thead>
                            <tr>
                                <th>Queue</th>
                                <th>Group</th>
                                <th>Waiting</th>
                                <th>Processing</th>
                                <th>Errors</th>
                                <th>Completed 24h</th>
                                <th>Oldest waiting</th>
                                <th>Last activity</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$activity_dashboard_queues.rows item=queue_row}
                                <tr>
                                    <td><div class="administration-activity-queue-name">{$queue_row.label|escape}</div></td>
                                    <td><span class="administration-activity-queue-group">{$queue_row.group_label|escape}</span></td>
                                    <td>
                                        {if $queue_row.pending_count > 0}
                                            <a href="index.php?page=administration_queue_monitor&amp;queue_id={$queue_row.id|escape:'url'}&amp;queue_status=waiting#queue-detail" class="administration-activity-queue-count administration-activity-queue-count-pending">{$queue_row.pending_count|escape}</a>
                                        {else}
                                            <span class="administration-activity-queue-count administration-activity-queue-count-pending">0</span>
                                        {/if}
                                    </td>
                                    <td>
                                        {if $queue_row.processing_count > 0}
                                            <a href="index.php?page=administration_queue_monitor&amp;queue_id={$queue_row.id|escape:'url'}&amp;queue_status=processing#queue-detail" class="administration-activity-queue-count administration-activity-queue-count-processing">{$queue_row.processing_count|escape}</a>
                                        {else}
                                            <span class="administration-activity-queue-count administration-activity-queue-count-processing">0</span>
                                        {/if}
                                    </td>
                                    <td>
                                        {if $queue_row.error_count > 0}
                                            <a href="index.php?page=administration_queue_monitor&amp;queue_id={$queue_row.id|escape:'url'}&amp;queue_status=error#queue-detail" class="administration-activity-queue-count administration-activity-queue-count-error">{$queue_row.error_count|escape}</a>
                                        {else}
                                            <span class="administration-activity-queue-count administration-activity-queue-count-error">0</span>
                                        {/if}
                                    </td>
                                    <td>
                                        {if $queue_row.completed_24h > 0}
                                            <a href="index.php?page=administration_queue_monitor&amp;queue_id={$queue_row.id|escape:'url'}&amp;queue_status=completed#queue-detail" class="administration-activity-queue-count administration-activity-queue-count-completed">{$queue_row.completed_24h|escape}</a>
                                        {else}
                                            <span class="administration-activity-queue-count administration-activity-queue-count-completed">0</span>
                                        {/if}
                                    </td>
                                    <td><span title="{$queue_row.oldest_pending|default:'-'|escape}">{$queue_row.oldest_pending_age|escape}</span></td>
                                    <td>{$queue_row.last_activity|default:'—'|escape}</td>
                                </tr>
                            {/foreach}
                            {if $activity_dashboard_queues.rows|@count==0}
                                <tr>
                                    <td colspan="8">
                                        <div class="home-corpora-empty">
                                            <i class="fa fa-inbox" aria-hidden="true"></i>
                                            <span>No queue data is available yet.</span>
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {if $activity_dashboard_queue_detail}
                <div class="panel panel-default administration-activity-dashboard-subpanel administration-activity-dashboard-queue-panel" id="queue-detail">
                    <div class="panel-heading administration-activity-dashboard-subheading">
                        <span><i class="fa fa-search" aria-hidden="true"></i> {$activity_dashboard_queue_detail.queue_label|escape} — {$activity_dashboard_queue_detail.status_label|escape}</span>
                    </div>
                    <div class="panel-body">
                        <div class="administration-activity-dashboard-queue-intro">
                            Showing the exact items currently matching this queue and status.
                        </div>

                        <div class="administration-table-wrapper">
                            <table class="table table-striped table-hover administration-table administration-activity-dashboard-table administration-activity-dashboard-queue-detail-table">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Corpus</th>
                                    <th>User</th>
                                    <th>Submitted</th>
                                    <th>Started</th>
                                    <th>Status</th>
                                    <th>Progress</th>
                                    <th>Description / Message</th>
                                    {if $activity_dashboard_queue_detail.item_kind == 'export'}<th>Action</th>{/if}
                                </tr>
                                </thead>
                                <tbody>
                                {foreach from=$activity_dashboard_queue_detail.items item=detail_item}
                                    <tr>
                                        <td>
                                            {if $activity_dashboard_queue_detail.item_kind == 'task'}
                                                <a href="index.php?page=corpus_tasks&amp;corpus={$detail_item.corpus_id|escape}&amp;task_id={$detail_item.item_id|escape}">{$detail_item.item_id|escape}</a>
                                            {else}
                                                {$detail_item.item_id|escape}
                                            {/if}
                                        </td>
                                        <td>{$detail_item.corpus_name|default:'—'|escape}</td>
                                        <td>{if $detail_item.screename}{$detail_item.screename|escape}{elseif $detail_item.login}{$detail_item.login|escape}{else}—{/if}</td>
                                        <td>{if isset($detail_item.datetime_submit)}{$detail_item.datetime_submit|escape}{else}{$detail_item.datetime|escape}{/if}</td>
                                        <td>{if isset($detail_item.datetime_start)}{$detail_item.datetime_start|default:'—'|escape}{else}—{/if}</td>
                                        <td>{$detail_item.status|escape}</td>
                                        <td>
                                            {if $activity_dashboard_queue_detail.item_kind == 'task'}
                                                {if $detail_item.max_steps > 0}{$detail_item.current_step|escape}/{$detail_item.max_steps|escape}{else}—{/if}
                                            {else}
                                                {$detail_item.progress|default:'0'|escape}%
                                            {/if}
                                        </td>
                                        <td>
                                            {if $detail_item.description}
                                                <div class="administration-activity-queue-detail-main">{$detail_item.description|escape}</div>
                                            {/if}
                                            {if $detail_item.message}
                                                <div class="administration-activity-queue-detail-secondary">{$detail_item.message|escape}</div>
                                            {/if}
                                            {if !$detail_item.description && !$detail_item.message}—{/if}
                                        </td>
                                        {if $activity_dashboard_queue_detail.item_kind == 'export'}
                                            <td>
                                                <div class="administration-activity-queue-action" data-export-id="{$detail_item.item_id|escape}">
                                                    <select class="form-control input-sm administration-activity-queue-status-select">
                                                        <option value="new"{if $detail_item.status=='new'} selected{/if}>new</option>
                                                        <option value="process"{if $detail_item.status=='process'} selected{/if}>process</option>
                                                        <option value="done"{if $detail_item.status=='done'} selected{/if}>done</option>
                                                        <option value="error"{if $detail_item.status=='error'} selected{/if}>error</option>
                                                    </select>
                                                    <button type="button" class="btn btn-xs btn-primary administration-activity-queue-status-apply">Apply</button>
                                                    <div class="administration-activity-queue-status-feedback"></div>
                                                </div>
                                            </td>
                                        {/if}
                                    </tr>
                                {/foreach}
                                {if $activity_dashboard_queue_detail.items|@count==0}
                                    <tr>
                                        <td colspan="{if $activity_dashboard_queue_detail.item_kind == 'export'}9{else}8{/if}">
                                            <div class="home-corpora-empty">
                                                <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                                                <span>No items match this queue filter right now.</span>
                                            </div>
                                        </td>
                                    </tr>
                                {/if}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>

{include file="inc_administration_bottom.tpl"}
{include file="inc_footer.tpl"}
