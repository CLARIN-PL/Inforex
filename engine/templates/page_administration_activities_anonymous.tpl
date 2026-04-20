{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-activities administration-activities-anonymous">
    <div class="row administration-activities-grid">
    <div class="col-md-4 administration-activities-column">
        <div class="panel panel-primary administration-content-panel administration-activities-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-calendar"></i></span>
                <span>Activities by year</span>
            </div>
            <div class="panel-body">
                <div class="tableContent">
                    <div class="administration-wsd-loading administration-activities-year-loading">
                        <img src="gfx/ajax.gif" alt="Loading"/>
                        <span>Loading yearly activity summary...</span>
                    </div>
                    <table id="user_activities_year" class="table table-striped text-center administration-table administration-activities-table" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th class = "text-center">Year</th>
                            <th class = "text-center">Activities</th>
                            <th class = "text-center">Unique users</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel-footer administration-content-footer administration-activities-footer">
                <button id = "chart_year" class = "btn btn-primary chart_button" data-toggle="modal" data-target="#activity_year_modal">Chart</button>
            </div>
        </div>
    </div>
    <div class="col-md-4 administration-activities-column">
        <div class="panel panel-primary administration-content-panel administration-activities-panel user_activities_months" style="display: none;">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-calendar-o"></i></span>
                <span>Activities by month</span>
            </div>
            <div class="panel-body">
                <div class="tableContent">
                    <div class="administration-wsd-loading administration-activities-month-loading" style="display: none;">
                        <img src="gfx/ajax.gif" alt="Loading"/>
                        <span>Loading monthly activity summary...</span>
                    </div>
                    <table id="user_activities_year_month" class="table table-striped text-center administration-table administration-activities-table" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th class = "text-center">Month</th>
                            <th class = "text-center">Activities</th>
                            <th class = "text-center">Unique users</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel-footer administration-content-footer administration-activities-footer">
                <button id = "chart_year_month" class = "btn btn-primary chart_button" data-toggle="modal" data-target="#activity_year_month_modal">Chart</button>
            </div>
        </div>
    </div>
    <div class="col-md-4 administration-activities-column">
        <div class="panel panel-primary administration-content-panel administration-activities-panel activity_list_hidden" style = "display: none;">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-history"></i></span>
                <span>Detailed activities</span>
            </div>
            <div class="panel-body">
                <div class="tableContent">
                    <div class="administration-wsd-loading administration-activities-detail-loading" style="display: none;">
                        <img src="gfx/ajax.gif" alt="Loading"/>
                        <span>Loading activity details...</span>
                    </div>
                    <table id="user_activities_details" class="table table-striped administration-table administration-activities-table administration-activities-list-table" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Activity</th>
                                <th>IP</th>
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

<div class="modal fade settingsModal administration-form-modal administration-activities-chart-modal" id="activity_year_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="administration-chart-modal-heading">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    <div class="administration-chart-modal-heading-text">
                        <h4 class="modal-title">Activities by year</h4>
                        <p class="administration-chart-modal-subtitle">Shows the selected metric aggregated by calendar year for anonymous users.</p>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="administration-chart-layout">
                    <div class="administration-chart-controls">
                        <div class="form-group">
                            <label for="select_year_value">Metric</label>
                            <select class="form-control" id="select_year_value">
                                <option value="Activities">Activities</option>
                                <option value="Users">Users</option>
                            </select>
                        </div>
                    </div>
                    <div class="administration-chart-preview">
                        <div class="administration-wsd-loading activity_year_loader">
                            <img src="gfx/ajax.gif" alt="Loading"/>
                            <span>Loading yearly chart...</span>
                        </div>
                        <div class="administration-chart-inline-title" id="year_chart_title"></div>
                        <div class="administration-chart-canvas" id="year_chart_div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal administration-activities-chart-modal" id="activity_year_month_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="administration-chart-modal-heading">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    <div class="administration-chart-modal-heading-text">
                        <h4 class="modal-title">Activities by month</h4>
                        <p class="administration-chart-modal-subtitle">Shows the selected metric split by month for the selected year.</p>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="administration-chart-layout">
                    <div class="administration-chart-controls">
                        <div class="form-group">
                            <label for="select_year">Year</label>
                            <select class="form-control" id="select_year">
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="select_month_value">Metric</label>
                            <select class="form-control" id="select_month_value">
                                <option value="Activities">Activities</option>
                                <option value="Users">Users</option>
                            </select>
                        </div>
                    </div>
                    <div class="administration-chart-preview">
                        <div class="administration-wsd-loading activity_year_month_loader">
                            <img src="gfx/ajax.gif" alt="Loading"/>
                            <span>Loading monthly chart...</span>
                        </div>
                        <div class="administration-chart-inline-title" id="year_month_chart_title"></div>
                        <div class="administration-chart-canvas" id="year_month_chart_div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_administration_bottom.tpl"}
{include file="inc_footer.tpl"}
