{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-4 tableContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Users</div>
                <div class="tableContent panel-body scrolling">
                    <table class="tablesorter table table-striped" id="corpus_update" cellspacing="1">
                        <thead>
                        <tr>
                            <th>User</th>
                            <th>Login</th>
                            <th style="text-align: center">Assign</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$users_in_corpus item=user}
                            <tr>
                                <td>{$user.screename}</td>
                                <td>{$user.login}</td>
                                <td style="text-align: center"><input {if $user.role}checked="checked"{/if} class="userInCorpus" type="checkbox" element_type="users" value="{$user.user_id}" /></td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>