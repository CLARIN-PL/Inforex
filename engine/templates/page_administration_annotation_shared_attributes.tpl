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

        <div class="col-md-6 scrollingWrapper">
            <div class="panel panel-primary tableContainer" id="sharedAttributesContainer" style="margin: 5px;">
                <div class="panel-heading">Shared attributes</div>
                <div class="panel-body scrolling" style="padding: 0">
                    <table id="sharedAttributesTable" class="table table-striped" cellspacing="1">
                        <thead>
                        <tr>
                            <th class="num">Id</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$sharedAttributes item=shared_attribute}
                            <tr>
                                <td class="num">{$shared_attribute.id}</td>
                                <td>{$shared_attribute.name}</td>
                                <td>{$shared_attribute.type}</td>
                                <td>{$shared_attribute.description}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer" element="annotation_subset" parent="annotationSetsContainer">
                    <button style="float: right" id="delete_shared_attribute" type = "button" class = "btn btn-danger" disabled="disabled">Delete</button>
                    <button type = "button" id="create_shared_attribute"  class = "btn btn-primary">Create</button>
                    <button type = "button" id="manage_annotations"  class = "btn btn-primary" disabled="disabled">Manage annotations</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 scrollingWrapper">
            <div class="panel panel-primary tableContainer" id="sharedAttributesEnumContainer" style="margin: 5px; ">
                <div class="panel-heading">Shared attribute values</div>
                <div class="panel-body scrolling" style="padding: 0">
                    <table id="sharedAttributesEnumTable" class="table table-striped" cellspacing="1">
                        <thead>
                            <tr>
                                <th class="num">No.</th>
                                <th class="value">Value</th>
                                <th class="description">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer" element="annotation_type" parent="annotationSubsetsContainer">
                    <button type="button" id="delete_shared_attribute_enum" class="btn btn-danger" disabled="disabled" style="float: right">Delete</button>
                    <button type="button" id="create_shared_attribute_enum" class="btn btn-primary" disabled="disabled">Create</button>
                    <button type="button" id="edit_shared_attribute_enum" class="btn btn-primary" disabled="disabled">Edit</button>
                </div>
            </div>
        </div>

    </div>
</div>

        {*
            <div class="panel panel-primary tableContainer" id="annotationTypesAttachedContainer" style="margin: 5px;">
                <div class="panel-heading">Annotation types attached</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationTypesAttachedTable" class="table table-striped" cellspacing="1">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>name</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer">
                    <button id="move_detach" type = "button" class = "btn btn-primary move unassign"> >>> </button>
                </div>
            </div>
        </div>
*}

        {*
                    <div class="panel panel-primary tableContainer" id="annotationTypesDetachedContainer" style="margin: 5px; visibility: visible;">
                        <div class="panel-heading">Annotation types detached</div>
                        <div class="panel-body">
                            <div class="tableContent scrolling">
                                <table id="annotationTypesDetachedTable" class="table table-striped" cellspacing="1">
                                    <thead>
                                    <tr>
                                        <th>id</th>
                                        <th>name</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button id="move_attach" type = "button" class = "btn btn-primary"> <<< </button>
                        </div>
                    </div>
                </div>
        *}

<div class="modal fade settingsModal" id="create_shared_attribute_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create shared attribute</h4>
            </div>
            <div class="modal-body">
                <form id = "create_shared_attribute_form">
                    <div class="form-group">
                        <label for="create_shared_attribute_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_shared_attribute_name" id="create_shared_attribute_name">
                    </div>
                    <div class="form-group">
                        <label for="create_shared_attribute_type">Type: </label>
                        <select class="form-control" name = "create_shared_attribute_type" id="create_shared_attribute_type">
                            <option value = "string">string</option>
                            <option value = "enum">enum</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="create_shared_attribute_description">Description: </label>
                        <input class="form-control" name = "create_shared_attribute_description" id="create_shared_attribute_description">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_shared_attribute">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="create_shared_attribute_enum_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create shared attribute value</h4>
            </div>
            <div class="modal-body">
                <form id = "create_shared_attribute_enum_form">
                    <div class="form-group">
                        <label for="create_shared_attribute_enum_value">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_shared_attribute_enum_value" id="create_shared_attribute_enum_value">
                    </div>
                    <div class="form-group">
                        <label for="create_shared_attribute_enum_description">Description: </label>
                        <input class="form-control" name = "create_shared_attribute_enum_description" id="create_shared_attribute_enum_description">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_shared_attribute_enum">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="edit_shared_attribute_enum_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create shared attribute value</h4>
            </div>
            <div class="modal-body">
                <form id = "create_shared_attribute_enum_form">
                    <input type="hidden" name="edit_shared_attribute_id"/>
                    <div class="form-group">
                        <label for="create_shared_attribute_enum_value">Old name: <span class = "required_field"></span></label>
                        <input class="form-control" name="edit_shared_attribute_enum_old_value" readonly="readonly">
                    </div>
                    <div class="form-group">
                        <label for="create_shared_attribute_enum_value">New name: <span class = "required_field"></span></label>
                        <input class="form-control" name="edit_shared_attribute_enum_new_value">
                    </div>
                    <div class="form-group">
                        <label for="create_shared_attribute_enum_description">Description: </label>
                        <input class="form-control" name="edit_shared_attribute_enum_description">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary save_edit_shared_attribute_enum">Save</button>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}