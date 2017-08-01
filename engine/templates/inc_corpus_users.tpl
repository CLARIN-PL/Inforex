{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-5 tableContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Users assigned to corpus</div>
                <div class="tableContent panel-body scrolling">
                    <table class="tablesorter table table-striped" id="corpus_update" cellspacing="1">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th>Last activity</th>
                            <th style="text-align: center">Remove</th>
                        </tr>
                        </thead>
                        <tbody id = "users_assigned_table">
                        {foreach from=$users_in_corpus item=user}
                            {if $user.role}
                                <tr>
                                    <td>{$user.screename}</td>
                                    <td>{$user.login}</td>
                                    <td>{$user.email}</td>
                                    <td>{$user.last_activity}</td>
                                    <td style="text-align: center"><button id = "{$user.user_id}"class = 'remove_user_button btn btn-primary'><i class='fa fa-arrow-right' aria-hidden='true'></i></button></td>
                                </tr>
                            {/if}
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5 tableContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Users not assigned</div>
                <div class="tableContent panel-body scrolling">
                    <div class="navbar navbar-default">
                        <div class="navbar-collapse collapse" id="searchbar">
                            <form class="navbar-form search-form">
                                <div class="form-group" style="display:inline;">
                                    <div class="input-group text-center" style="display:table;">
                                        <span class="input-group-addon" style="width:1%;"><span class="glyphicon glyphicon-search"></span></span>
                                        <input class="form-control search_users" name="search" placeholder="Enter at least 3 characters to show matching users." autocomplete="off" autofocus="autofocus" type="text">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class="tablesorter table table-striped" id="add_user_to_corpus" cellspacing="1">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Login</th>
                            <th>Email</th>
                            <th style="text-align: center">Add</th>
                        </tr>
                        </thead>
                        <tbody id = "add_user_to_corpus_table">
                        </tbody>
                    </table>
                </div>
            </div>
        </
    </div>
</div>