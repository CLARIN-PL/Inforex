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
            <span class="administration-content-heading-icon"><i class="fa fa-tachometer" aria-hidden="true"></i></span>
            <span>Activity dashboard</span>
        </div>
        <div class="panel-body">
            <div class="administration-activity-dashboard-hero">
                <div class="administration-activity-dashboard-copy">
                    <div class="administration-activity-dashboard-eyebrow">Live administration overview</div>
                    <h3 class="administration-activity-dashboard-title">See who is active now and how usage changes over time</h3>
                    <p class="administration-activity-dashboard-text">The dashboard summarizes recent user activity across the application. “Active now” means any registered user with activity in the last 15 minutes.</p>
                </div>
            </div>

            <div class="administration-activity-dashboard-stats">
                <div class="administration-activity-stat-card administration-activity-stat-card-primary">
                    <span class="administration-activity-stat-label">Active now</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_summary.active_now|default:0}</span>
                    <span class="administration-activity-stat-help">last 15 minutes</span>
                </div>
                <div class="administration-activity-stat-card">
                    <span class="administration-activity-stat-label">Active today</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_summary.active_day|default:0}</span>
                    <span class="administration-activity-stat-help">last 24 hours</span>
                </div>
                <div class="administration-activity-stat-card">
                    <span class="administration-activity-stat-label">Active this week</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_summary.active_week|default:0}</span>
                    <span class="administration-activity-stat-help">last 7 days</span>
                </div>
                <div class="administration-activity-stat-card">
                    <span class="administration-activity-stat-label">Activity events</span>
                    <span class="administration-activity-stat-value">{$activity_dashboard_summary.events_day|default:0}</span>
                    <span class="administration-activity-stat-help">events in the last 24 hours</span>
                </div>
            </div>

            <div class="row administration-activity-dashboard-grid">
                <div class="col-md-7 administration-activity-dashboard-column">
                    <div class="panel panel-default administration-activity-dashboard-subpanel">
                        <div class="panel-heading administration-activity-dashboard-subheading">
                            <span><i class="fa fa-bar-chart" aria-hidden="true"></i> Activity in the last 24 hours</span>
                        </div>
                        <div class="panel-body">
                            <div class="administration-activity-chart-legend">
                                <span class="administration-activity-chart-legend-item"><span class="administration-activity-chart-legend-swatch administration-activity-chart-legend-swatch-events"></span> Events</span>
                                <span class="administration-activity-chart-legend-item"><span class="administration-activity-chart-legend-swatch administration-activity-chart-legend-swatch-users"></span> Active users</span>
                            </div>
                            <div class="administration-activity-chart">
                                {foreach from=$activity_dashboard_timeline.points item=point}
                                    <div class="administration-activity-chart-column" title="{$point.label|escape}: {$point.events_count} events, {$point.users_count} users">
                                        <div class="administration-activity-chart-bars">
                                            <span class="administration-activity-chart-bar administration-activity-chart-bar-events" style="height:{$point.events_percent}%"></span>
                                            <span class="administration-activity-chart-bar administration-activity-chart-bar-users" style="height:{$point.users_percent}%"></span>
                                        </div>
                                        <span class="administration-activity-chart-label">{$point.label}</span>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 administration-activity-dashboard-column">
                    <div class="panel panel-default administration-activity-dashboard-subpanel">
                        <div class="panel-heading administration-activity-dashboard-subheading">
                            <span><i class="fa fa-users" aria-hidden="true"></i> Active users</span>
                        </div>
                        <div class="panel-body administration-activity-dashboard-table-body">
                            <div class="administration-table-wrapper">
                                <table class="table table-striped table-hover administration-table administration-activity-dashboard-table">
                                    <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Last activity</th>
                                        <th>24h</th>
                                        <th>7d</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {foreach from=$activity_dashboard_users item=dashboard_user}
                                        <tr>
                                            <td>
                                                <div class="administration-activity-user-cell">
                                                    <span class="administration-activity-user-name">{$dashboard_user.screename|escape}</span>
                                                    <small class="administration-activity-user-login">{$dashboard_user.login|escape}</small>
                                                </div>
                                            </td>
                                            <td>{$dashboard_user.last_activity|escape}</td>
                                            <td>{$dashboard_user.events_24h|escape}</td>
                                            <td>{$dashboard_user.events_7d|escape}</td>
                                        </tr>
                                    {/foreach}
                                    {if $activity_dashboard_users|@count==0}
                                        <tr>
                                            <td colspan="4">
                                                <div class="home-corpora-empty">
                                                    <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                                    <span>No users are active right now.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    {/if}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

{include file="inc_administration_bottom.tpl"}
{include file="inc_footer.tpl"}
