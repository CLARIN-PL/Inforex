{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div class="container-fluid admin_tables corpus-settings-users">
    <div class="row corpus-settings-users-grid">
        <div class="col-md-6 tableContainer corpus-settings-users-column">
            <div class="panel administration-content-panel corpus-settings-users-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-users" aria-hidden="true"></i></span>
                    <span>Users assigned to corpus</span>
                </div>
                <div class="panel-body">
                    <div class="administration-toolbar corpus-settings-users-search-toolbar">
                        <div class="corpus-settings-users-searchbar">
                            <form class="search-form">
                                <div class="form-group">
                                    <div class="input-group administration-search corpus-settings-users-search">
                                        <span class="input-group-addon"><i class="fa fa-filter" aria-hidden="true"></i></span>
                                        <input class="form-control search_assigned_users" name="assigned_search" placeholder="Filter assigned users by name, login or email." autocomplete="off" type="text">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="administration-table-wrapper corpus-settings-users-table-wrapper">
                    <table class="tablesorter table table-striped table-hover administration-table corpus-settings-users-table" id="corpus_update" cellspacing="1">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Last activity</th>
                            <th class="corpus-settings-users-action-header">Remove</th>
                        </tr>
                        </thead>
	                        <tbody id = "users_assigned_table">
	                        {foreach from=$users_in_corpus item=user}
	                            <tr>
	                                <td><span class="corpus-settings-user-name">{$user.screename}</span></td>
	                                <td><span class="corpus-settings-user-login">{$user.login}</span></td>
	                                <td><span class="corpus-settings-user-email">{$user.email}</span></td>
	                                <td>
                                        {if $user.last_activity}
                                            <span class="administration-activities-time corpus-settings-user-date" title="{$user.last_activity|escape}">
                                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                <span>{$user.last_activity|date_format:"%Y-%m-%d"}</span>
                                                <small>{$user.last_activity|date_format:"%H:%M"}</small>
                                            </span>
                                        {else}
                                            <span class="administration-activities-time-empty corpus-settings-user-date-empty">No activity</span>
                                        {/if}
                                    </td>
	                                <td class="corpus-settings-users-action-cell">
                                        <button id="{$user.user_id}" class="remove_user_button btn btn-primary corpus-settings-user-action-button" title="Remove user from corpus">
                                            <i class="fa fa-arrow-right" aria-hidden="true"></i><span class="sr-only">Remove user</span>
                                        </button>
                                    </td>
	                            </tr>
	                        {/foreach}
                        </tbody>
                    </table>
                    </div>
                    <div class="corpus-settings-users-pagination" id="assigned_users_pagination">
                        <div class="corpus-settings-users-pagination-info" id="assigned_users_pagination_info"></div>
                        <div class="corpus-settings-users-pagination-controls" id="assigned_users_pagination_controls"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 tableContainer corpus-settings-users-column">
            <div class="panel administration-content-panel corpus-settings-users-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-user-plus" aria-hidden="true"></i></span>
                    <span>Search user to assign</span>
                </div>
                <div class="panel-body">
                    <div class="administration-toolbar corpus-settings-users-search-toolbar">
                        <div id="searchbar">
                            <form class="search-form">
                                <div class="form-group">
                                    <div class="input-group administration-search corpus-settings-users-search">
                                        <span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
                                        <input class="form-control search_users" name="search" placeholder="Enter at least 3 characters to show matching users." autocomplete="off" autofocus="autofocus" type="text">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="administration-table-wrapper corpus-settings-users-table-wrapper corpus-settings-users-search-results">
                    <table class="tablesorter table table-striped table-hover administration-table corpus-settings-users-table" id="add_user_to_corpus" cellspacing="1">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th class="corpus-settings-users-action-header">Add</th>
                        </tr>
                        </thead>
                        <tbody id = "add_user_to_corpus_table">
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
