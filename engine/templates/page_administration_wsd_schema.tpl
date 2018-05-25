{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}
<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-6 tableContainer" id = "sensContainer" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px;">
                <div class="panel-heading">Lemma</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="tablesorter table table-striped" id="sensTable" cellspacing="1">
                        <thead>
                        <tr>
                            <th style="width:25px;">Lp.</th>
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
                <div class="panel-footer" element="event_group">
                    <button type = "button" class = "btn btn-primary sensCreate" data-toggle="modal" data-target="#create_lemma_modal">Add lemma</button>
                    <button type = "button" class = "btn btn-primary sensEdit" data-toggle="modal" data-target="#edit_lemma_modal" style = "display:none;">Edit</button>
                    <button type = "button" class = "btn btn-danger sensDelete" style = "display:none;">Delete</button>
                </div>
            </div>
        </div>
        <div class="col-md-6" style="padding: 0" id="senses_options">
            <div class="panel panel-primary scrollingWrapper tableContainer" id="sense_panel" style="margin: 5px; display: none;">
                <div class="sensTableHeader panel-heading">Senses of lemma</div>
                <div class="panel-body">
                    <div id ="sensDescriptionList"  class="scrolling">
                    </div>
                </div>
                <div class="panel-footer" element="event_type" parent="eventGroupsContainer">
                    <button type = "button" class = "sensDescriptionCreate btn btn-primary create">New sense</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="create_lemma_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add new lemma</h4>
            </div>
            <div class="modal-body">
                <form id = "create_lemma_form">
                    <div class="form-group">
                        <label for="create_lemma_word">Word: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "create_lemma_word" id="create_lemma_word">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_lemma">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="edit_lemma_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit lemma</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_lemma_form">
                    <div class="form-group">
                        <label for="edit_lemma_word">Word: <span class = "required_field">*</span></label>
                        <input class="form-control" name = "edit_lemma_word" id="edit_lemma_word">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_edit_lemma">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="delete_lemma_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Do you really want to delete this lemma?</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="delete_lemma_word">Word: </label>
                    <input class="form-control" name = "delete_lemma_word" id="delete_lemma_word">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger confirm_delete_lemma">Delete</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="create_sens_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add new sense</h4>
            </div>
            <div class="modal-body">
                <form id = "create_sens_form">
                    <div class = "row">
                        <div class="form-group form-inline col-sm-12" >
                            <label for="create_sens_name" id = "sens_name" style = "float: left; margin-top: 8px;">Word: </label>
                            <input class="col-lg-8 form-control" style = "width: 50px; margin-left: 5px;" name = "create_sens_name" id="sensnum">
                        </div>
                    </div>
                    <div class = "row">
                        <div class="form-group col-sm-12">
                            <label for="create_sens_description">Description: </label>
                            <input class="form-control" name = "create_sens_description" id="create_sens_description">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_sens">Confirm</button>
            </div>
        </div>
    </div>
</div>


{include file="inc_footer.tpl"}
