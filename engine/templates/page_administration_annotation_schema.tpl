{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-schema">
    <div class="row administration-schema-grid">
        <div class="col-md-4 tableContainer administration-schema-column" id="annotationSetsContainer">
            <div class="panel scrollingWrapper administration-content-panel administration-schema-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
                    <span>Annotation sets</span>
                </div>
                <div class="tableContent panel-body scrolling">
                    <table class="tablesorter table table-striped administration-table administration-schema-table" id="annotationSetsTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th style="width: 10%" class="td-right">Id</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class="td-center">Owner</th>
                            <th class="td-center">Access</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$annotationSets item=set}
                            <tr visibility="{$set.public}">
                                <td class="column_id td-right">{$set.id}</td>
                                <td>{$set.name}</td>
                                <td>
                                    <div class="annotation_description administration-description-preview" title="{$set.description|escape}">{$set.description}</div>
                                </td>
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
                <div class="panel-footer administration-content-footer administration-schema-footer" element="annotation_set">
                    <button type="button" class="btn btn-primary create create_annotation_set" data-toggle="modal"
                            data-target="#create_annotation_set_modal">Create
                    </button>
                    <button style="display: none; " type="button" class="btn btn-primary edit edit_annotation_set"
                            data-toggle="modal" data-target="#edit_annotation_set_modal">Edit
                    </button>
                    <button style="display: none; " type="button" title="Assign annotation set to corpora"
                            class="btn btn-primary edit edit_annotation_set_corpora" data-toggle="modal"
                            data-target="#edit_annotation_set_corpora_modal">Corpora
                    </button>
                    <button style="display: none; " type="button" class="btn btn-danger delete">Delete</button>
                </div>
            </div>
        </div>

        <div class="col-md-4 tableContainer administration-schema-column" id="annotationSubsetsContainer"
                 style="visibility: hidden;">
            <div class="panel scrollingWrapper administration-content-panel administration-schema-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
                    <span>Annotation subsets</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationSubsetsTable" class="tablesorter table table-striped administration-table administration-schema-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th class="td-right administration-schema-id-column">Id</th>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer administration-schema-footer" element="annotation_subset" parent="annotationSetsContainer">
                    <button type="button" class="btn btn-primary create adminPanelButton create_annotation_subset"
                            data-toggle="modal" data-target="#create_annotation_subset_modal">Create
                    </button>
                    <button style="display: none;" type="button"
                            class="btn btn-primary edit adminPanelButton edit_annotation_subset" data-toggle="modal"
                            data-target="#edit_annotation_subset_modal">Edit
                    </button>
                    <button style="display: none;" type="button" class="btn btn-danger delete adminPanelButton">Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4 tableContainer administration-schema-column" id="annotationTypesContainer"
                 style="visibility: hidden;">
            <div class="panel scrollingWrapper administration-content-panel administration-schema-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-bookmark" aria-hidden="true"></i></span>
                    <span>Categories</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationTypesTable" class="tablesorter table table-striped administration-table administration-schema-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th class="administration-schema-symbol-column">Symbolic name</th>
                                <th class="administration-schema-display-column" title="short description">Display name</th>
                                <th>Description</th>
                                <th>Visibility</th>
                                <th style="display:none">Style</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer administration-schema-footer" element="annotation_type" parent="annotationSubsetsContainer">
                    <button type="button" class="btn btn-primary create adminPanelButton create_annotation_type"
                            data-toggle="modal" data-target="#create_annotation_type_modal">Create
                    </button>
                    <button style="display: none;" type="button"
                            class="btn btn-primary edit adminPanelButton edit_annotation_type" data-toggle="modal"
                            data-target="#edit_annotation_type_modal">Edit
                    </button>
                    <button style="display: none;" type="button" class="btn btn-danger delete adminPanelButton">Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="create_annotation_set_modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-tags" aria-hidden="true"></i> Create annotation set</h4>
            </div>
            <div class="modal-body">
                <form id="create_annotation_sets_form">
                    <div class="form-group">
                        <label for="create_annotation_set_name">Name: <span class="required_field">*</span></label>
                        <input class="form-control" name="create_annotation_set_name" id="create_annotation_set_name">
                    </div>
                    <div class="form-group">
                        <label for="create_annotation_set_description">Description: </label>
                        <textarea class="form-control" name="create_annotation_set_description" rows="5"
                                  id="create_annotation_set_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="create_setAccess">Access:</label>
                        <select id="create_setAccess" class="form-control">
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_annotation_set">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="edit_annotation_set_modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-tags" aria-hidden="true"></i> Edit annotation set</h4>
            </div>
            <div class="modal-body">
                <form id="edit_annotation_sets_form">
                    <div class="form-group">
                        <label for="edit_annotation_set_name">Name: <span class="required_field">*</span></label>
                        <input class="form-control" name="edit_annotation_set_name" id="edit_annotation_set_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_annotation_set_description">Description: <span class="required_field">*</span></label>
                        <textarea class="form-control" name="edit_annotation_set_description" rows="5"
                                  id="edit_annotation_set_description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_setAccess">Access:</label>
                        <select id="edit_setAccess" class="form-control">
                            <option value="public">Public</option>
                            <option value="private">Private</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_annotation_set">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade settingsModal administration-form-modal" id="create_annotation_subset_modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-sitemap" aria-hidden="true"></i> Create annotation subset</h4>
            </div>
            <div class="modal-body">
                <form id="create_annotation_subsets_form">
                    <div class="form-group">
                        <label for="create_annotation_subset_name">Name: <span class="required_field">*</span></label>
                        <input class="form-control" name="create_annotation_subset_name" rows="5"
                               id="create_annotation_subset_name">
                    </div>
                    <div class="form-group">
                        <label for="create_annotation_subset_description">Description: </label>
                        <textarea class="form-control" name="create_annotation_subset_description" rows="5"
                                  id="create_annotation_subset_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_annotation_subset">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="edit_annotation_subset_modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-sitemap" aria-hidden="true"></i> Edit annotation subset</h4>
            </div>
            <div class="modal-body">
                <form id="edit_annotation_subsets_form">
                    <div class="form-group">
                        <label for="edit_annotation_subset_name">Name: <span class="required_field">*</span></label>
                        <textarea class="form-control administration-compact-textarea" name="edit_annotation_subset_name" rows="3"
                                  id="edit_annotation_subset_name"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_annotation_subset_description">Description: </label>
                        <textarea class="form-control administration-compact-textarea" name="edit_annotation_subset_description" rows="3"
                                  id="edit_annotation_subset_description"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_annotation_subset">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="create_annotation_type_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-bookmark" aria-hidden="true"></i> Create annotation type</h4>
            </div>
            <div class="modal-body">
                <form id="create_annotation_types_form">
                    <div class="form-group">
                        <label for="create_annotation_type_name">Symbolic name: <span
                                    class="required_field">*</span></label>
                        <input class="form-control" type="text" name="create_annotation_type_name"
                               id="create_annotation_type_name">
                    </div>
                    <div class="form-group">
                        <label for="create_annotation_type_short">Display name:</label>
                        <input class="form-control" type="text" id="create_annotation_type_short">
                    </div>
                    <div class="form-group">
                        <label for="create_annotation_type_desc">Description:</label>
                        <textarea class="form-control administration-compact-textarea" rows="3" id="create_annotation_type_desc"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="create_elementVisibility">Default visibility:</label>
                        <select id="create_elementVisibility" class="form-control">
                            <option value="Hidden">Hidden</option>
                            <option value="Visible">Visibile</option>
                        </select>
                    </div>
                    <div class="form-group administration-style-editor">
                        <label for="create_annotation_type_css">Style:</label>
                        <div class="administration-style-card">
                            <div class="administration-style-row">
                                <span class="administration-style-label">Predefined</span>
                                <ul class="list-inline administration-style-presets" id="create_predefined-styles">
                                    <li><span class="annotation" style="background: #FFB878; border: 1px solid #E67E22">Style 1</span></li>
                                    <li><span class="annotation" style="background: #DDB9EB; border: 1px solid #9C59B6">Style 2</span></li>
                                    <li><span class="annotation" style="background: #85C4ED; border: 1px solid #3499DB">Style 3</span></li>
                                    <li><span class="annotation" style="background: #7EE7AC; border: 1px solid #2ecc71">Style 4</span></li>
                                    <li><span class="annotation" style="background: #FF998E; border: 1px solid #e74c3c">Style 5</span></li>
                                </ul>
                            </div>
                            <div class="administration-style-row administration-style-row-stack">
                                <label class="administration-style-label" for="create_annotation_type_css">Custom CSS</label>
                                <textarea class="form-control administration-css-textarea" rows="2" id="create_annotation_type_css"></textarea>
                            </div>
                            <div class="administration-style-row">
                                <span class="administration-style-label">Preview</span>
                                <span class="administration-style-preview" id="create_annotation-style-preview">annotation style preview</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_annotation_type">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal" id="edit_annotation_type_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-bookmark" aria-hidden="true"></i> Edit annotation type</h4>
            </div>
            <div class="modal-body">
                <form id="edit_annotation_types_form">
                    <div class="form-group">
                        <label for="edit_annotation_type_name">Symbolic name: <span
                                    class="required_field">*</span></label>
                        <input class="form-control" type="text" name="edit_annotation_type_name"
                               id="edit_annotation_type_name">
                    </div>
                    <div class="form-group">
                        <label for="edit_annotation_type_short">Display name:</label>
                        <input class="form-control" type="text" id="edit_annotation_type_short">
                    </div>
                    <div class="form-group">
                        <label for="edit_annotation_type_desc">Description:</label>
                        <textarea class="form-control administration-compact-textarea" rows="3" id="edit_annotation_type_desc"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_elementVisibility">Default visibility:</label>
                        <select id="edit_elementVisibility" class="form-control">
                            <option value="Hidden">Hidden</option>
                            <option value="Visible">Visibile</option>
                        </select>
                    </div>
                    <div class="form-group administration-style-editor">
                        <label for="edit_annotation_type_css">Style:</label>
                        <div class="administration-style-card">
                            <div class="administration-style-row">
                                <span class="administration-style-label">Predefined</span>
                                <ul class="list-inline administration-style-presets" id="edit_predefined-styles">
                                    <li><span class="annotation" style="background: #FFB878; border: 1px solid #E67E22">Style 1</span></li>
                                    <li><span class="annotation" style="background: #DDB9EB; border: 1px solid #9C59B6">Style 2</span></li>
                                    <li><span class="annotation" style="background: #85C4ED; border: 1px solid #3499DB">Style 3</span></li>
                                    <li><span class="annotation" style="background: #7EE7AC; border: 1px solid #2ecc71">Style 4</span></li>
                                    <li><span class="annotation" style="background: #FF998E; border: 1px solid #e74c3c">Style 5</span></li>
                                </ul>
                            </div>
                            <div class="administration-style-row administration-style-row-stack">
                                <label class="administration-style-label" for="edit_annotation_type_css">Custom CSS</label>
                                <textarea class="form-control administration-css-textarea" rows="2" id="edit_annotation_type_css"></textarea>
                            </div>
                            <div class="administration-style-row">
                                <span class="administration-style-label">Preview</span>
                                <span class="administration-style-preview" id="edit_annotation-style-preview">annotation style preview</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_annotation_type">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade settingsModal administration-form-modal administration-wide-modal administration-corpora-modal" id="edit_annotation_set_corpora_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-exchange" aria-hidden="true"></i> Assign annotation schema to corpora</h4>
            </div>
            <div class="modal-body scrollingWrapper">
                <div class="row administration-corpora-grid">
                    <div class="col-md-5 administration-corpora-column">
                        <div class="panel tableContainer administration-content-panel administration-corpora-panel" id="annotationSetsCorporaContainer"
                             style="visibility: hidden;">
                            <div class="panel-heading administration-corpora-heading">
                                <span class="administration-content-heading-icon"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
                                <span>Assigned corpora</span>
                            </div>
                            <div class="panel-body">
                                <div class="tableContent scrolling">
                                    <table id="annotationSetsCorporaTable" class="tablesorter table table-striped administration-table administration-corpora-table"
                                           cellspacing="1">
                                        <thead>
                                        <tr>
                                            <th class="administration-corpora-id-column">Id</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 administration-corpora-actions">
                        <button type="button" class="btn move assign"><i class="fa fa-angle-left" aria-hidden="true"></i> Assign</button>
                        <button type="button" class="btn move unassign">Unassign <i class="fa fa-angle-right" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-md-5 administration-corpora-column">
                        <div class="panel tableContainer administration-content-panel administration-corpora-panel" id="corpusContainer"
                             style="visibility: hidden;">
                            <div class="panel-heading administration-corpora-heading">
                                <span class="administration-content-heading-icon"><i class="fa fa-folder-open-o" aria-hidden="true"></i></span>
                                <span>Available corpora</span>
                            </div>
                            <div class="panel-body">
                                <div class="administration-corpora-filter">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                                    <input type="text" class="form-control" id="available_corpora_filter" placeholder="Filter available corpora">
                                </div>
                                <div class="tableContent scrolling">
                                    <table id="corpusTable" class="tablesorter table table-striped administration-table administration-corpora-table" cellspacing="1">
                                        <thead>
                                        <tr>
                                            <th class="administration-corpora-id-column">Id</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
