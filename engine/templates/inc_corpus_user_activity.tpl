{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div class="container-fluid admin_tables administration-activities corpus-settings-activities">
    <div class="row administration-activities-grid">
        <div class="col-md-6 administration-activities-column">
        <div class="panel panel-primary administration-content-panel administration-activities-panel corpus-settings-activities-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
                <span>User activities</span>
            </div>
            <div class="panel-body">
                <div class="tableContent">
                    <div class="administration-wsd-loading corpus-settings-activities-main-loading">
                        <img src="gfx/ajax.gif" alt="Loading"/>
                        <span>Loading user activities...</span>
                    </div>
                    <table id="user_activities" class="table table-striped administration-table administration-activities-table corpus-settings-activities-table" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th>UID</th>
                            <th>Login</th>
                            <th>User</th>
                            <th>Last activity</th>
                            <th>30 days</th>
                            <th>Total</th>
                            <th>List</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
        <div class="col-md-6 administration-activities-column">
        <div class="panel panel-primary administration-content-panel administration-activities-panel corpus-settings-activities-panel user_activities_details" style="display: none;">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                <span>Activity summary</span>
            </div>
            <div class="panel-body">
                <div class="tableContent">
                    <div class="administration-wsd-loading corpus-settings-activities-summary-loading" style="display: none;">
                        <img src="gfx/ajax.gif" alt="Loading"/>
                        <span>Loading activity summary...</span>
                    </div>
                    <table id="user_activity_table" class="table table-striped administration-table administration-activities-table corpus-settings-activities-summary-table" cellspacing="0" cellpadding="0">
                        <thead>
                        <tr>
                            <th>Event</th>
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
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-activity-modal" id="activity_list_modal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-history" aria-hidden="true"></i> Activity list</h4>
            </div>
            <div class="modal-body">
                <div class="administration-wsd-loading loader" style="display: none;">
                    <img src="gfx/ajax.gif" alt="Loading"/>
                    <span>Loading latest activities...</span>
                </div>
                <div class="activity_list_hidden" style="display: none;">
                    <table id="user_activity_list_table" class="table table-striped administration-table administration-activities-table administration-activities-list-table corpus-settings-activity-list-table">
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
