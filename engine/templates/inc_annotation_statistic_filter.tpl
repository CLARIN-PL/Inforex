{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div class="panel panel-default">
    <div class="panel-heading clearfix">
        <h5 style="float: left;">Annotation filters</h5>
    </div>
    <div class="panel-body scrolling" style="padding: 5px">
        {capture name=link_ext_filters assign=link_ext_filters}
            {foreach from=$filters item=filter}
                {if $filter.selected}&amp;filter_{$filter.name}={$filter.selected}{/if}
            {/foreach}
        {/capture}
        <table class="table table-stripped" cellspacing="1">
            <tr>
                <th>Stage:</th>
                <td>
                    <select name="annotation_stage" class="annotation_stage" style="font-size: 12px">
                        <option value="-"
                                style="font-style: italic">Select stage</option>
                        <option value="new" {if 'new' == $selected_filters.annotation.stage}selected="selected"{/if}
                                style="font-style: italic">New
                        </option>
                        <option value="agreement" {if 'agreement'==$selected_filters.annotation.stage} selected="selected"{/if}
                                style="font-style: italic">Agreement
                        </option>
                        <option value="bootstrapping" {if 'bootstrapping'==$selected_filters.annotation.stage} selected="selected"{/if}
                                style="font-style: italic">Bootstrapping
                        </option>
                        <option value="final" {if 'final'==$selected_filters.annotation.stage} selected="selected"{/if}
                                style="font-style: italic">Final
                        </option>
                    </select>
                    {if $stage_set}
                        <i class="fa fa-times cancel_stage" aria-hidden="true"></i>
                    {/if}
                </td>
            </tr>
            <tr>
                <th>User:</th>
                <td>
                    <select name="annotation_user" class="annotation_user" style="font-size: 12px">
                        <option value="-" style="font-style: italic">Select user</option>
                        {foreach from=$corpus_users item=user}
                        <option value="{$user.user_id}" {if $user.user_id==$selected_filters.annotation.user} selected="selected"{/if}
                                style="font-style: italic"> {$user.screename}
                        </option>
                        {/foreach}
                    </select>
                    {if $user_set}
                        <i class="fa fa-times cancel_user" aria-hidden="true"></i>
                    {/if}
                </td>
            </tr>
        </table>
    </div>
</div>

