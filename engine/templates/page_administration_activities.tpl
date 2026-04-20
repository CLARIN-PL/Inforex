{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-activities">
    <div class="row administration-activities-grid">
        <div class="col-md-6 administration-activities-column">
            <div class="panel panel-primary administration-content-panel administration-activities-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-line-chart"></i></span>
                    <span>User activities</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent">
                        <div class="administration-wsd-loading administration-activities-main-loading">
                            <img src="gfx/ajax.gif" alt="Loading"/>
                            <span>Loading user activities...</span>
                        </div>
                        <table id="user_activities" class="table table-striped administration-table administration-activities-table" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>User ID</th>
                                <th>Login</th>
                                <th>User</th>
                                <th>Last activity</th>
                                <th>30 days</th>
                                <th>Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$activities item=a}
                                <tr>
                                    <td>{$a.user_id}</td>
                                    <td>{$a.login}</td>
                                    <td>{$a.screename}</td>
                                    <td>{$a.last_activity}</td>
                                    <td>{$a.num_of_activities_30}</td>
                                    <td>{$a.num_of_activities}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 administration-activities-column">
            <div class="panel panel-primary administration-content-panel administration-activities-panel user_activities_details" style = "display: none;">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-history"></i></span>
                    <span>Recent activity list</span>
                    <span class="administration-activities-heading-help" title="Showing latest 500 activities for selected user.">
                        <i class="fa fa-info" aria-hidden="true"></i>
                    </span>
                </div>
                <div class="panel-body">
                    <div class="tableContent">
                        <div class="administration-wsd-loading administration-activities-loading" style="display: none;">
                            <img src="gfx/ajax.gif" alt="Loading"/>
                            <span>Loading latest activities...</span>
                        </div>
                        <table id="user_activity_table" class="table table-striped administration-table administration-activities-table administration-activities-list-table" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th>When</th>
                                <th>Event</th>
                                <th>Corpus</th>
                                <th>Report</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_administration_bottom.tpl"}
{include file="inc_footer.tpl"}
