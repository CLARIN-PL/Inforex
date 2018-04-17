{include file="inc_header2.tpl"}
<div class = "container-fluid scrollingWrapper" id = "metadata_batch_edit_page">
    <div class = "row">
        <div class = "col-sm-7 no-padding text-center">
            <div class = "autosave_group" style = "margin-top: 15px; margin-right: 15px; float: left;">
                <label for = "autosave_checkbox">Autosave:</label>
                <input class = "autosave" name = "autosave_checkbox" type = "checkbox">
            </div>
            <button class = "btn btn-primary" id = "save_data_button" style = "float: left; margin-bottom: 10px; margin-top: 10px; min-width: 100px;">Save</button>
        </div>
        <div class = "col-sm-5" style = "padding: 0;">
            <button class = "btn btn-primary" style = "float: right; margin-bottom: 10px; margin-top: 10px;"  data-toggle="modal" data-target="#load_metadata_modal">Load metadata from filename</button>
        </div>
    </div>
    <div class = "row" >
        <div id="hot-container"></div>
    </div>
</div>

<div class="modal fade" id="load_metadata_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Load metadata</h4>
            </div>
            <div class="modal-body scrolling">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Pattern editor
                    </div>
                    <div id="pattern-editor" class="panel-body"></div>
                    <div class="panel-footer">
                        <button id="pattern-editor-add-row" class="btn">Add row</button>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Pattern preview
                    </div>
                    <div id="pattern-preview" class="panel-body">
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Documents preview
                    </div>
                    <div id="pattern-result" class="panel-body" style = "max-height: 300px; overflow: auto;"></div>
                </div>
                <button id = "confirm_metadata_load" style = "float: right;" type="button" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
