<div class="container-fluid admin_tables corpus-settings-custom-annotation-sets">
    <div class="row corpus-settings-custom-annotation-sets-grid">
        <div class="col-md-4 tableContainer corpus-settings-custom-annotation-sets-column" id="annotationSetsContainer">
            <div class="panel administration-content-panel corpus-settings-custom-annotation-sets-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
                    <span>Annotation sets</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent administration-table-wrapper corpus-settings-custom-annotation-sets-table-wrapper">
                        <table class="table table-striped table-hover administration-table corpus-settings-custom-annotation-sets-table corpus-settings-custom-annotation-sets-main-table" id="annotationSetsTable" cellspacing="1">
                            <thead>
                            <tr>
                                <th class="td-right">ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th class="td-center">Owner</th>
                                <th class="td-center">Access</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$annotationSets item=set}
                                <tr visibility="{$set.public}" {if $set.access != null}class="edit_access"{/if}>
                                    <td class="column_id td-right"><span class="corpus-settings-custom-annotation-set-id">{$set.id}</span></td>
                                    <td><span class="corpus-settings-custom-annotation-set-name">{$set.name}</span></td>
                                    <td><span class="annotation_description corpus-settings-custom-annotation-set-description" title="{$set.description|escape}">{$set.description}</span></td>
                                    <td class="td-center set_owner" id="{$set.user_id}" data-owner-name="{$set.screename|escape}">
                                        {if $set.screename}<span class="corpus-settings-custom-annotation-set-owner" title="{$set.screename|escape}">{$set.screename}</span>{/if}
                                    </td>
                                    <td class="td-center">
                                        {if $set.access != null}
                                            <span class="corpus-settings-custom-annotation-set-access corpus-settings-custom-annotation-set-access-edit">edit</span>
                                        {else}
                                            {if $set.public == 1}
                                                <span class="corpus-settings-custom-annotation-set-access corpus-settings-custom-annotation-set-access-public">public</span>
                                            {else}
                                                <span class="corpus-settings-custom-annotation-set-access corpus-settings-custom-annotation-set-access-private">private</span>
                                            {/if}
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer corpus-settings-custom-annotation-sets-footer" element="annotation_set">
                    <button type="button" class="btn btn-primary create create_annotation_set" data-toggle="modal" data-target="#create_annotation_set_modal">
                        <i class="fa fa-plus" aria-hidden="true"></i> Create
                    </button>
                    <button style="display: none;" type="button" class="btn btn-primary edit edit_annotation_set" data-toggle="modal" data-target="#edit_annotation_set_modal">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </button>
                    <button style="display: none;" type="button" class="btn btn-danger deleteAnnotations">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                    </button>
                    <button style="display: none;" type="button" class="btn btn-info shareAnnotationSet" title="Allow specified users to use and modify this annotation set" data-toggle="modal" data-target="#share_annotation_set_modal">
                        <i class="fa fa-share-alt" aria-hidden="true"></i> Share
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-3 tableContainer corpus-settings-custom-annotation-sets-column" id="annotationSubsetsContainer" style="visibility: hidden;">
            <div class="panel administration-content-panel corpus-settings-custom-annotation-sets-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-folder-open" aria-hidden="true"></i></span>
                    <span>Annotation subsets</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent administration-table-wrapper corpus-settings-custom-annotation-sets-table-wrapper">
                        <table id="annotationSubsetsTable" class="table table-striped table-hover administration-table corpus-settings-custom-annotation-sets-table corpus-settings-custom-annotation-sets-subsets-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th class="td-right">ID</th>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer corpus-settings-custom-annotation-sets-footer" element="annotation_subset" parent="annotationSetsContainer">
                    <button type="button" class="btn btn-primary create adminPanelButton create_annotation_subset" data-toggle="modal" data-target="#create_annotation_subset_modal">
                        <i class="fa fa-plus" aria-hidden="true"></i> Create
                    </button>
                    <button style="display: none;" type="button" class="btn btn-primary edit adminPanelButton edit_annotation_subset" data-toggle="modal" data-target="#edit_annotation_subset_modal">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </button>
                    <button style="display: none;" type="button" class="btn btn-danger deleteAnnotations adminPanelButton">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-5 tableContainer corpus-settings-custom-annotation-sets-column" id="annotationTypesContainer" style="visibility: hidden;">
            <div class="panel administration-content-panel corpus-settings-custom-annotation-sets-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-bookmark" aria-hidden="true"></i></span>
                    <span>Categories</span>
                </div>
                <div class="panel-body">
                    <div class="tableContent administration-table-wrapper corpus-settings-custom-annotation-sets-table-wrapper">
                        <table id="annotationTypesTable" class="table table-striped table-hover administration-table corpus-settings-custom-annotation-sets-table corpus-settings-custom-annotation-sets-types-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th>Symbolic name</th>
                                <th title="short description">Display name</th>
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
                <div class="panel-footer administration-content-footer corpus-settings-custom-annotation-sets-footer" element="annotation_type" parent="annotationSubsetsContainer">
                    <button type="button" class="btn btn-primary create adminPanelButton create_annotation_type" data-toggle="modal" data-target="#create_annotation_type_modal">
                        <i class="fa fa-plus" aria-hidden="true"></i> Create
                    </button>
                    <button style="display: none;" type="button" class="btn btn-primary edit adminPanelButton edit_annotation_type" data-toggle="modal" data-target="#edit_annotation_type_modal">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </button>
                    <button style="display: none;" type="button" class="btn btn-danger deleteAnnotations adminPanelButton">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade settingsModal administration-form-modal corpus-settings-custom-annotation-sets-modal" id="create_annotation_set_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus" aria-hidden="true"></i> Create annotation set</h4>
                </div>
                <div class="modal-body">
                    <form id="create_annotation_sets_form">
                        <div class="form-group">
                            <label for="create_annotation_set_name">Name <span class="required_field">*</span></label>
                            <input class="form-control" name="create_annotation_set_name" id="create_annotation_set_name">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_set_description">Description</label>
                            <textarea class="form-control administration-compact-textarea" name="create_annotation_set_description" rows="4" id="create_annotation_set_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="create_setAccess">Access</label>
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

    <div class="modal fade settingsModal administration-form-modal corpus-settings-custom-annotation-sets-modal" id="edit_annotation_set_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i> Edit annotation set</h4>
                </div>
                <div class="modal-body">
                    <form id="edit_annotation_sets_form">
                        <div class="form-group">
                            <label for="edit_annotation_set_name">Name <span class="required_field">*</span></label>
                            <input class="form-control" name="edit_annotation_set_name" id="edit_annotation_set_name">
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_set_description">Description <span class="required_field">*</span></label>
                            <textarea class="form-control administration-compact-textarea" name="edit_annotation_set_description" rows="4" id="edit_annotation_set_description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_setAccess">Access</label>
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

    <div class="modal fade settingsModal administration-form-modal corpus-settings-custom-annotation-sets-modal" id="create_annotation_subset_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus" aria-hidden="true"></i> Create annotation subset</h4>
                </div>
                <div class="modal-body">
                    <form id="create_annotation_subsets_form">
                        <div class="form-group">
                            <label for="create_annotation_subset_name">Name <span class="required_field">*</span></label>
                            <input class="form-control" name="create_annotation_subset_name" id="create_annotation_subset_name">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_subset_description">Description</label>
                            <textarea class="form-control administration-compact-textarea" name="create_annotation_subset_description" rows="4" id="create_annotation_subset_description"></textarea>
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

    <div class="modal fade settingsModal administration-form-modal corpus-settings-custom-annotation-sets-modal" id="edit_annotation_subset_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i> Edit annotation subset</h4>
                </div>
                <div class="modal-body">
                    <form id="edit_annotation_subsets_form">
                        <div class="form-group">
                            <label for="edit_annotation_subset_name">Name <span class="required_field">*</span></label>
                            <input class="form-control" name="edit_annotation_subset_name" id="edit_annotation_subset_name">
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_subset_description">Description</label>
                            <textarea class="form-control administration-compact-textarea" name="edit_annotation_subset_description" rows="4" id="edit_annotation_subset_description"></textarea>
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

    <div class="modal fade settingsModal administration-form-modal corpus-settings-custom-annotation-sets-modal corpus-settings-custom-annotation-sets-style-modal" id="create_annotation_type_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-plus" aria-hidden="true"></i> Create annotation type</h4>
                </div>
                <div class="modal-body">
                    <form id="create_annotation_types_form">
                        <div class="form-group">
                            <label for="create_annotation_type_name">Symbolic name <span class="required_field">*</span></label>
                            <input class="form-control" type="text" name="create_annotation_type_name" id="create_annotation_type_name">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_type_short">Display name</label>
                            <input class="form-control" type="text" id="create_annotation_type_short">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_type_desc">Description</label>
                            <textarea class="form-control administration-compact-textarea" rows="3" id="create_annotation_type_desc"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="create_elementVisibility">Visibility</label>
                            <select id="create_elementVisibility" class="form-control">
                                <option value="Hidden">Hidden</option>
                                <option value="Visible" selected="selected">Visible</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_type_css">Style</label>
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
                                    <span class="administration-style-label">CSS</span>
                                    <textarea class="form-control administration-css-textarea" rows="3" id="create_annotation_type_css"></textarea>
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

    <div class="modal fade settingsModal administration-form-modal corpus-settings-custom-annotation-sets-modal corpus-settings-custom-annotation-sets-style-modal" id="edit_annotation_type_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i> Edit annotation type</h4>
                </div>
                <div class="modal-body">
                    <form id="edit_annotation_types_form">
                        <div class="form-group">
                            <label for="edit_annotation_type_name">Symbolic name <span class="required_field">*</span></label>
                            <input class="form-control" type="text" name="edit_annotation_type_name" id="edit_annotation_type_name">
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_type_short">Display name</label>
                            <input class="form-control" type="text" id="edit_annotation_type_short">
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_type_desc">Description</label>
                            <textarea class="form-control administration-compact-textarea" rows="3" id="edit_annotation_type_desc"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_elementVisibility">Visibility</label>
                            <select id="edit_elementVisibility" class="form-control">
                                <option value="Hidden">Hidden</option>
                                <option value="Visible">Visible</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_type_css">Style</label>
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
                                    <span class="administration-style-label">CSS</span>
                                    <textarea class="form-control administration-css-textarea" rows="3" id="edit_annotation_type_css"></textarea>
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

    <div class="modal fade settingsModal administration-form-modal corpus-settings-custom-annotation-sets-share-modal" id="share_annotation_set_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"><i class="fa fa-share-alt" aria-hidden="true"></i> Share annotation set</h4>
                </div>
                <div class="modal-body">
                    <div class="corpus-settings-custom-annotation-sets-search">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search" aria-hidden="true"></i></span>
                            <input class="form-control search_input" name="search" placeholder="Search users" autocomplete="off" autofocus="autofocus" type="text">
                        </div>
                    </div>
                    <div class="administration-table-wrapper corpus-settings-custom-annotation-sets-share-table-wrapper">
                        <table class="table table-striped table-hover administration-table corpus-settings-custom-annotation-sets-share-table">
                            <thead>
                            <tr>
                                <th>User</th>
                                <th>Login</th>
                                <th class="text-center">Access</th>
                            </tr>
                            </thead>
                            <tbody id="share_annotation_set_table">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
