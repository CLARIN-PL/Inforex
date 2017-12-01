{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="col-md-12" style="padding: 0">
            <div class="panel panel-primary scrollingWrapper">
                <div class="panel-heading">Flags</div>
                <div class="tableContent panel-body scrolling" style="">
                    <table class="table table-striped" id="flagsListContainer" cellspacing="1">
                        <thead>
                        <tr>
                            <th class="col-num">Id</th>
                            <th style="width: 300px">Name</th>
                            <th style="width: 100px">Short name</th>
                            <th>Description</th>
                            <th class="col-num">Sort</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from=$flagsList item=set}
                            <tr>
                                <td>{$set.id}</td>
                                <td class="name">{$set.name}</td>
                                <td class="short">{$set.short}</td>
                                <td class="description">{$set.description}</td>
                                <td class="sort">{$set.sort}</td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="panel-footer tableOptions" element="flag" parent="flagsListContainer">
                    <button type = "button" class = "btn btn-primary createFlag">New flag</button>
                    <button style = "display: none;" type = "button" class = "btn btn-primary edit editFlag">Edit</button>
                    <button style = "display: none;" type = "button" class = "btn btn-danger delete deleteFlag">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="createFlag" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create flag</h4>
            </div>
            <div class="modal-body">
                <form id = "create_flag_form">
                    <div class="form-group">
                        <label for="flagNameCreate">Name: <span class = "required_field">*</span></label>
                        <input type = "text" class="form-control" name = "flagNameCreate" id="flagNameCreate"></input>
                    </div>
                    <div class="form-group">
                        <label for="flagShortCreate">Short:</label>
                        <input type = "text" class="form-control"  id="flagShortCreate"></input>
                    </div>
                    <div class="form-group">
                        <label for="flagDescCreate">Description:</label>
                        <textarea class="form-control" rows="5" id="flagDescCreate"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="flagSortCreate">Sort:</label>
                        <input type = "text" class="form-control" id="flagSortCreate"></input>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirmFlagAdd">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="editFlag" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit flag</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_flag_form">
                    <div class="form-group">
                        <label for="flagNameEdit">Name: <span class = "required_field">*</span></label>
                        <input type = "text" class="form-control" name = "flagNameEdit" id="flagNameEdit">
                    </div>
                    <div class="form-group">
                        <label for="flagShortEdit">Short:</label>
                        <input type = "text" class="form-control"  id="flagShortEdit">
                    </div>
                    <div class="form-group">
                        <label for="flagDescEdit">Description:</label>
                        <textarea class="form-control" rows="5" id="flagDescEdit"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="flagSortEdit">Sort:</label>
                        <input type = "text" class="form-control" id="flagSortEdit">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirmFlagEdit">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="deleteFlag" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit flag</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="flagNameEdit">Name:</label>
                        <input type = "text" class="form-control" id="flagNameEdit">
                    </div>
                    <div class="form-group">
                        <label for="flagShortEdit">Short:</label>
                        <input type = "text" class="form-control"  id="flagShortEdit">
                    </div>
                    <div class="form-group">
                        <label for="flagDescEdit">Description:</label>
                        <textarea class="form-control" rows="5" id="flagDescEdit"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="flagSortEdit">Sort:</label>
                        <input type = "text" class="form-control" id="flagSortEdit">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirmFlagEdit" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>



