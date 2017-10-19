{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div class="container-fluid admin_tables" style = "padding: 0;">
    <div class = "col-md-6">
        <div class="panel panel-primary">
            <div class="panel-heading">User activities</div>
            <div class="panel-body scrollingWrapper">
                <div class="scrolling">
                    <table id="user_activities" class="table table-striped" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Login</th>
                            <th>Username</th>
                            <th>Last activity</th>
                            <th>Last 30 days</th>
                            <th>All activities</th>
                            <th></th>
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
                                <td><button type = "button" class = "browse_user_activity btn btn-primary" id = "{$a.user_id}">Summary</button></td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class = "col-md-6">
        <div class="panel panel-primary user_activities_details" style = "display: none;">
            <div class="panel-heading">User activities</div>
            <div class="panel-body scrollingWrapper">
                <div class="scrolling">
                    <table id="user_activity_table" class="table table-striped" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th>Activity</th>
                            <th>Total</th>
                            <th>Last month</th>
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

<div class="modal fade settingsModal" id="activity_list_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Activity list</h4>
            </div>
            <div class="modal-body">
                <div class = "loader" style = "display: none;"></div>
                <div class = "activity_list_hidden" style = "display: none;">
                    <table id="user_activity_list_table" class="table table-striped">
                        <thead>
                        <tr>
                            <th>Time</th>
                            <th>Activity</th>
                            <th>Corpus</th>
                            <th>Report ID</th>
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