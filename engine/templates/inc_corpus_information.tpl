{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables corpus-settings-information">
    <div class="row corpus-settings-information-grid">
        <div class="col-md-8 col-md-offset-2 corpus-settings-information-column">
        <div class="panel administration-content-panel corpus-settings-information-panel">
            <div class="panel-heading administration-content-heading">
                <span class="administration-content-heading-icon"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
                <span>Basic information</span>
            </div>
            <div class="panel-body">
                <div class="administration-table-wrapper corpus-settings-information-table-wrapper">
                <table class="table table-striped table-hover administration-table corpus-settings-information-table" id="corpusElementsContainer" cellspacing="1">
                    <tbody>
                    <tr>
                        <th id="name">Name</th>
                        <td><span class="corpus-settings-value corpus-settings-name">{$corpus.name}</span></td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td class="corpus-settings-actions-cell">
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary btn-xs editBasicInfo editBasicInfoName corpus-settings-edit-button" title="Edit name">
                                        <i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">Edit name</span>
                                    </a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="user_id">Owner</th>
                        <td><span class="corpus-settings-value corpus-settings-owner">{$owner.screename}</span></td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td class="corpus-settings-actions-cell">
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary btn-xs editBasicInfo editBasicInfoOwner corpus-settings-edit-button" data-toggle="modal" data-target="#basicInfoOwner" title="Edit owner">
                                        <i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">Edit owner</span>
                                    </a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="public">Access</th>
                        <td>
                            {if $corpus.public}
                                <span class="corpus-settings-access corpus-settings-access-public">public</span>
                            {else}
                                <span class="corpus-settings-access corpus-settings-access-restricted">restricted</span>
                            {/if}
                        </td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td class="corpus-settings-actions-cell">
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary btn-xs editBasicInfo editBasicInfoAccess corpus-settings-edit-button" data-toggle="modal" data-target="#basicInfoAccess" title="Edit access">
                                        <i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">Edit access</span>
                                    </a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="description">Description</th>
                        <td><span class="corpus-settings-value corpus-settings-description">{$corpus.description}</span></td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td class="corpus-settings-actions-cell">
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" type="button" class="btn btn-primary btn-xs editBasicInfo editBasicInfoDescription corpus-settings-edit-button" data-toggle="modal" data-target="#basicInfoDescription" title="Edit description">
                                        <i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">Edit description</span>
                                    </a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="css">CSS</th>
                        <td id="cssValue">{$corpus.css}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td class="corpus-settings-actions-cell">
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" type="button" class="btn btn-primary btn-xs editBasicInfo editBasicInfoCss corpus-settings-edit-button" data-toggle="modal" data-target="#basicInfoCss" title="Edit CSS">
                                        <i class="fa fa-pencil" aria-hidden="true"></i><span class="sr-only">Edit CSS</span>
                                    </a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="date_created">Created</th>
                        <td><span class="corpus-settings-date"><i class="fa fa-calendar-o" aria-hidden="true"></i> {$corpus.date_created}</span></td>
                        {if isCorpusOwner() || "admin"|has_role}<td class="corpus-settings-actions-cell"></td>{/if}
                    </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-modal" id="basicInfoNameModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i> Change corpus name</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_corpus_name_form">
                    <div class="form-group">
                        <label for="nameDescription">Name <span class = "required_field">*</span></label>
                        <textarea class="form-control administration-compact-textarea" rows="3" name = "nameDescription" id="nameDescription"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmName">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-modal" id="basicInfoOwner" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user" aria-hidden="true"></i> Edit corpus owner</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label>Owner</label>
                        <div id = "basicInfoOwnerSelect">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button"  class="btn btn-primary confirmOwner" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-modal" id="basicInfoAccess" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-lock" aria-hidden="true"></i> Edit corpus access</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group" id = "basicInfoAccessSelect">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmAccess" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade settingsModal administration-form-modal corpus-settings-modal" id="basicInfoDescription" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-align-left" aria-hidden="true"></i> Edit corpus description</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group" id = "corpusDescriptionArea">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmDescription" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade settingsModal administration-form-modal corpus-settings-modal corpus-settings-css-modal" id="basicInfoCss" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-code" aria-hidden="true"></i> Edit corpus custom CSS</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group" id="corpusCssArea">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmCss" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>
