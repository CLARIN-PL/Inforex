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
                    <button type = "button" class = "btn btn-primary create createEventGroup" data-toggle="modal" data-target="#create_event_modal">Create</button>
                    <button type = "button" class = "btn btn-primary edit editEventGroup" style = "display: none;" data-toggle="modal" data-target="#edit_event_modal" >Edit</button>
                    <button type = "button" class = "btn btn-danger delete" style = "display: none;">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-4" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper tableContainer" id="eventTypesContainer" style="margin: 5px; display: none;">
                <div class="panel-heading">Event types</div>
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
                    <button type = "button" class = "btn btn-primary create createEventType" data-toggle="modal" data-target="#create_event_type_modal">Create</button>
                    <button type = "button" class = "btn btn-primary edit editEventType" style = "display: none;" data-toggle="modal" data-target="#edit_event_type_modal">Edit</button>
                    <button type = "button" class = "btn btn-danger delete" style = "display: none;">Delete</button>
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
                    <button type = "button" class = "btn btn-primary create createEventTypeSlot" data-toggle="modal" data-target="#create_event_type_slot_modal">Create</button>
                    <button type = "button" class = "btn btn-primary edit editEventTypeSlot" style = "display: none;" data-toggle="modal" data-target="#edit_event_type_slot_modal">Edit</button>
                    <button type = "button" class = "btn btn-danger delete" style = "display: none;">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="create_event_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create event group</h4>
            </div>
            <div class="modal-body">
                <form id = "create_event_form">
                    <div class="form-group">
                        <label for="create_event_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_event_name" id="create_event_name">
                    </div>
                    <div class="form-group">
                        <label for="create_event_description">Description: </label>
                        <textarea class="form-control" name = "create_event_description" rows="5" id="create_event_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_event">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="edit_event_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit event group</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_event_form">
                    <div class="form-group">
                        <label for="edit_event_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "edit_event_name" id="edit_event_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_event_description">Description: </label>
                        <textarea class="form-control" name = "edit_event_description" rows="5" id="edit_event_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_edit_event">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="create_event_type_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create event type</h4>
            </div>
            <div class="modal-body">
                <form id = "create_event_type_form">
                    <div class="form-group">
                        <label for="create_event_type_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_event_type_name" id="create_event_type_name">
                    </div>
                    <div class="form-group">
                        <label for="create_event_type_description">Description: </label>
                        <textarea class="form-control" name = "create_event_type_description" rows="5" id="create_event_type_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_event_type">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="edit_event_type_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit event type</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_event_type_form">
                    <div class="form-group">
                        <label for="edit_event_type_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "edit_event_type_name" id="edit_event_type_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_event_type_description">Description: </label>
                        <textarea class="form-control" name = "edit_event_type_description" rows="5" id="edit_event_type_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_edit_event_type">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="create_event_type_slot_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create event type slot</h4>
            </div>
            <div class="modal-body">
                <form id = "create_event_type_slot_form">
                    <div class="form-group">
                        <label for="create_event_type_slot_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_event_type_slot_name" id="create_event_type_slot_name">
                    </div>
                    <div class="form-group">
                        <label for="create_event_type_slot_description">Description: </label>
                        <textarea class="form-control" name = "create_event_type_slot_description" rows="5" id="create_event_type_slot_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_event_type_slot">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="edit_event_type_slot_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit event type slot</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_event_type_slot_form">
                    <div class="form-group">
                        <label for="edit_event_type_slot_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "edit_event_type_slot_name" id="edit_event_type_slot_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_event_type_slot_description">Description: </label>
                        <textarea class="form-control" name = "edit_event_type_slot_description" rows="5" id="edit_event_type_slot_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_edit_event_type_slot">Confirm</button>
            </div>
        </div>
    </div>
</div>


{include file="inc_footer.tpl"}
