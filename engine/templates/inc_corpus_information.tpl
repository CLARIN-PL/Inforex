{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables">
    <div class="row">
        <div class="panel panel-primary scrollingWrapper" style="margin: 5px; width: 40%;">
            <div class="panel-heading">Basic information</div>
            <div class="tableContent panel-body scrolling" style="">
                <table class="table table-striped" id="corpusElementsContainer" cellspacing="1">
                    <tr>
                        <th id="name"><strong> Name: </strong></th>
                        <td>{$corpus.name}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td style="width: 40px">
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary btn-xs editBasicInfo editBasicInfoName" style="margin: 2px">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="user_id"><strong>Owner:</strong></th>
                        <td>{$owner.screename}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td>
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary btn-xs editBasicInfo editBasicInfoOwner" style="margin: 2px" data-toggle="modal" data-target="#basicInfoOwner">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="public"><strong>Access:</strong></th>
                        <td>{if $corpus.public}public{else}restricted{/if}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td>
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" class="btn btn-primary btn-xs editBasicInfo editBasicInfoAccess" style="margin: 2px" data-toggle="modal" data-target="#basicInfoAccess">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="description"><strong>Description:</strong></th>
                        <td>{$corpus.description}</td>
                        {if isCorpusOwner() || "admin"|has_role}
                            <td>
                                <div class="tableOptions" element="corpus_details" parent="corpusElementsContainer">
                                    <a href="#" type="button" class="btn btn-primary btn-xs editBasicInfo editBasicInfoDescription" style="margin: 2px" data-toggle="modal" data-target="#basicInfoDescription">edit</a>
                                </div>
                            </td>
                        {/if}
                    </tr>
                    <tr>
                        <th id="date_created"><strong>Date created:</strong></th>
                        <td>{$corpus.date_created}</td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="basicInfoNameModal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Change the name of the corpus</h4>
            </div>
            <div class="modal-body">
                <form id = "edit_corpus_name_form">
                    <div class="form-group">
                        <label for="comment">Name: <span class = "required_field">*</span></label>
                        <textarea class="form-control" rows="5" name = "nameDescription" id="nameDescription"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirmName">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="basicInfoOwner" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit owner of the corpus</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="comment">Owner:</label>
                        <div id = "basicInfoOwnerSelect">

                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button"  class="btn btn-primary confirmOwner" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="basicInfoAccess" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit corpus access</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group" id = "basicInfoAccessSelect">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirmAccess" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade settingsModal" id="basicInfoDescription" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit corpus description</h4>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group" id = "corpusDescriptionArea">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirmDescription" data-dismiss="modal">Confirm</button>
            </div>
        </div>
    </div>
</div>



