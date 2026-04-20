{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="container-fluid admin_tables corpus-settings-delete-corpus">
    <div class="row corpus-settings-delete-corpus-grid">
        <div class="col-md-8 col-md-offset-2 corpus-settings-delete-corpus-column">
            <div class="panel administration-content-panel corpus-settings-delete-corpus-panel">
                <div class="panel-heading administration-content-heading corpus-settings-delete-corpus-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                    <span>Delete corpus</span>
                </div>
                <div class="panel-body">
                    <div class="corpus-settings-delete-corpus-card">
                        <div class="corpus-settings-delete-corpus-icon">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </div>
                        <div class="corpus-settings-delete-corpus-content">
                            <h3>Permanent corpus deletion</h3>
                            <p>This operation removes the corpus and cannot be undone. Verify that the corpus is no longer needed before continuing.</p>
                        </div>
                    </div>
                </div>
                <div class="panel-footer administration-content-footer corpus-settings-delete-corpus-footer">
                    <button type="button" class="delete_corpora_button btn btn-danger"
                            data-toggle="modal" data-target="#deleteCorpus">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete corpus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<input id="corpus_name" type="hidden" value="{$corpus.name}"/>
<input id="corpus_id" type="hidden" value="{$corpus.id}"/>
<input id="corpus_description" type="hidden" value="{$corpus.description}"/>

<div class="modal fade settingsModal administration-form-modal administration-delete-modal corpus-settings-delete-corpus-modal" id="deleteCorpus" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-trash" aria-hidden="true"></i> <span id="deleteCorpusHeader">Are you sure you want to delete this?</span></h4>
            </div>
            <div class="modal-body" id="deleteContent">
                <div class = "delete_info">
                    <label for="deleteCorpusName">Name:</label>
                    <p id="deleteCorpusName"></p>
                    <label for="deleteCorpusDesc">Description:</label>
                    <p id="deleteCorpusDesc"></p>
                </div>
                <div class = "delete_loader text-center" style = "display: none;">
                    <div class = "loader"></div>
                    <h3>Deleting corpus...</h3>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger confirmDeleteCorpus">Delete</button>
            </div>
        </div>
    </div>
</div>
