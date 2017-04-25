{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width:40%;">
            <div class="panel-heading">Event groups</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="tablesorter table table-striped" id="corpus_set_corpus_event_groups" cellspacing="1">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Assign</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$eventList item=set}
                        <tr>
                            <td>{$set.id}</td>
                            <td>{$set.name}</td>
                            <td>{$set.description}</td>
                            <td><input class="setEventGroup" type="checkbox" event_group_id="{$set.id}" {if $set.cid} checked="checked" {/if}/></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>