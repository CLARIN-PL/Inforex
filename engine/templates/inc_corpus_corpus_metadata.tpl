{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width:40%;">
            <div class="panel-heading">Metadata</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="table table-striped" id="extListContainer" cellspacing="1">
                    <thead>
                    <tr>
                        <th>Field</th>
                        <th>Column id</th>
                        <th>Comment</th>
                        <th>Type</th>
                        <th>Default</th>
                        <th class="text-center" style="width: 200px;">Possible values</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach from=$extList item=set}
                        <tr>
                            {if $set.field_name == null}
                                <td>{$set.field}</td>
                            {else}
                                <td>{$set.field_name}</td>
                            {/if}
                            <td>{$set.field}</td>
                            {if $set.comment == null}
                                <td>-</td>
                            {else}
                                <td>{$set.comment}</td>
                            {/if}
                            <td>{$set.type}</td>
                            <td>{$set.default}</td>
                            <td class="text-center">
                                {if !empty($set.field_values)}
                                    <select class="form-control">
                                        <option>-values-</option>
                                        {foreach from = $set.field_values item = value}
                                            <option>{$value}</option>
                                        {/foreach}
                                    </select>
                                {else}
                                    -
                                {/if}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer tableOptions" element="ext" parent="extListContainer">
                <button type="button" class="ext_edit btn btn-primary" action="add" data-toggle="modal"
                        data-target="#create_metadata_modal">Create
                </button>
                <button style="display: none;" type="button" class="edit_metadata btn btn-primary">Edit</button>
                <button style="display: none;" type="button" class="delete_metadata btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="create_metadata_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create metadata element</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger create_metadata_error text-center" style="display: none;">
                    <strong>At least one enum value is required</strong>
                </div>
                <form id="create_metadata_form">
                    <div class="form-group">
                        <label for="create_metadata_field">Field name: <span class="required_field">*</span></label>
                        <input class="form-control" name="create_metadata_field" id="create_metadata_field"
                               placeholder="Name of the column">
                    </div>
                    <div class="form-group">
                        <label for="create_metadata_column_id">Column id: <span class="required_field">*</span></label>
                        <input class="form-control" name="create_metadata_column_id" id="create_metadata_column_id"
                               placeholder="Name of the column in the database">
                    </div>
                    <div class="form-group">
                        <label for="create_metadata_comment">Comment: </label>
                        <input class="form-control" name="create_metadata_comment" id="create_metadata_comment"
                               placeholder="Description of the field.">
                    </div>
                    <div class="form-group">
                        <label for="create_metadata_type">Type:</label>
                        <select id="create_metadata_type" class="form-control metadata_type">
                            <option class="edit_metadata_value_text" value="text">Text</option>
                            <option class="edit_metadata_value_enum" value="enum">Enum</option>
                        </select>
                    </div>
                    <div class="enum_values_edition" style="display: none;">
                        <div class="form-group">
                            <div style="float: right;">
                                <button type="button" value="add" class="btn btn-primary add_enum">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                </button>
                                <button type="button" value="add" class="btn btn-danger remove_enum">
                                    <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group" style="clear: both;">
                            <label style="margin-top: 10px;" for="enum_values">Enum values: (use + and - buttons to add
                                more values)</label>
                            <div id="enum_values" class="enum_values">
                                <input class="form-control enum_input">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="create_metadata_null">Default value:</label>
                        <div class="radio">
                            <label><input type="radio" checked="checked" class = "null_select" name="create_metadata_default_value"
                                          value="null">Empty</label>
                        </div>
                        <div class="radio">
                            <label class = "enum_options">
                                <input type="radio" class = "enum_select" name="create_metadata_default_value" value = "enum">
                                <div id="create_default_options"></div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_metadata">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="edit_metadata_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit metadata element</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger edit_metadata_error text-center" style="display: none;">
                    <strong>At least one enum value is required</strong>
                </div>
                <form id="edit_metadata_form">
                    <div class="form-group">
                        <label for="edit_metadata_field">Field name: <span class="required_field">*</span></label>
                        <input class="form-control" name="edit_metadata_field" id="edit_metadata_field"
                               placeholder="Name of the field">
                    </div>
                    <div class="form-group">
                        <label for="edit_metadata_column_id">Column id: <span class="required_field">*</span></label>
                        <input class="form-control" disabled name="edit_metadata_column_id" id="edit_metadata_column_id"
                               placeholder="Name of the column in the database">
                    </div>
                    <div class="form-group">
                        <label for="edit_metadata_comment">Comment: <span class="required_field">*</span></label>
                        <input class="form-control" name="edit_metadata_comment" id="edit_metadata_comment"
                               placeholder="Column description">
                    </div>
                    <div class="form-group">
                        <label for="edit_metadata_type">Type:</label>
                        <select id="edit_metadata_type" class="form-control metadata_type">
                            <option value="text">Text</option>
                            <option value="enum">Enum</option>
                        </select>
                    </div>
                    <div class="edit_enum_values_edition" style="display: none;">
                        <div class="form-group">
                            <div style="float: right;">
                                <button type="button" value="edit" class="btn btn-primary add_enum">
                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                </button>
                                <button type="button" value="edit" class="btn btn-danger remove_enum">
                                    <i class="fa fa-minus-circle" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group" style="clear: both;">
                            <label style="margin-top: 10px;" for="edit_enum_values">Enum values: (use + and - buttons to
                                add more values)</label>
                            <div id="edit_enum_values" class="edit_enum_values">
                                <input class="form-control edit_enum_input" value="null">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_metadata_null">Default value:</label>
                        <div class="radio">
                            <label><input type="radio" checked="checked" value="null" id="edit_metadata_default_empty"
                                          name="edit_metadata_default_value">Empty</label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" id="edit_metadata_default_select" name="edit_metadata_default_value"
                                       value="enum">
                                <div id="edit_default_options"></div>
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_edit_metadata">Confirm</button>
            </div>
        </div>
    </div>
</div>


{* <button type="button" class="ext_edit btn btn-primary" action="add_table" style="{if $extList}display:none{/if}">Add custom metadata</button> *}