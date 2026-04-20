{include file="inc_header2.tpl"}
<div class="container-fluid admin_tables metadata-batch-edit-page" id="metadata_batch_edit_page">
    <div class="panel administration-content-panel metadata-batch-edit-panel">
        <div class="panel-heading administration-content-heading metadata-batch-edit-heading">
            <span class="administration-content-heading-icon"><i class="fa fa-table" aria-hidden="true"></i></span>
            <span>Metadata batch edit</span>
        </div>
        <div class="panel-body metadata-batch-edit-panel-body">
            <div class="metadata-batch-edit-toolbar">
                <div class="metadata-batch-edit-toolbar-left">
                    <label class="metadata-batch-edit-autosave" for="autosave_checkbox">
                        <span>Autosave</span>
                        <input class="autosave" id="autosave_checkbox" name="autosave_checkbox" type="checkbox">
                    </label>
                    <button class="btn btn-primary metadata-batch-edit-save-button" id="save_data_button">
                        <i class="fa fa-save" aria-hidden="true"></i>
                        Save
                    </button>
                </div>
                <div class="metadata-batch-edit-toolbar-right">
                    <button class="btn btn-primary metadata-batch-edit-load-button" data-toggle="modal" data-target="#load_metadata_modal">
                        <i class="fa fa-magic" aria-hidden="true"></i>
                        Load metadata from filename
                    </button>
                </div>
            </div>
            <div class="administration-table-wrapper metadata-batch-edit-hot-wrapper">
                <div id="hot-container"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade metadata-batch-edit-modal" id="load_metadata_modal" role="dialog">
    <div class="modal-dialog metadata-batch-edit-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-magic" aria-hidden="true"></i> Load metadata</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body metadata-batch-edit-modal-body">
                <div class="panel administration-content-panel metadata-batch-edit-modal-panel">
                    <div class="panel-heading administration-content-heading metadata-batch-edit-modal-heading">
                        <span class="administration-content-heading-icon"><i class="fa fa-code-fork" aria-hidden="true"></i></span>
                        Pattern editor
                    </div>
                    <div id="pattern-editor" class="panel-body"></div>
                    <div class="panel-footer administration-content-footer metadata-batch-edit-modal-footer">
                        <button id="pattern-editor-add-row" class="btn btn-default metadata-batch-edit-secondary-button">Add row</button>
                    </div>
                </div>
                <div class="panel administration-content-panel metadata-batch-edit-modal-panel">
                    <div class="panel-heading administration-content-heading metadata-batch-edit-modal-heading">
                        <span class="administration-content-heading-icon"><i class="fa fa-eye" aria-hidden="true"></i></span>
                        Pattern preview
                    </div>
                    <div id="pattern-preview" class="panel-body">
                    </div>
                </div>
                <div class="panel administration-content-panel metadata-batch-edit-modal-panel">
                    <div class="panel-heading administration-content-heading metadata-batch-edit-modal-heading">
                        <span class="administration-content-heading-icon"><i class="fa fa-files-o" aria-hidden="true"></i></span>
                        Documents preview
                    </div>
                    <div id="pattern-result" class="panel-body metadata-batch-edit-pattern-result"></div>
                </div>
                <div class="metadata-batch-edit-modal-actions">
                    <button id="confirm_metadata_load" type="button" class="btn btn-primary metadata-batch-edit-confirm-button">
                        <i class="fa fa-check" aria-hidden="true"></i>
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
