{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables" id = "subcorpora">
    <div class="row">
            <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width: 40%;">
                <div class="panel-heading">Subcorpora</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="tablesorter table table-striped" id="subcorpusListContainer" cellspacing="1">
                        <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$subcorpusList item=set}
                            <tr>
                                <td>{$set.id}</td>
                                <td>{$set.name}</td>
                                <td>{$set.description}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer tableOptions" element="subcorpus" parent="subcorpusListContainer" >
                    <button action="subcorpus_add" type = "button" class = "btn btn-primary create subcorporaCreate">Create</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit subcorporaEdit">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-danger delete deleteSubcorpus">Delete</button>
                </div>
            </div>
    </div>
</div>

<div class="modal fade settingsModal" id="subcorporaCreate" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create subcorpus</h4>
            </div>
            <div class="modal-body">
                <form id = "create_subcorpora_form">
                    <div class="form-group">
                        <label for="subcorporaCreateName">Name: <span class = "required_field">*</span></label>
                        <input type = "text" name = "subcorporaCreateName" id = "subcorporaCreateName" class = "form-control">
                        </input>
                    </div>
                    <div class="form-group">
                        <label for="subcorporaCreateDescription">Description:</label>
                        <textarea class="form-control" rows="5" id="subcorporaCreateDescription"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button"  class="btn btn-primary confirmSubcorporaCreate">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="subcorporaEdit" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit subcorpus</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_subcorpora_form">
                    <div class="form-group">
                        <label for="subcorporaEditName">Name: <span class = "required_field">*</span></label>
                        <input type = "text" name = "subcorporaEditName" id = "subcorporaEditName" class = "form-control">
                        </input>
                    </div>
                    <div class="form-group">
                        <label for="subcorporaEditDescription">Description:</label>
                        <textarea class="form-control" rows="5" id="subcorporaEditDescription"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button"  class="btn btn-primary confirmSubcorporaEdit">Confirm</button>
            </div>
        </div>
    </div>
</div>
