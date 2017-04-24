{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-4 tableContainer" id = "eventGroupsContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Event groups</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="tablesorter table table-striped" id="eventGroupsTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th>id</th>
                            <th>name</th>
                            <th>description</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$eventGroups item=group}
                            <tr>
                                <td class = "column_id">{$group.id}</td>
                                <td>{$group.name}</td>
                                <td>{$group.description}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer" element="event_group">
                    <button type = "button" class = "btn btn-primary create">Create</button>
                    <button type = "button" class = "btn btn-primary edit">Edit</button>
                    <button type = "button" class = "btn btn-primary delete">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper tableContainer" id="eventTypesContainer" style="margin: 5px; display: none;">
                <div class="panel-heading">Relation types</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="eventTypesTable" class="tablesorter table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer" element="event_type" parent="eventGroupsContainer">
                    <button type = "button" class = "btn btn-primary create">Create</button>
                    <button type = "button" class = "btn btn-primary edit">Edit</button>
                    <button type = "button" class = "btn btn-primary delete">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper tableContainer" id="eventTypeSlotsContainer" style="margin: 5px; display: none;">
                <div class="panel-heading">Event type slots</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="eventTypeSlotsTable" class="tablesorter table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>name</th>
                                <th>description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer" element="event_type_slot" parent="eventTypesContainer">
                    <button type = "button" class = "btn btn-primary create">Create</button>
                    <button type = "button" class = "btn btn-primary edit">Edit</button>
                    <button type = "button" class = "btn btn-primary delete">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
{include file="inc_footer.tpl"}
