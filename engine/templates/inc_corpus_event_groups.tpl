{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables corpus-settings-event-groups">
    <div class="row corpus-settings-event-groups-grid">
        <div class="col-md-10 col-md-offset-1 corpus-settings-event-groups-column">
        <div class="panel administration-content-panel corpus-settings-event-groups-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
                <span>Event groups</span>
            </div>
            <div class="panel-body">
                <div class="administration-table-wrapper corpus-settings-event-groups-table-wrapper">
                <table class="tablesorter table table-striped table-hover administration-table corpus-settings-event-groups-table" id="corpus_set_corpus_event_groups" cellspacing="1">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Use</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$eventList item=set}
                        <tr>
                            <td class="corpus-settings-event-group-id">{$set.id}</td>
                            <td><span class="corpus-settings-event-group-name">{$set.name}</span></td>
                            <td><span class="corpus-settings-event-group-description" title="{$set.description|escape}">{$set.description}</span></td>
                            <td class="corpus-settings-event-group-use-cell {if $set.cid}corpus-settings-event-group-use-cell-active{/if}">
                                <label class="corpus-settings-event-group-checkbox" title="Use event group">
                                    <input class="setEventGroup" type="checkbox" event_group_id="{$set.id}" {if $set.cid} checked="checked" {/if}/>
                                    <span aria-hidden="true"></span>
                                    <span class="sr-only">Use event group</span>
                                </label>
                            </td>
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
