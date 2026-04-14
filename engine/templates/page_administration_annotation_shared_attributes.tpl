{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-shared-attributes">
    <div class="row administration-shared-grid">

        <div class="col-md-6 tableContainer administration-shared-column" id="sharedAttributesContainer">
            <div class="panel scrollingWrapper administration-content-panel administration-shared-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-share-alt" aria-hidden="true"></i></span>
                    <span>Shared attributes</span>
                </div>
                <div class="panel-body scrolling">
                    <table id="sharedAttributesTable" class="table table-striped administration-table administration-shared-table" cellspacing="1">
                        <thead>
                        <tr>
                            <th class="num administration-shared-id-column">Id</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
			{if isset($sharedAttributes)}
                        {foreach from=$sharedAttributes item=shared_attribute}
                            <tr>
                                <td class="num">{$shared_attribute.id}</td>
                                <td>{$shared_attribute.name}</td>
                                <td><span class="administration-type-label administration-type-label-{$shared_attribute.type|escape}">{$shared_attribute.type}</span></td>
                                <td><div class="administration-description-preview" title="{$shared_attribute.description|escape}">{$shared_attribute.description}</div></td>
                            </tr>
                        {/foreach}
			{/if}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer administration-content-footer administration-shared-footer" element="annotation_subset" parent="annotationSetsContainer">
                    <button type = "button" id="create_shared_attribute"  class = "btn btn-primary create">Create</button>
                    <button type = "button" id="manage_annotations"  class = "btn btn-primary manage" disabled="disabled">Manage annotations</button>
                    <button id="delete_shared_attribute" type = "button" class = "btn btn-danger delete" disabled="disabled">Delete</button>
                </div>
            </div>
        </div>

        <div class="col-md-6 tableContainer administration-shared-column" id="sharedAttributesEnumContainer">
            <div class="panel scrollingWrapper administration-content-panel administration-shared-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-list-ul" aria-hidden="true"></i></span>
                    <span>Shared attribute values</span>
                </div>
                <div class="panel-body scrolling">
                    <table id="sharedAttributesEnumTable" class="table table-striped administration-table administration-shared-table" cellspacing="1">
                        <thead>
                            <tr>
                                <th class="num administration-shared-id-column">No.</th>
                                <th class="value">Value</th>
                                <th class="description">Description</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer administration-content-footer administration-shared-footer" element="annotation_type" parent="annotationSubsetsContainer">
                    <button type="button" id="create_shared_attribute_enum" class="btn btn-primary create" disabled="disabled">Create</button>
                    <button type="button" id="edit_shared_attribute_enum" class="btn btn-primary edit" disabled="disabled">Edit</button>
                    <button type="button" id="delete_shared_attribute_enum" class="btn btn-danger delete" disabled="disabled">Delete</button>
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

<div class="modal fade settingsModal administration-form-modal" id="create_shared_attribute_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-share-alt" aria-hidden="true"></i> Create shared attribute</h4>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_create_shared_attribute">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="create_shared_attribute_enum_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-list-ul" aria-hidden="true"></i> Create shared attribute value</h4>
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_create_shared_attribute_enum">Save</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="edit_shared_attribute_enum_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i> Edit shared attribute value</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_shared_attribute_enum_form">
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary save_edit_shared_attribute_enum">Save</button>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
