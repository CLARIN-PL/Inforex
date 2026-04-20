{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables corpus-settings-subcorpora" id="subcorpora">
    <div class="row corpus-settings-subcorpora-grid">
        <div class="col-md-8 col-md-offset-2 corpus-settings-subcorpora-column">
            <div class="panel administration-content-panel corpus-settings-subcorpora-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-folder-open" aria-hidden="true"></i></span>
                    <span>Subcorpora</span>
                </div>
                <div class="panel-body">
                    <div class="administration-table-wrapper corpus-settings-subcorpora-table-wrapper">
                    <table class="tablesorter table table-striped table-hover administration-table corpus-settings-subcorpora-table" id="subcorpusListContainer" cellspacing="1">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$subcorpusList item=set}
                            <tr>
                                <td class="corpus-settings-subcorpora-id">{$set.id}</td>
                                <td id="{$set.id}"><span class="corpus-settings-subcorpora-name">{$set.name}</span></td>
                                <td><span class="corpus-settings-subcorpora-description" title="{$set.description|escape}">{$set.description}</span></td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer corpus-settings-subcorpora-footer tableOptions" element="subcorpus" parent="subcorpusListContainer">
                    <button action="subcorpus_add" type="button" class="btn btn-primary create subcorporaCreate">
                        <i class="fa fa-plus" aria-hidden="true"></i> Create
                    </button>
                    <button style="display: none;" type="button" class="btn btn-primary edit subcorporaEdit">
                        <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                    </button>
                    <button style="display: none;" type="button" class="btn btn-danger delete deleteSubcorpus">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-subcorpora-modal" id="subcorporaCreate" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus" aria-hidden="true"></i> Create subcorpus</h4>
            </div>
            <div class="modal-body">
                <form id = "create_subcorpora_form">
                    <div class="form-group">
                        <label for="subcorporaCreateName">Name <span class="required_field">*</span></label>
                        <input type="text" name="subcorporaCreateName" id="subcorporaCreateName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="subcorporaCreateDescription">Description</label>
                        <textarea class="form-control administration-compact-textarea" rows="4" id="subcorporaCreateDescription"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmSubcorporaCreate">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal corpus-settings-subcorpora-modal" id="subcorporaEdit" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-pencil" aria-hidden="true"></i> Edit subcorpus</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_subcorpora_form">
                    <div class="form-group">
                        <label for="subcorporaEditName">Name <span class="required_field">*</span></label>
                        <input type="text" name="subcorporaEditName" id="subcorporaEditName" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="subcorporaEditDescription">Description</label>
                        <textarea class="form-control administration-compact-textarea" rows="4" id="subcorporaEditDescription"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirmSubcorporaEdit">Confirm</button>
            </div>
        </div>
    </div>
</div>
