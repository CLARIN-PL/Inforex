{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="delete-corpus-wrapper">
    <div class="alert alert-warning">
        <strong>Warning!</strong> Notice, that this operation is permament and cannot be undone.
    </div>
    <div class="delete-corpus-button-wrapper">
        <button type="button" class="delete_corpora_button btn btn-danger" style="margin-bottom: 20px;"
                data-toggle="modal" data-target="#deleteCorpus">
            Delete corpus <span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button>
    </div>
</div>

<input id="corpus_name" type="hidden" value="{$corpus.name}"/>
<input id="corpus_id" type="hidden" value="{$corpus.id}"/>
<input id="corpus_description" type="hidden" value="{$corpus.description}"/>

<div class="modal fade settingsModal" id="deleteCorpus" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="deleteCorpusHeader">Are you sure you want to delete this?</h4>
            </div>
            <div class="modal-body" id="deleteContent">
                <label for="deleteCorpusName">Name:</label>
                <p id="deleteCorpusName"></p>
                <label for="deleteCorpusDesc">Description:</label>
                <p id="deleteCorpusDesc"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger confirmDeleteCorpus" data-dismiss="modal">Delete</button>
            </div>
        </div>
    </div>
</div>
