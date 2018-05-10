{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
{include file="inc_header2.tpl"}
<div class = "col-md-3">
    <div class = "panel panel-primary">
        <div class = "panel-heading">Filters</div>
        <div class = "panel-body scrollingWrapper">
            <div class = "scrolling">
                <div class = "form-group">
                    <label for = "user">User:</label>
                    <select class = "form-control" id ="user_filter" name = "user">
                        <option value = "-">-select-</option>
                        {foreach from=$users item=user}
                            <option value = "{$user.user_id}">{$user.screename}</option>
                        {/foreach}
                    </select>
                </div>
                <div class = "form-group">
                    <label for = "flag">Flag:</label>
                    <select class = "form-control" id ="flag_filter" name = "flag">
                        <option value = "-">-select-</option>
                        {foreach from=$flags item=flag}
                            <option value = "{$flag.corpora_flag_id}">{$flag.short}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
        </div>
        <div class = "panel-footer clearfix">
            <button id = "apply_history_filters" class = "btn btn-primary" style = "float: right;">Apply</button>
        </div>
    </div>
</div>
<div class = "col-md-9">
    <div class="panel panel-primary">
        <div class="panel-heading">Flag history</div>
        <div class="panel-body scrollingWrapper">
            <div class="scrolling">
                <table id="flag_history" class="table table-striped" cellspacing="0" cellpadding="0">
                    <thead>
                    <tr>
                        <th>Document</th>
                        <th>Flag</th>
                        <th>Old status</th>
                        <th>New status</th>
                        <th>User</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$flag_history item=flag}
                        <tr>
                            <td>
                                <a href="index.php?page=report&amp;corpus={$flag.corpus_id}&amp;subpage=preview&amp;id={$flag.report_id}">{$flag.report_name}</a>
                            </td>
                            <td>{$flag.flag}</td>
                            <td>
                                <img src="gfx/flag_{if $flag.old_status != null}{$flag.old_status_id}{else}-1{/if}.png" style="padding-top: 1px" title = "{$flag.old_status}">
                            </td>
                            <td><img src="gfx/flag_{$flag.new_status_id}.png" style="padding-top: 1px" title = "{$flag.new_status}"></td>
                            <td>{$flag.screename}</td>
                            <td>{$flag.date}</td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
        <div class = "panel-footer clearfix" style = "height: 54px;">
        </div>
    </div>
</div>

<br style="clear: both;"/>

{include file="inc_footer.tpl"}