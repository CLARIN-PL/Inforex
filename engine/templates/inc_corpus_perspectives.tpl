{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables" style="margin: 5px;">
    <div class = "row">
        <input type = "button" class="btn btn-primary" id="reportPerspectives" value="Add/remove perspectives" style = "margin-bottom: 5px; float: right;" data-toggle="modal" data-target="#corpusPerspectives">
    </div>
    <div class="row">
        <div class="panel panel-primary scrollingWrapper">
            <div class="panel-heading">Access to perspectives</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="tablesorter table table-striped" id="corpus_set_corpus_perspective_roles" cellspacing="1">
                    <thead>
                    <tr>
                        <th>User</th>
                        {foreach from=$corpus_perspectivs key=id item=perspectiv}
                            <th perspective_id="{$id}">{$perspectiv.title}</th>
                        {/foreach}
                    </tr>
                    </thead>
                    <tbody>
                    {*
                    <tr id={$user_id}>
                        <th>owner</th>
                        {foreach from=$corpus_perspectivs key=perspectiv item=perspectiv_data}
                        <td style="text-align: center;">
                        <input class="userReportPerspective" type="checkbox" value="1" readonly="readonly" checked="checked"/>
                        </td>
                        {/foreach}
                    </tr>
                    *}
                    {foreach from=$users_roles key=user_id item=user}
                        <tr id={$user_id}>
                            <th>{$user.screename}</th>
                            {foreach from=$corpus_perspectivs key=perspectiv item=perspectiv_data}
                                {if $perspectiv_data.access eq 'role'}
                                    {if isset($users_perspectives.$user_id)}
                                        <td perspective_id="{$perspectiv}" style="text-align: center;{if in_array($perspectiv, $users_perspectives.$user_id)} background: #9DD943;{/if}">
                                            <input {if in_array($perspectiv, $users_perspectives.$user_id)}checked="checked"{/if} class="userReportPerspective" type="checkbox" user_id="{$user_id}" perspective_id="{$perspectiv}" value="1" />
                                            {else}
                                        <td perspective_id="{$perspectiv}" style="text-align: center;">
                                        <input class="userReportPerspective" type="checkbox" user_id="{$user_id}" perspective_id="{$perspectiv}" value="1" />
                                    {/if}
                                {else}
                                    <td perspective_id="{$perspectiv}" style="text-align: center;">
                                    <i>{$perspectiv_data.access}</i>
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


<div class="modal fade settingsModal" id="corpusPerspectives" role="dialog" style = "margin: 0;">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content scrollingWrapper">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit corpus access</h4>
            </div>
            <div class="modal-body scrolling" id = "corpusPerspectivesContent">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirmAccess" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


{if $users_roles|@count == 0}
<div>
<i>No other users have access to this corpus (<a href="index.php?page=corpus&amp;corpus={$corpus.id}&amp;subpage=users">manage users</a>).</i>
</div>
{/if}
