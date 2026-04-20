{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables corpus-settings-flags">
    <div class="row corpus-settings-flags-grid">
        <div class="col-md-12 corpus-settings-flags-column">
            <div class="panel administration-content-panel corpus-settings-flags-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-flag" aria-hidden="true"></i></span>
                    <span>Flags</span>
                </div>
                <div class="panel-body">
                    <div class="administration-table-wrapper corpus-settings-flags-table-wrapper">
                    <table class="table table-striped table-hover administration-table corpus-settings-flags-table" id="flagsListContainer" cellspacing="1">
                        <thead>
                        <tr>
                            <th class="col-num">ID</th>
                            <th>Name</th>
                            <th>Short</th>
                            <th>Description</th>
                            <th class="col-num">Sort</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$flagsList item=set}
                            <tr>
                                <td class="corpus-settings-flags-id">{$set.id}</td>
                                <td class="name"><span class="corpus-settings-flags-name">{$set.name}</span></td>
                                <td class="short"><span class="corpus-settings-flags-short">{$set.short}</span></td>
                                <td class="description"><span class="corpus-settings-flags-description" title="{$set.description|escape}">{$set.description}</span></td>
                                <td class="sort corpus-settings-flags-sort">{$set.sort}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer corpus-settings-flags-footer tableOptions" element="flag" parent="flagsListContainer">
                    <button type="button" class="btn btn-primary createFlag">
                        <i class="fa fa-plus" aria-hidden="true"></i> New flag
                    </button>
                    <button style="display: none;" type="button" class="btn btn-primary edit editFlag">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </button>
                    <button style="display: none;" type="button" class="btn btn-danger delete deleteFlag">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-flags-modal" id="createFlag" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus" aria-hidden="true"></i> Create flag</h4>
            </div>
            <div class="modal-body">
                <form id = "create_flag_form">
                    <div class="form-group">
                        <label for="flagNameCreate">Name <span class="required_field">*</span></label>
                        <input type="text" class="form-control" name="flagNameCreate" id="flagNameCreate">
                    </div>
                    <div class="form-group">
                        <label for="flagShortCreate">Short</label>
                        <input type="text" class="form-control" id="flagShortCreate">
                    </div>
                    <div class="form-group">
                        <label for="flagDescCreate">Description</label>
                        <textarea class="form-control administration-compact-textarea" rows="4" id="flagDescCreate"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="flagSortCreate">Sort</label>
                        <input type="text" class="form-control" id="flagSortCreate">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmFlagAdd">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-flags-modal" id="editFlag" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i> Edit flag</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_flag_form">
                    <div class="form-group">
                        <label for="flagNameEdit">Name <span class="required_field">*</span></label>
                        <input type="text" class="form-control" name="flagNameEdit" id="flagNameEdit">
                    </div>
                    <div class="form-group">
                        <label for="flagShortEdit">Short</label>
                        <input type="text" class="form-control" id="flagShortEdit">
                    </div>
                    <div class="form-group">
                        <label for="flagDescEdit">Description</label>
                        <textarea class="form-control administration-compact-textarea" rows="4" id="flagDescEdit"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="flagSortEdit">Sort</label>
                        <input type="text" class="form-control" id="flagSortEdit">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmFlagEdit">Confirm</button>
            </div>
        </div>
    </div>
</div>
