{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<table class = "admin_tables">
    <tr>
        <td style="width: 25%; vertical-align: top; padding: 5px; " id="eventGroupsContainer" class="tableContainer">
            <div class="panel panel-primary scrollingWrapper">
                <div class="panel-heading">Event groups</div>
                <div class="panel-body scrolling">
                    <div class="tableContent bigTableContent">
                        <table id = "eventGroupsTable" class="table table-striped" id="public" cellspacing="1">
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
                    <div class="tableOptions" element="event_group" style = "margin-top: 10px; text-align:center;">
                        <button type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                        <button type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                        <button type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                    </div>
                </div>
            </div>
        </td>
        <td style="width: 25%; vertical-align: top">
            <div id="eventTypesContainer" class="tableContainer panel panel-primary scrollingWrapper" style="display: none;">
                <div class="panel-heading">Event types</div>
                <div class="panel-body scrolling">
                    <div class="tableContent">
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
                    <div class="tableOptions" element="event_type" parent="eventGroupsContainer" style = "margin-top: 10px; text-align:center;">
                        <button style = "display: none;" type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                        <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                        <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                    </div>
                </div>
            </div>
        </td>
        <td style="width: 25%; vertical-align: top">
            <div id="eventTypeSlotsContainer" class="tableContainer panel panel-primary scrollingWrapper" style="display: none;">
                <div class="panel-heading">Event types</div>
                <div class="panel-body scrolling">
                    <div class="tableContent">
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
                    <div class="tableOptions" element="event_type_slot" parent="eventTypesContainer" style = "margin-top: 10px; text-align:center;">
                        <button style = "display: none;" type = "button" class = "btn btn-primary create adminPanelButton">Create</button>
                        <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton">Edit</button>
                        <button style = "display: none;" type = "button" class = "btn btn-primary delete adminPanelButton">Delete</button>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

{include file="inc_administration_bottom.tpl"}         
{include file="inc_footer.tpl"}
