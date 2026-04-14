{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-relation-schema">
    <div class="row administration-relation-grid">
        <div class="col-md-5 tableContainer administration-relation-column" id = "relationSetsContainer">
                <div class="panel scrollingWrapper administration-content-panel administration-relation-panel">
                    <div class="panel-heading administration-content-heading">
                        <span class="administration-content-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
                        <span>Relation sets</span>
                    </div>
                    <div class="tableContent panel-body scrolling">
                        <table class="tablesorter table table-striped administration-table administration-relation-table" id="relationSetsTable" cellspacing="1">
                            <thead>
                            <tr>
                                <th class="administration-relation-id-column">ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Owner</th>
                                <th>Access</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$relationSets item=set}
                                <tr visibility = "{$set.public}">
                                    <td class = "column_id">{$set.id}</td>
                                    <td>{$set.name}</td>
                                    <td><div class="administration-description-preview" title="{$set.description|escape}">{$set.description}</div></td>
                                    <td class="td-center">
                                        <span class="administration-owner-initials" title="{$set.screename|escape}">{$set.owner_initials|escape}</span>
                                    </td>
                                    <td class="td-center">
                                        {if $set.public == 1}
                                            <span class="administration-access-label administration-access-label-public">public</span>
                                        {else}
                                            <span class="administration-access-label administration-access-label-private">private</span>
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-footer administration-content-footer administration-relation-footer" element="relation_set" parent="relationSetsContainer">
                        <button type = "button" class = "btn btn-primary create adminPanelButton createRelationSet" data-toggle="modal" data-target="#create_relation_set_modal">Create</button>
                        <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton editRelationSet" data-toggle="modal" data-target="#edit_relation_set_modal">Edit</button>
                        <button style = "display: none;" type = "button" class = "btn btn-danger delete adminPanelButton">Delete</button>
                    </div>
                </div>
        </div>
        <div class="col-md-4 tableContainer administration-relation-column" id="relationTypesContainer" style="display: none;">
            <div class="panel scrollingWrapper administration-content-panel administration-relation-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-link" aria-hidden="true"></i></span>
                    <span>Relation types</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="relationTypesTable" class="tablesorter table table-striped administration-table administration-relation-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th class="administration-relation-id-column">Id</th>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer administration-relation-footer" element="relation_type" parent="relationSetsContainer">
                    <button type = "button" class = "btn btn-primary create adminPanelButton createRelation" data-toggle="modal" data-target="#create_relation_modal">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton editRelation" data-toggle="modal" data-target="#edit_relation_modal">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-danger delete adminPanelButton">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-3 tableContainer administration-relation-column" id="relationGroupsContainer" style="display: none;">
            <div class="panel scrollingWrapper administration-content-panel administration-relation-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-object-group" aria-hidden="true"></i></span>
                    <span>Relation groups</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="relationGroupsTable" class="tablesorter table table-striped administration-table administration-relation-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th>Direction</th>
                                <th>Annotation</th>
                            </tr>
                            </thead>
                            <tbody id = "relations_groups_content">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer administration-relation-footer" element="relation_type" parent="relationTypesContainer">
                    <button type = "button" class = "btn btn-primary create adminPanelButton createRelationGroup" data-toggle="modal" data-target="#create_relation_group_modal">Edit relation groups</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="create_relation_set_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-sitemap" aria-hidden="true"></i> Create relation set</h4>
            </div>
            <div class="modal-body">
                <form id = "create_relation_set_form">
                    <div class="form-group">
                        <label for="create_relation_set_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_relation_set_name" id="create_relation_set_name">
                    </div>
                    <div class="form-group">
                        <label for="create_relation_set_description">Description: </label>
                        <textarea class="form-control" name = "create_relation_set_description" rows="5" id="create_relation_set_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="create_setAccess">Access:</label>
                        <select id="create_setAccess" class = "form-control">
                            <option value = "public">Public</option>
                            <option value = "private">Private</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_relation_set_create">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="edit_relation_set_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-sitemap" aria-hidden="true"></i> Edit relation set</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_relation_set_form">
                    <div class="form-group">
                        <label for="edit_relation_set_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "edit_relation_set_name" id="edit_relation_set_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_relation_set_description">Description: </label>
                        <textarea class="form-control" name = "edit_relation_set_description" rows = "5" id="edit_relation_set_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_setAccess">Access:</label>
                        <select id="edit_setAccess" class = "form-control">
                            <option value = "public">Public</option>
                            <option value = "private">Private</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_relation_set_edit">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade settingsModal administration-form-modal" id="create_relation_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-link" aria-hidden="true"></i> Create relation type</h4>
            </div>
            <div class="modal-body">
                <form id = "create_relation_form">
                    <div class="form-group">
                        <label for="create_relation_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_relation_name" id="create_relation_name">
                    </div>
                    <div class="form-group">
                        <label for="create_relation_description">Description: </label>
                        <textarea class="form-control" name = "create_relation_description" rows="5" id="create_relation_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_relation_create">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="edit_relation_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-link" aria-hidden="true"></i> Edit relation type</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_relation_form">
                    <div class="form-group">
                        <label for="edit_relation_name">Name: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "edit_relation_name" id="edit_relation_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_relation_description">Description: </label>
                        <textarea class="form-control" name = "edit_relation_description" rows = "5" id="edit_relation_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_relation_edit">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal administration-wide-modal administration-relation-group-modal" id="create_relation_group_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-object-group" aria-hidden="true"></i> Edit relation groups</h4>
            </div>
            <div class="modal-body">
                <form id = "create_relation_group_form" class="administration-relation-group-form">
                    <div class = "col-lg-4 administration-relation-group-column">
                        <table class="relationGroupManagement table table-striped administration-table administration-relation-group-table">
                            <thead>
                                <tr>
                                    <th>Annotation set</th>
                                    <th>Description</th>
                                    <th class = "text-center">Source</th>
                                    <th class = "text-center">Target</th>
                                </tr>
                            </thead>
                            <tbody id = "relation_group_annotation_set">
                            </tbody>
                        </table>
                    </div>
                    <div class = "col-lg-4 administration-relation-group-column">
                        <table class="relationGroupManagement table table-striped administration-table administration-relation-group-table">
                            <thead>
                            <tr>
                                <th>Annotation subset</th>
                                <th>Description</th>
                                <th class = "text-center">Source</th>
                                <th class = "text-center">Target</th>
                            </tr>
                            </thead>
                            <tbody id = "relation_group_annotation_subset">
                            </tbody>
                        </table>
                    </div>
                    <div class = "col-lg-4 administration-relation-group-column">
                        <table class="relationGroupManagement table table-striped administration-table administration-relation-group-table">
                            <thead>
                            <tr>
                                <th>Annotation type</th>
                                <th>Description</th>
                                <th class = "text-center">Source</th>
                                <th class = "text-center">Target</th>
                            </tr>
                            </thead>
                            <tbody id = "relation_group_annotation_type">
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
