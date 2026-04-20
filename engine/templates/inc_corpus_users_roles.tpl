{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables corpus-settings-roles">
    <div class="row corpus-settings-roles-grid">
        <div class="col-md-12 corpus-settings-roles-column">
        <div class="panel administration-content-panel corpus-settings-roles-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-key" aria-hidden="true"></i></span>
                <span>User roles</span>
            </div>
            <div class="panel-body">
                <div class="administration-table-wrapper corpus-settings-roles-table-wrapper">
                <table class="tablesorter table table-striped table-hover administration-table corpus-settings-roles-table" id="corpus_set_corpus_role" cellspacing="1">
                    <thead>
                    <tr>
                        <th class="corpus-settings-roles-user-header">User</th>
                        {foreach from=$corpus_roles item=role}
                            <th class="corpus-settings-roles-role-header">
                                <span class="corpus-settings-role-heading">
                                    <span class="corpus-settings-role-description" title="{$role.description|escape}">
                                        {if $role.role == "agreement_morpho"}Zgodność dezamb. morf.{else}{$role.description}{/if}
                                    </span>
                                    <span class="corpus-settings-role-code">{$role.role}</span>
                                </span>
                            </th>
                        {/foreach}
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$users_roles item=user}
                        <tr>
                            <th class="corpus-settings-roles-user-cell">
                                <span class="corpus-settings-role-user-name">{$user.screename}</span>
                            </th>
                            {foreach from=$corpus_roles item=role}
                                <td class="corpus-settings-roles-checkbox-cell {if $user.role|@contains:$role.role}corpus-settings-roles-checkbox-cell-active{/if}">
                                    <label class="corpus-settings-role-checkbox" title="{$role.description|escape}">
                                        <input {if $user.role|@contains:$role.role}checked="checked"{/if} class="corpusRole" type="checkbox" user_id="{$user.user_id}" role="{$role.role}" value="1"/>
                                        <span aria-hidden="true"></span>
                                        <span class="sr-only">{$role.description}</span>
                                    </label>
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
    </div>
</div>
