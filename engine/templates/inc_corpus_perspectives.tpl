{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div class="container-fluid admin_tables corpus-settings-perspectives">
    <div class="row corpus-settings-perspectives-grid">
        <div class="col-md-12 corpus-settings-perspectives-column">
            <div class="panel administration-content-panel corpus-settings-perspectives-panel">
                <div class="panel-heading administration-content-heading">
                    <div class="corpus-settings-perspectives-heading-title">
                        <span class="administration-content-heading-icon"><i class="fa fa-eye" aria-hidden="true"></i></span>
                        <span>Access to perspectives</span>
                    </div>
                    <button type="button" class="btn btn-primary corpus-settings-perspectives-heading-action" id="reportPerspectives" data-toggle="modal" data-target="#corpusPerspectives">
                        <i class="fa fa-sliders" aria-hidden="true"></i> Add/remove perspectives
                    </button>
                </div>
                <div class="panel-body">
                    <div class="tableContent administration-table-wrapper corpus-settings-perspectives-table-wrapper">
                        <table class="tablesorter table table-striped table-hover administration-table corpus-settings-perspectives-table" id="corpus_set_corpus_perspective_roles" cellspacing="1">
                            <thead>
                            <tr>
                                <th>User</th>
                                {foreach from=$corpus_perspectivs key=id item=perspectiv}
                                    <th perspective_id="{$id}" title="{$perspectiv.title|escape}">{$perspectiv.title}</th>
                                {/foreach}
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$users_roles key=user_id item=user}
                                <tr id="{$user_id}">
                                    <th><span class="corpus-settings-perspectives-user" title="{$user.screename|escape}">{$user.screename}</span></th>
                                    {foreach from=$corpus_perspectivs key=perspectiv item=perspectiv_data}
                                        {if $perspectiv_data.access eq 'role'}
                                            {if isset($users_perspectives.$user_id)}
                                                <td perspective_id="{$perspectiv}" class="corpus-settings-perspectives-access-cell {if in_array($perspectiv, $users_perspectives.$user_id)}corpus-settings-perspectives-access-cell-active{/if}">
                                                    <label class="corpus-settings-perspectives-checkbox">
                                                        <input {if in_array($perspectiv, $users_perspectives.$user_id)}checked="checked"{/if} class="userReportPerspective" type="checkbox" user_id="{$user_id}" perspective_id="{$perspectiv}" value="1" />
                                                        <span aria-hidden="true"></span>
                                                    </label>
                                            {else}
                                                <td perspective_id="{$perspectiv}" class="corpus-settings-perspectives-access-cell">
                                                    <label class="corpus-settings-perspectives-checkbox">
                                                        <input class="userReportPerspective" type="checkbox" user_id="{$user_id}" perspective_id="{$perspectiv}" value="1" />
                                                        <span aria-hidden="true"></span>
                                                    </label>
                                            {/if}
                                        {else}
                                            <td perspective_id="{$perspectiv}" class="corpus-settings-perspectives-access-cell">
                                                <span class="corpus-settings-perspectives-access-badge">{$perspectiv_data.access}</span>
                                        {/if}
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

<div class="modal fade settingsModal administration-form-modal corpus-settings-perspectives-modal" id="corpusPerspectives" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-sliders" aria-hidden="true"></i> Edit corpus access</h4>
            </div>
            <div class="modal-body" id="corpusPerspectivesContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmAccess" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

{if $users_roles|@count == 0}
    <div class="corpus-settings-perspectives-empty">
        <i class="fa fa-info-circle" aria-hidden="true"></i>
        <span>No other users have access to this corpus (<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=users">manage users</a>).</span>
    </div>
{/if}
