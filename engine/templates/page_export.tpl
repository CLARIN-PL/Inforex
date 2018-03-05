{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}

<div class="modal fade settingsModal" id="newExportForm" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">New export</h4>
            </div>
            <div class="modal-body" style = "max-height: 70vh; overflow: auto;">
                <table class="table table-striped" cellspacing="1">
                    <tr>
                        <th style="width: 100px; vertical-align: top">Description</th>
                        <td colspan="2">
                            <textarea name="description" style="width: 98%; height: 50px"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Selectors<br/><small style="font-weight: normal">Definie citeria used to select document to export</small></th>

                        <td class="flags"></td>
                        <td style="width: 40px; text-align: center; vertical-align: top;">
                            <div class="flag_template" style="display: none">
                                <div style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 2px;">
                                    <i class="fa fa-times-circle-o close" aria-hidden="true"></i>
                                    <div class="flags" style="vertical-align: middle;">
                                        Flag <select name="corpus_flag_id"
                                                     style="font-size: 12px">
                                            <option style="font-style: italic" value="">Select flag</option>
                                            {foreach from=$corpus_flags item=flag}
                                                <option value="{$flag.corpora_flag_id}"
                                                        {if $flag.corpora_flag_id==$corpus_flag_id}selected=
                                                        "selected"{/if} title="{$flag.name}"><em>{$flag.short}</em></option>
                                            {/foreach}
                                        </select> : {foreach from=$flags item=flag}
                                            <img class="flag" src="gfx/flag_{$flag.flag_id}.png"
                                                 value="{$flag.flag_id}" /> {/foreach}
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="+" class="button new_selector"/>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Extractors<br/><small style="font-weight: normal">Definie what elements should be exported for documents on the basis of their flag values</small></th>
                        <td class="extractors">
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <div class="extractor_template" style="display: none;">
                                <div class="extractor" style="border: 1px solid #152C96; background: #C8D0F2; padding: 4px; margin: 2px;">
                                    <i class="fa fa-times-circle-o close" aria-hidden="true"></i>
                                    <b>For</b>
                                    <div style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 4px;">
                                        <div class="flags">
                                            Flag
                                            <select name="corpus_flag_id" style="font-size: 12px">
                                                <option style="font-style: italic" value="">Select flag</option>
                                                {foreach from=$corpus_flags item=flag}
                                                    <option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if} title="{$flag.name}"><em>{$flag.short}</em></option>
                                                {/foreach}
                                            </select>
                                            :
                                            {foreach from=$flags item=flag}
                                                <img class="flag" src="gfx/flag_{$flag.flag_id}.png" value="{$flag.flag_id}"/>
                                            {/foreach}
                                        </div>
                                    </div>
                                    <b>Export</b>
                                    <select class = "select_mode">
                                        <option selected value = "standard">Standard</option>
                                        <option value = "custom">Custom</option>
                                    </select>
                                    <div class="element_user" style="margin: 4px; max-height: 300px; overflow: auto; display: none;">
                                        <table style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; width: 100%;">
                                            <tr>
                                                <td style = "background: white !important;">
                                                    <label>Stage: </label>
                                                    <select class = "annotation_stage_select">
                                                        <option value = "final">final</option>
                                                        <option value = "agreement">agreement</option>
                                                        <option value = "new">new</option>
                                                        <option value = "discarded">discarded</option>

                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div style = "max-height: 200px !important; overflow: auto;">
                                                        <table class = "export_users" style = "width: 100%;">
                                                            <thead>
                                                            <th>User</th>
                                                            <th></th>
                                                            </thead>
                                                            <tbody>
                                                            {foreach from=$users item = user}
                                                                <tr>
                                                                    <td class = "username">{$user.screename}</td>
                                                                    <td class = "text-center">
                                                                        <input class = "user_checkbox" value = {$user.user_id} type = "checkbox">
                                                                    </td>
                                                                </tr>
                                                            {/foreach}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{include file="inc_widget_annotation_sets_subsets.tpl"}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="elements" style="margin: 4px; max-height: 300px; overflow: auto;">
                                        <table style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; width: 100%;">
                                            <tr>
                                                <td>{include file="inc_widget_annotation_layers_and_subsets.tpl"}</td>
                                            </tr>
                                            <tr>
                                                <td>{include file="inc_widget_relation_structure.tpl"}</td>
                                            </tr>
                                            {*
                                            <tr>
                                                <td style="text-align: right">Annotation relation(s):</td>
                                                <td><a href="#">select</a></td>
                                            </tr>
                                            *}
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="+" class="button new_extractor"/>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Indices<br/>
                            <small style="font-weight: normal">Define indices</small>
                        </th>
                        <td class="indices">
                        </td>
                        <td style="text-align: center; vertical-align: top;">
                            <div class="index_template" style="display: none;">
                                <div class="index" style="border: 1px solid #152C96; background: #C8D0F2; padding: 4px; margin: 2px;">
                                    <i class="fa fa-times-circle-o close" aria-hidden="true"></i>
                                    <b>For</b>
                                    <div style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 4px;">
                                        <div class="flags">
                                            Flag
                                            <select name="corpus_flag_id" style="font-size: 12px">
                                                <option style="font-style: italic" value="">Select flag</option>
                                                {foreach from=$corpus_flags item=flag}
                                                    <option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if} title="{$flag.name}"><em>{$flag.short}</em></option>
                                                {/foreach}
                                            </select>
                                            :
                                            {foreach from=$flags item=flag}
                                                <img class="flag" src="gfx/flag_{$flag.flag_id}.png" value="{$flag.flag_id}"/>
                                            {/foreach}
                                        </div>
                                    </div>
                                    <b>File name</b>
                                    <div style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 4px;">
                                        index_<input class = "index_file" type = "text" style = "width: 200px;">.list
                                    </div>
                                </div>
                            </div>
                            <input type="submit" value="+" class="button new_index"/>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary confirm_create_user" id = "export">Export</button>
            </div>
        </div>
    </div>
</div>


<div id="newExportForm" style="display: none">
    <h2>Define new export</h2>

</div>

<div id="history">
    <div class="panel panel-primary scrollingWrapper">
        <div class="panel-heading">History of exports</div>
        <div class="panel-body scrolling" style="padding: 0">
            <table id="exportHistory" class="table table-striped" cellspacing="1">
                <thead>
                <tr>
                    <th class = "col-md-1">Id</th>
                    <th class = "col-md-1" style="text-align: center">Status</th>
                    <th class = "col-md-1">Description</th>
                    <th class = "col-md-1">Submitted</th>
                    <th class = "col-md-1">Processing started</th>
                    <th class = "col-md-1">Processing finished</th>
                    <th class = "col-md-1">Selectors</th>
                    <th class = "col-md-1"  style = "max-width: 50px;">Extractors</th>
                    <th class = "col-md-1"> Indices</th>
                    <th class = "col-md-1" style="text-align: center">Message</th>
                    <th class = "col-md-1" style="text-align: center">Statistics</th>
                    <th class = "col-md-1" style="text-align: center">Download</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$exports item=export}
                    <tr>
                        <td class = "col-md-1">{$export.export_id}</td>
                        <td class = "col-md-1 export_status" id = "export_status_{$export.export_id}" style="text-align: center">{$export.status}</td>
                        <td class = "col-md-1">{$export.description}</td>
                        <td class = "col-md-1">{$export.datetime_submit}</td>
                        <td class = "col-md-1">{$export.datetime_start}</td>
                        <td class = "col-md-1">{$export.datetime_finish}</td>
                        <td class = "col-md-1 export_column">{$export.selectors|trim}</td>
                        <td class = "col-md-1 export_column">{$export.extractors}</td>
                        <td class = "col-md-1 export_column /">{$export.indices}</td>
                        <td class = "col-md-1" id = "export_message_{$export.export_id}" style="text-align: center">
                            {if $export.errors > 0}
                                <button class="btn btn-warning export_message_button" id = "{$export.export_id}">Contains errors</button>
                            {else}
                                <i>-</i>
                            {/if}
                        </td>
                        <td class = "col-md-1" id = "export_stats_{$export.export_id}" style="text-align: center">
                            {if $export.status == "done" && $export.statistics != ""}
                                <button class="btn btn-primary export_stats_button" id = "{$export.export_id}" >Statistics</button>
                            {else}
                                <i>not available</i>
                            {/if}
                        </td>
                        <td class = "col-md-1" id = "export_download_{$export.export_id}" style="text-align: center">
                            {if $export.status == "done"}
                                <a href="index.php?page=export_download&amp;export_id={$export.export_id}">
                                    <button class="btn btn-primary">Download</button>
                                </a>
                            {else}
                                <i>not ready</i>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                {if $exports|@count==0}
                    <tr>
                        <td colspan="10"><i>History of exports is empty</i></td>
                    </tr>
                {/if}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <button id="newExportButton" type="button" class="btn btn-primary" style = "margin-bottom: 7px;">New export</button>
        </div>
    </div>
</div>

<br style="clear: both;"/>

<div class="modal fade settingsModal" id="export_stats_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Export statistics</h4>
            </div>
            <div class="modal-body" id = "export_stats_body" style = "max-height:400px; overflow: auto;">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal" id="export_message_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Export message</h4>
            </div>
            <div class="modal-body" id = "export_message_body" style = "min-height: 180px; max-height:400px; overflow: auto;">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
