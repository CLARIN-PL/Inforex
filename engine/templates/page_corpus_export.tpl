{include file="inc_header2.tpl"}

<div class="modal fade settingsModal corpus-export-modal" id="newExportForm" role="dialog">
    <div class="modal-dialog corpus-export-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-download" aria-hidden="true"></i> New export</h4>
            </div>
            <div class="modal-body corpus-export-modal-body">
                <table class="table table-striped corpus-export-form-table" cellspacing="1">
                    <tr>
                        <th style="width: 200px; vertical-align: top">Description</th>
                        <td colspan="2">
                            <textarea name="description" class="form-control corpus-export-description"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Tagging<br/>
                            <small style="font-weight: normal">Choose export's tagging set</small>
                        </th>
                        <td class="corpus-export-tagging-cell" style="text-align: left; vertical-align: top;">
                            <select name="select-tagging" class="form-control" style="min-width: 70px">
                                <option value="final_or_tagger">Final or tagger (if final not present)</option>
                                <option value="final">Final</option>
                                <option value="user">User (agreement)</option>
                                <option value="tagger">Tagger</option>
                            </select>

                            <br>

                            <select style="display: none;" name="morpho-user" class="form-control" style="min-width: 70px">
                                {foreach from=$morpho_users item = user}
                                    <option value="{$user.user_id}">{$user.screename}</option>
                                {foreachelse}
                                    <option value="">No user annotations</option>
                                {/foreach}
                            </select>
                        </td>
                        <td class="corpus-export-add-column"></td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Format<br/>
                            <small style="font-weight: normal">Choose export package format</small>
                        </th>
                        <td class="corpus-export-tagging-cell" style="text-align: left; vertical-align: top;">
                            <select name="select-export-format" class="form-control" style="min-width: 70px">
                                <option value="legacy">CCL XML</option>
                                <option value="text">Text format</option>
                                <option value="conllu">CoNLL-U CLARIN</option>
                                <option value="conllu_standard">CoNLL-U</option>
                                <option value="clarin_json">CLARIN JSON</option>
                                <option value="clarin_parquet_zst">CLARIN Parquet ZST</option>
                            </select>
                        </td>
                        <td class="corpus-export-add-column"></td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Selectors<br/><small style="font-weight: normal">Definie citeria used to select document to export</small></th>

                        <td class="flags"></td>
                        <td class="corpus-export-add-column" style="width: 40px; text-align: center; vertical-align: top;">
                            <div class="flag_template" style="display: none">
                                <div class="corpus-export-selector-card" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 2px;">
                                    <i class="fa fa-times-circle-o close" aria-hidden="true"></i>
                                    <div class="flags corpus-export-filter-box" style="vertical-align: middle;">
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
                            <button type="button" class="btn btn-primary corpus-export-add-button new_selector" title="Add selector"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Extractors<br/><small style="font-weight: normal">Definie what elements should be exported for documents on the basis of their flag values</small></th>
                        <td class="extractors">
                        </td>
                        <td class="corpus-export-add-column" style="text-align: center; vertical-align: top;">
                            <div class="extractor_template" style="display: none;">
                                <div class="extractor corpus-export-extractor-card" style="border: 1px solid #152C96; background: #C8D0F2; padding: 4px; margin: 2px;">
                                    <i class="fa fa-times-circle-o close" aria-hidden="true"></i>
                                    <b>For</b>
                                    <div class="corpus-export-filter-box" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 4px;">
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
                                    <div class="element_user corpus-export-elements-panel" style="margin: 4px; max-height: 300px; overflow: auto; display: none;">
                                        <table class="corpus-export-elements-table" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; width: 100%;">
                                            <tr>
                                                <td class="corpus-export-stage-cell" style = "background: white !important;">
                                                    <label>Stage: </label>
                                                    <select class = "annotation_stage_select">
                                                        <option value = "final">final</option>
                                                        <option value = "agreement">agreement</option>
                                                        <option value = "relationagreement">relation agreement</option>
                                                        <option value = "new">new</option>
                                                        <option value = "discarded">discarded</option>

                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="corpus-export-users-scroll" style = "max-height: 200px !important; overflow: auto;">
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
						<td>{include file="inc_widget_annotation_layers_and_subsets.tpl"}</td>
                                            </tr>
                                            <tr>
                                                <td>{include file="inc_widget_relation_structure.tpl"}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="elements corpus-export-elements-panel" style="margin: 4px; max-height: 300px; overflow: auto;">
                                        <table class="corpus-export-elements-table" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; width: 100%;">
                                            <tr>
                                                <td>{include file="inc_widget_annotation_layers_and_subsets.tpl"}</td>
                                            </tr>
                                            <tr>
                                                <td>{include file="inc_widget_relation_structure.tpl"}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary corpus-export-add-button new_extractor" title="Add extractor"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top">Indices<br/>
                            <small style="font-weight: normal">Define indices</small>
                        </th>
                        <td class="indices">
                        </td>
                        <td class="corpus-export-add-column" style="text-align: center; vertical-align: top;">
                            <div class="index_template" style="display: none;">
                                <div class="index corpus-export-index-card" style="border: 1px solid #152C96; background: #C8D0F2; padding: 4px; margin: 2px;">
                                    <i class="fa fa-times-circle-o close" aria-hidden="true"></i>
                                    <b>For</b>
                                    <div class="corpus-export-filter-box" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 4px;">
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
                                    <div class="corpus-export-index-file-box" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 4px;">
                                        index_<input class = "index_file" type = "text" style = "width: 200px;">.list
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary corpus-export-add-button new_index" title="Add index"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
		<button type="button" class="btn btn-default corpus-export-check-button confirm_create_user" id="check_form"><i class="fa fa-check-square-o" aria-hidden="true"></i> Check Form</button>
                <button type="button" class="btn btn-primary corpus-export-confirm-button confirm_create_user" id="export"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Export</button>
            </div>
        </div>
    </div>
</div>

<div id="history" class="corpus-export-page">
	    <div class="panel panel-primary scrollingWrapper administration-content-panel corpus-export-panel">
	        <div class="panel-heading administration-content-heading corpus-export-heading">
                <span class="administration-content-heading-icon corpus-export-heading-icon"><i class="fa fa-archive" aria-hidden="true"></i></span>
                <span>History of exports</span>
                <span class="home-corpora-counter corpus-export-counter">last {$export_history_limit}</span>
            </div>
        <div class="panel-body scrolling corpus-export-history-body">
            <table id="exportHistory" class="table table-striped corpus-export-history-table" cellspacing="1">
                <colgroup>
                    <col class="col-export-id">
                    <col class="col-description">
                    <col class="col-status">
                    <col class="col-time">
                    <col class="col-time">
                    <col class="col-time">
                    <col class="col-selectors">
                    <col class="col-tagging">
                    <col class="col-tagging">
                    <col class="col-operations">
                </colgroup>
                <thead>
                <tr>
                    <th class="col-export-id">Id</th>
                    <th class="col-description">Description</th>
                    <th class="col-status">Status</th>
                    <th class="col-time">Submitted</th>
                    <th class="col-time" title="Processing start time">Started</th>
                    <th class="col-time" title="Processing end time">Finished</th>
                    <th class="col-selectors">Filters</th>
                    <th class="col-tagging">Tagging method</th>
                    <th class="col-tagging">Format</th>
                    <th class="col-operations">Operations</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$exports item=export}
                    {assign var=export_status_class value=$export.status|regex_replace:"/[^a-zA-Z0-9_-]+/":"-"|lower}
                    <tr>
                        <td class="col-export-id">{$export.export_id}</td>
                        <td class="col-description"><b>{$export.description}</b></td>
                        <td class="col-status export_status" id="export_status_{$export.export_id}"><span class="corpus-export-status status-{$export_status_class}">{$export.status}</span></td>
                        <td class="col-time">
                            <span class="administration-activities-time corpus-export-time" title="{$export.datetime_submit|escape}">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                <span>{$export.datetime_submit|date_format:"%Y-%m-%d"}</span>
                                <small>{$export.datetime_submit|date_format:'%H:%M'}</small>
                            </span>
                        </td>
                        <td class="col-time">
                            <span class="administration-activities-time corpus-export-time" title="{$export.datetime_start|escape}">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                <span>{$export.datetime_start|date_format:"%Y-%m-%d"}</span>
                                <small>{$export.datetime_start|date_format:'%H:%M'}</small>
                            </span>
                        </td>
                        <td class="col-time" id="export_finish_{$export.export_id}">
                            <span class="administration-activities-time corpus-export-time" title="{$export.datetime_finish|escape}">
                                <i class="fa fa-clock-o" aria-hidden="true"></i>
                                <span>{$export.datetime_finish|date_format:"%Y-%m-%d"}</span>
                                <small>{$export.datetime_finish|date_format:'%H:%M'}</small>
                            </span>
                        </td>
                        <td class="col-selectors">
                            <span class="corpus-export-filters-icon" title="Selectors: {$export.selectors|trim|escape}{if $export.extractors} | Extractors: {$export.extractors|escape}{/if}{if $export.indices} | Indices: {$export.indices|escape}{/if}">
                                <i class="fa fa-sliders" aria-hidden="true"></i>
                            </span>
                        </td>
                        <td class="col-tagging export_column">{$export.tagging}</td>
                        <td class="col-tagging export_column">{$export.export_format|default:'legacy'}</td>
                        <td class="col-operations corpus-export-action-cell">
                            <span class="corpus-export-operation">
                                <button class="btn btn-xs btn-default export_repeat_button" id="{$export.export_id}" title="Run again">
                                    <i class="fa fa-repeat" aria-hidden="true"></i>
                                </button>
                            </span>
                            <span id="export_message_{$export.export_id}" class="corpus-export-operation">
                            {if $export.errors > 0}
                                <button class="btn btn-xs btn-warning export_message_button" id = "{$export.export_id}" title="Errors">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                </button>
                            {/if}
                            </span>
                            <span id="export_stats_{$export.export_id}" class="corpus-export-operation">
                            {if $export.status == "done" && $export.statistics != ""}
                                <button class="btn btn-xs btn-info export_stats_button" id="{$export.export_id}" title="Statistics"><i class="fa fa-bar-chart" aria-hidden="true"></i></button>
                            {/if}
                            </span>
                            <span id="export_download_{$export.export_id}" class="corpus-export-operation">
                            {if $export.status == "done"}
                                <a href="index.php?page=export_download&amp;export_id={$export.export_id}" title="Download">
                                    <button class="btn btn-xs btn-primary"><i class="fa fa-download" aria-hidden="true"></i></button>
                                </a>
                            {else}
                                <span class="corpus-export-not-ready">not ready</span>
                            {/if}
                            </span>
                        </td>
                    </tr>
                {/foreach}
                {if $exports|@count==0}
                    <tr>
                        <td colspan="9" class="corpus-export-empty"><i class="fa fa-info-circle" aria-hidden="true"></i> History of exports is empty</td>
                    </tr>
                {/if}
                </tbody>
            </table>
        </div>
        <div class="panel-footer corpus-export-footer">
            <button id="newExportButton" type="button" class="btn btn-primary corpus-export-new-button"><i class="fa fa-plus" aria-hidden="true"></i> New export</button>
        </div>
    </div>
</div>

<br style="clear: both;"/>

<div class="modal fade settingsModal corpus-export-modal" id="export_stats_modal" role="dialog">
    <div class="modal-dialog corpus-export-stats-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> Export statistics</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body corpus-export-modal-body-small corpus-export-stats-body" id="export_stats_body">
                <div class="corpus-export-stats-loader"><div class="loader"></div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade settingsModal corpus-export-modal" id="export_message_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Export message</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body corpus-export-modal-body-small corpus-export-message-body corpus-export-message-body-padded" id="export_message_body">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
