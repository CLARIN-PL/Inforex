<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-4 tableContainer" id = "annotationSetsContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Annotation sets</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="table table-striped" id="annotationSetsTable" cellspacing="1">
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
                                <tr visibility = "{$set.public}" {if $set.access != null}class = "edit_access"{/if}>
                                    <td class="column_id td-right">{$set.id}</td>
                                    <td>{$set.name}</td>
                                    <td><div class = "annotation_description">{$set.description}</div></td>
                                    <td class="td-center set_owner" id = {$set.user_id}>{$set.screename}</td>
                                    <td class="td-center">{if $set.access != null}edit{else}{if $set.public == 1} public {else} private {/if}{/if}</td>
                                </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer" element="annotation_set" >
                    <button type = "button" class = "btn btn-primary create create_annotation_set" data-toggle="modal" data-target="#create_annotation_set_modal">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit edit_annotation_set" data-toggle="modal" data-target="#edit_annotation_set_modal">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-danger deleteAnnotations ">Delete</button>
                    <button style = "display: none; float: right;" type = "button" class = "btn btn-info shareAnnotationSet" title = "Allow specified users to use and modify this annotation set" data-toggle="modal" data-target="#share_annotation_set_modal">Share with other users</button>
                </div>
            </div>
        </div>
        <div class="col-md-3" style="padding: 0">
            <div class="panel panel-primary tableContainer scrollingWrapper" id="annotationSubsetsContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">Annotation subsets</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationSubsetsTable" class="table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th style="width: 50px;" class="td-right">Id</th>
                                <th>Name</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer" element="annotation_subset" parent="annotationSetsContainer">
                    <button type = "button" class = "btn btn-primary create adminPanelButton create_annotation_subset" data-toggle="modal" data-target="#create_annotation_subset_modal">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton edit_annotation_subset" data-toggle="modal" data-target="#edit_annotation_subset_modal">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-danger deleteAnnotations adminPanelButton">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-5" style="padding: 0">
            <div class="panel panel-primary tableContainer scrollingWrapper" id="annotationTypesContainer" style="margin: 5px; visibility: hidden;">
                <div class="panel-heading">Categories</div>
                <div class="panel-body">
                    <div class="tableContent scrolling">
                        <table id="annotationTypesTable" class="table table-striped" cellspacing="1">
                            <thead>
                            <tr>
                                <th style="width: 150px">Symbolic name</th>
                                <th title="short description" style="width: 150px">Display name</th>
                                <th>Description</th>
                                <th>Default visibility</th>
                                <th style="display:none">Style</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-footer" element="annotation_type" parent="annotationSubsetsContainer">
                    <button type = "button" class = "btn btn-primary create adminPanelButton create_annotation_type" data-toggle="modal" data-target="#create_annotation_type_modal">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit adminPanelButton edit_annotation_type" data-toggle="modal" data-target="#edit_annotation_type_modal">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-danger deleteAnnotations adminPanelButton">Delete</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade settingsModal" id="create_annotation_set_modal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create annotation set</h4>
                </div>
                <div class="modal-body">
                    <form id = "create_annotation_sets_form">
                        <div class="form-group">
                            <label for="create_annotation_set_name">Name: <span class = "required_field">*</span></label>
                            <input class="form-control" name = "create_annotation_set_name" id="create_annotation_set_name">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_set_description">Description: </label>
                            <textarea class="form-control" name = "create_annotation_set_description" rows="5" id="create_annotation_set_description"></textarea>
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
                    <button type="button" class="btn btn-primary confirm_annotation_set">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade settingsModal" id="edit_annotation_set_modal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit annotation set</h4>
                </div>
                <div class="modal-body">
                    <form id = "edit_annotation_sets_form">
                        <div class="form-group">
                            <label for="edit_annotation_set_name">Name: <span class = "required_field">*</span></label>
                            <input class="form-control" name = "edit_annotation_set_name" id="edit_annotation_set_name">
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_set_description">Description: <span class = "required_field">*</span></label>
                            <textarea class="form-control" name = "edit_annotation_set_description" rows = "5" id="edit_annotation_set_description"></textarea>
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
                    <button type="button" class="btn btn-primary confirm_annotation_set">Confirm</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade settingsModal" id="create_annotation_subset_modal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create annotation subset</h4>
                </div>
                <div class="modal-body">
                    <form id = "create_annotation_subsets_form">
                        <div class="form-group">
                            <label for="create_annotation_subset_name">Name: <span class = "required_field">*</span></label>
                            <input class="form-control" name = "create_annotation_subset_name" rows="5" id="create_annotation_subset_name">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_subset_description">Description: </label>
                            <textarea class="form-control" name = "create_annotation_subset_description" rows="5" id="create_annotation_subset_description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary confirm_annotation_subset">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade settingsModal" id="edit_annotation_subset_modal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit annotation subset</h4>
                </div>
                <div class="modal-body">
                    <form id = "edit_annotation_subsets_form">
                        <div class="form-group">
                            <label for="edit_annotation_subset_name">Name: <span class = "required_field">*</span></label>
                            <textarea class="form-control" name = "edit_annotation_subset_name" rows="5" id="edit_annotation_subset_name"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_subset_description">Description: </label>
                            <textarea class="form-control" name = "edit_annotation_subset_description" rows="5" id="edit_annotation_subset_description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary confirm_annotation_subset">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade settingsModal" id="create_annotation_type_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create annotation type</h4>
                </div>
                <div class="modal-body">
                    <form id = "create_annotation_types_form">
                        <div class="form-group">
                            <label for="create_annotation_type_name">Symbolic name: <span class = "required_field">*</span></label>
                            <input class="form-control" type = "text" name = "create_annotation_type_name" id="create_annotation_type_name">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_type_short">Display name:</label>
                            <input class="form-control" type = "text" id="create_annotation_type_short">
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_type_desc">Description:</label>
                            <textarea class="form-control" rows="5" id="create_annotation_type_desc"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="create_elementVisibility">Default visibility:</label>
                            <select id="create_elementVisibility" class = "form-control">
                                <option value = "Hidden">Hidden</option>
                                <option value = "Visible">Visibile</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="create_annotation_type_css">Style:</label><br/>

                            <div class="panel panel-primary">
                                <table class="table">
                                    <tr>
                                        <td style="vertical-align: middle"><i>Predefined:</i></td>
                                        <td style="vertical-align: middle">
                                            <ul class="list-inline" id="create_predefined-styles" style="margin-top: 10px">
                                                <li><span class="annotation" style="background: #FFB878; border: 1px solid #E67E22">Style 1</span></li>
                                                <li><span class="annotation" style="background: #DDB9EB; border: 1px solid #9C59B6">Style 2</span></li>
                                                <li><span class="annotation" style="background: #85C4ED; border: 1px solid #3499DB">Style 3</span></li>
                                                <li><span class="annotation" style="background: #7EE7AC; border: 1px solid #2ecc71">Style 4</span></li>
                                                <li><span class="annotation" style="background: #FF998E; border: 1px solid #e74c3c">Style 5</span></li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top"><i>CSS:</i></td>
                                        <td style="vertical-align: top"><textarea class="form-control" rows="3" id="create_annotation_type_css"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: middle"><i>Preview:</i></td>
                                        <td style="vertical-align: middle"><span id="create_annotation-style-preview">annotation style preview</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary confirm_annotation_type">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade settingsModal" id="edit_annotation_type_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit annotation type</h4>
                </div>
                <div class="modal-body">
                    <form id = "edit_annotation_types_form">
                        <div class="form-group">
                            <label for="edit_annotation_type_name">Symbolic name: <span class = "required_field">*</span></label>
                            <input class="form-control" type = "text" name = "edit_annotation_type_name" id="edit_annotation_type_name">
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_type_short">Display name:</label>
                            <input class="form-control" type = "text" id="edit_annotation_type_short">
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_type_desc">Description:</label>
                            <textarea class="form-control" rows="5" id="edit_annotation_type_desc"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit_elementVisibility">Default visibility:</label>
                            <select id="edit_elementVisibility" class = "form-control">
                                <option value = "Hidden">Hidden</option>
                                <option value = "Visible">Visibile</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_annotation_type_css">Style:</label><br/>

                            <div class="panel panel-primary">
                                <table class="table">
                                    <tr>
                                        <td style="vertical-align: middle"><i>Predefined:</i></td>
                                        <td style="vertical-align: middle">
                                            <ul class="list-inline" id="edit_predefined-styles" style="margin-top: 10px">
                                                <li><span class="annotation" style="background: #FFB878; border: 1px solid #E67E22">Style 1</span></li>
                                                <li><span class="annotation" style="background: #DDB9EB; border: 1px solid #9C59B6">Style 2</span></li>
                                                <li><span class="annotation" style="background: #85C4ED; border: 1px solid #3499DB">Style 3</span></li>
                                                <li><span class="annotation" style="background: #7EE7AC; border: 1px solid #2ecc71">Style 4</span></li>
                                                <li><span class="annotation" style="background: #FF998E; border: 1px solid #e74c3c">Style 5</span></li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top"><i>CSS:</i></td>
                                        <td style="vertical-align: top"><textarea class="form-control" rows="3" id="edit_annotation_type_css"></textarea></td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: middle"><i>Preview:</i></td>
                                        <td style="vertical-align: middle"><span id="edit_annotation-style-preview">annotation style preview</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary confirm_annotation_type">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade settingsModal" id="share_annotation_set_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Share annotation set</h4>
                </div>
                <div class="modal-body" style = "height: 65vh; overflow: auto;">
                    <div class="navbar navbar-default">
                        <div class="navbar-collapse collapse" id="searchbar">
                            <form class="navbar-form search-form">
                                <div class="form-group" style="display:inline;">
                                    <div class="input-group text-center" style="display:table;">
                                        <span class="input-group-addon" style="width:1%;"><span class="glyphicon glyphicon-search"></span></span>
                                        <input class="form-control search_input" name="search" placeholder="Search Here" autocomplete="off" autofocus="autofocus" type="text">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <table class = "table table-striped">
                        <thead>
                        <th>user</th>
                        <th>login</th>
                        <th class = "text-center">access</th>
                        </thead>
                        <tbody id = "share_annotation_set_table">
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
        </div>
    </div>