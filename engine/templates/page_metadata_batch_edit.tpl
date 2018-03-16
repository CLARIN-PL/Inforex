{include file="inc_header2.tpl"}
<div class = "container-fluid">
    <div class = "row">
        <div class = "col-sm-2 no-padding text-center">
            <button class = "btn btn-primary" id = "save_data_button" style = "float: left; margin-bottom: 10px; margin-top: 10px; width: 100px;">Save</button>
            <div class = "autosave_group" style = "margin-top: 15px;">
                <label for = "autosave_checkbox">Autosave:</label>
                <input class = "autosave" name = "autosave_checkbox" type = "checkbox">
            </div>
        </div>
        <div class = "col-sm-5" style = "padding: 0;">
            <input class = "form-control" id = "search_field" style = "width: 200px; margin-bottom: 10px; margin-top: 10px;" placeholder = "Search...">
        </div>
        <div class = "col-sm-5" style = "padding: 0;">
            <button class = "btn btn-primary" style = "float: right; margin-bottom: 10px; margin-top: 10px;"  data-toggle="modal" data-target="#load_metadata_modal">Load metadata from filename</button>
        </div>
    </div>
    <div class = "row" >
        <div id="hot-container"></div>
    </div>
</div>

<div class="modal fade settingsModal" id="load_metadata_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Load metadata</h4>
            </div>
            <div class="modal-body">
                <div class = "load_options">
                    <div class = "form-group">
                        <label for = "field_select">Field:</label>
                        <select name = "field_select" class = "form-control field_select">
                            <option value = "null">-select-</option>
                            {foreach from = $metadata_columns item = metadata_column}
                                <option value = "{$metadata_column}">{$metadata_column}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class = "form-group">
                        <label for = "token_select">Token:</label>
                        <select name = "token_select" class = "form-control token_select">
                            <option value = "null">-select-</option>
                            <option value = "end">END</option>
                            <option value = "-">-</option>
                            <option value = ".">.</option>
                            <option value = "_">_</option>
                        </select>
                    </div>
                    <div class = "form-group clearfix">
                        <div class = "col-sm-6 no-padding">
                            <button class = "btn btn-danger back_metadata" style = "float: left; width: 80px;">Back</button>
                        </div>
                        <div class = "col-sm-6 no-padding">
                            <button class = "btn btn-primary continue_metadata" style = "float: right; width: 80px;">Continue</button>
                        </div>
                    </div>
                    <hr>
                    <div class = "form-group">
                        <label for = "token_select">Regular expression:</label>
                        <input type = "text" class = "form-control regex_user_friendly" disabled>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id = "confirm_metadata_load" disabled type="button" class="btn btn-primary">Confirm</button>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
