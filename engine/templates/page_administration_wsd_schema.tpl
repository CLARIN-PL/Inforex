{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-wsd-schema">
    <div class="row administration-wsd-grid">
        <div class="col-md-6 tableContainer administration-wsd-column" id="sensContainer">
            <div class="panel panel-primary administration-content-panel administration-wsd-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-book"></i></span>
                    <span>Lemma</span>
                </div>
                <div class="tableContent panel-body">
                    <table class="tablesorter table table-striped administration-table administration-wsd-table" id="sensTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th class="administration-wsd-index-column">Lp.</th>
                            <th>Lemma</th>
                        </tr>
                        </thead>
                        <tbody id="sensTableItems">
                        {foreach from=$sensList key=key item=sens}
                            <tr class="sensName" id={$sens.id}>
                                <td>{$key+1}</td>
                                <td class="sens_name">{$sens.annotation_name}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer administration-wsd-footer" element="event_group">
                    <button type="button" class="btn btn-primary create sensCreate" data-toggle="modal" data-target="#create_lemma_modal">Add lemma</button>
                    <button type="button" class="btn btn-primary edit sensEdit" data-toggle="modal" data-target="#edit_lemma_modal" style="display:none;">Edit</button>
                    <button type="button" class="btn btn-danger delete sensDelete" style="display:none;">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 administration-wsd-column" id="senses_options">
            <div class="panel panel-primary tableContainer administration-content-panel administration-wsd-panel" id="sense_panel" style="display: none;">
                <div class="sensTableHeader panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-tags"></i></span>
                    <span>Senses of lemma</span>
                </div>
                <div class="panel-body">
                    <div id="sensDescriptionList" class="administration-wsd-senses-list">
                    </div>
                </div>
                <div class="panel-footer administration-wsd-footer" element="event_type" parent="eventGroupsContainer">
                    <button type="button" class="sensDescriptionCreate btn btn-primary create">New sense</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal administration-wsd-modal" id="create_lemma_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Add new lemma</h4>
            </div>
            <div class="modal-body">
                <form id="create_lemma_form">
                    <div class="form-group">
                        <label for="create_lemma_word">Word <span class="required_field">*</span></label>
                        <input class="form-control" name="create_lemma_word" id="create_lemma_word">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_create_lemma">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal administration-wsd-modal" id="edit_lemma_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-pencil"></i> Edit lemma</h4>
            </div>
            <div class="modal-body">
                <form id="edit_lemma_form">
                    <div class="form-group">
                        <label for="edit_lemma_word">Word <span class="required_field">*</span></label>
                        <input class="form-control" name="edit_lemma_word" id="edit_lemma_word">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_edit_lemma">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-delete-modal administration-wsd-modal" id="delete_lemma_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-trash"></i> Delete lemma</h4>
            </div>
            <div class="modal-body">
                <div id="deleteContent">
                    <label for="delete_lemma_word">Word</label>
                    <p id="delete_lemma_word_preview"></p>
                    <input type="hidden" name="delete_lemma_word" id="delete_lemma_word">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger confirm_delete_lemma">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-form-modal administration-wsd-modal" id="create_sens_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Add new sense</h4>
            </div>
            <div class="modal-body">
                <form id="create_sens_form">
                    <div class="form-group administration-wsd-sense-name-field">
                        <label for="sensnum">Sense number <span class="required_field">*</span></label>
                        <div class="input-group">
                            <span class="input-group-addon" id="sens_name">Word -</span>
                            <input class="form-control" name="create_sens_name" id="sensnum">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="create_sens_description">Description</label>
                        <textarea class="form-control administration-compact-textarea" name="create_sens_description" id="create_sens_description" rows="4"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary confirm_create_sens">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal administration-delete-modal administration-wsd-modal" id="delete_sens_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-trash"></i> Delete sense</h4>
            </div>
            <div class="modal-body">
                <div id="deleteContent">
                    <label>Sense</label>
                    <p id="delete_sens_name_preview"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger confirm_delete_sens">Delete</button>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
