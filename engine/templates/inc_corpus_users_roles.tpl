{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
            <div class="panel-heading">User roles</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="tablesorter table table-striped" id="corpus_set_corpus_role" cellspacing="1">
                    <thead>
                    <tr>
                        <th></th>
                        {foreach from=$corpus_roles item=role}
                            <th style = "text-align: center;">{$role.description}</th>
                        {/foreach}
                    </tr>
                    <tr>
                        <th></th>
                        {foreach from=$corpus_roles item=role}
                            <th style = "text-align: center;"><small>[{$role.role}]</small></th>
                        {/foreach}
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$users_roles item=user}
                        <tr>
                            <th>{$user.screename}</th>
                            {foreach from=$corpus_roles item=role}
                                <td style="text-align: center; {if $user.role|@contains:$role.role} background: #9DD943;{/if}">
                                    <input {if $user.role|@contains:$role.role}checked="checked"{/if} class="corpusRole" type="checkbox" user_id="{$user.user_id}" role="{$role.role}" value="1"/>
                                </td>
                            {/foreach}
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>