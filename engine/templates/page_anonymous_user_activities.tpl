{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables" style = "padding: 0;">
    <div class = "col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">Activities by year</div>
            <div class="panel-body scrollingWrapper">
                <div class="scrolling">
                    <table id="user_activities_year" class="table table-striped text-center" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th class = "text-center">Year</th>
                            <th class = "text-center">Activities</th>
                            <th class = "text-center">Unique users</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$activities_years item=a}
                            <tr>
                                <td>{$a.year}</td>
                                <td>{$a.number_of_activities}</td>
                                <td>{$a.number_of_users}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <button id = "chart_year" class = "btn btn-primary chart_button" data-toggle="modal" data-target="#activity_year_modal">Chart</button>
            </div>
        </div>
    </div>
    <div class = "col-md-4">
        <div class="panel panel-primary user_activities_details">
            <div class="panel-heading">Activities by month</div>
            <div class="panel-body scrollingWrapper">
                <div class="scrolling">
                    <table id="user_activities_year_month" class="table table-striped text-center" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th class = "text-center">Year</th>
                            <th class = "text-center">Month</th>
                            <th class = "text-center">Activities</th>
                            <th class = "text-center">Unique users</th>
                        </tr>
                            <tbody>
                            {foreach from=$activities_years_months item=a}
                                <tr>
                                    <td>{$a.year}</td>
                                    <td>{$a.month}</td>
                                    <td>{$a.number_of_activities}</td>
                                    <td>{$a.number_of_users}</td>
                                </tr>
                            {/foreach}
                            </tbody>

                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <button id = "chart_year_month" class = "btn btn-primary chart_button">Chart</button>
            </div>
        </div>
    </div>
    <div class = "col-md-4">
        <div class="panel panel-primary activity_list_hidden" style = "display: none;">
            <div class="panel-heading">Detailed activities</div>
            <div class="panel-body scrollingWrapper">
                <div class="scrolling">
                    <table id="user_activities_details" class="table table-striped" cellspacing="0" cellpadding="0">
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

<div class="modal fade settingsModal" id="activity_year_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Activities by year</h4>
            </div>
            <div class="modal-body text-center">
                <div class = "activity_year_loader loader"></div>
                <div style = "margin: 0 auto; display: inline-block;" id="year_chart_div"></div>
            </div>
        </div>
    </div>
</div>

{include file="inc_administration_bottom.tpl"}
{include file="inc_footer.tpl"}