{include file="inc_header2.tpl"}

<div class="modal fade settingsModal corpus-export-modal" id="korpuskopExportForm" role="dialog">
    <div class="modal-dialog corpus-export-modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-bar-chart" aria-hidden="true"></i> Prepare corpus report</h4>
            </div>
            <div class="modal-body corpus-export-modal-body">
                <input type="hidden" id="korpuskopCorpusKind" value="{$korpuskop_default_kind|escape}">
                <select name="select-export-format" class="form-control korpuskop-export-format corpus-korpuskop-hidden-format" aria-hidden="true" tabindex="-1"></select>
                <div class="corpus-korpuskop-modal-intro">
                    Move through the steps to choose report options, prepare the dataset, and review everything before starting.
                </div>
                <div class="corpus-korpuskop-modal-steps">
                    <span class="corpus-korpuskop-modal-step-indicator active" data-step-indicator="1">
                        <span class="corpus-korpuskop-modal-step-number">1</span>
                        <span class="corpus-korpuskop-modal-step-text">Report parameters</span>
                    </span>
                    <span class="corpus-korpuskop-modal-step-indicator" data-step-indicator="2">
                        <span class="corpus-korpuskop-modal-step-number">2</span>
                        <span class="corpus-korpuskop-modal-step-text">Export data</span>
                    </span>
                    <span class="corpus-korpuskop-modal-step-indicator" data-step-indicator="3">
                        <span class="corpus-korpuskop-modal-step-number">3</span>
                        <span class="corpus-korpuskop-modal-step-text">Report summary</span>
                    </span>
                </div>

                <div class="corpus-korpuskop-modal-step" data-step="1">
                    <div class="corpus-korpuskop-modal-step-card">
                    <table class="table table-striped corpus-export-form-table" cellspacing="1">
                        <tr>
                            <th style="width: 200px; vertical-align: top">Corpus type</th>
                            <td colspan="2">
                                <select class="form-control corpus-korpuskop-select" id="korpuskopCorpusTypeModal">
                                    <option value="document">Documents</option>
                                    <option value="dialog">Dialogs</option>
                                </select>
                                <p class="help-block" style="margin-bottom:0;">The corpus type selects the matching export format and report schema.</p>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: top">Focus words<br/><small style="font-weight: normal">Optionally provide words that should be passed to the report</small></th>
                            <td colspan="2">
                                <textarea id="korpuskopFocusWords" class="form-control corpus-korpuskop-focus-words" placeholder="One word or phrase per line&#10;e.g. war&#10;Russia&#10;energy"></textarea>
                            </td>
                        </tr>
                    </table>
                    </div>
                </div>

                <div class="corpus-korpuskop-modal-step" data-step="2" style="display:none;">
                    <div class="corpus-korpuskop-modal-step-card">
                        <div class="alert alert-warning corpus-korpuskop-step-warning" id="korpuskopStepValidationWarning" style="display:none;">
                            Please fix the export configuration before continuing to the summary step.
                        </div>
                    <table class="table table-striped corpus-export-form-table" cellspacing="1">
                        <tr>
                            <th style="width: 200px; vertical-align: top">Description</th>
                            <td colspan="2">
                                <textarea name="description" class="form-control corpus-export-description">Corpus report export</textarea>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: top">Tagging<br/><small style="font-weight: normal">Choose export's tagging set</small></th>
                            <td class="corpus-export-tagging-cell" style="text-align: left; vertical-align: top;">
                                <select name="select-tagging" class="form-control" style="min-width: 70px">
                                    <option value="final_or_tagger">Final or tagger (if final not present)</option>
                                    <option value="final">Final</option>
                                        <option value="user">User</option>
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
                            <th style="vertical-align: top">Selectors<br/><small style="font-weight: normal">Define criteria used to select documents to export</small></th>
                            <td class="flags"></td>
                            <td class="corpus-export-add-column" style="width: 40px; text-align: center; vertical-align: top;">
                            <div class="flag_template" style="display: none">
                                <div class="corpus-export-selector-card" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; margin: 2px;">
                                    <i class="fa fa-times-circle-o close" aria-hidden="true"></i>
                                    <div class="flags corpus-export-filter-box" style="vertical-align: middle;">
                                        Flag <select name="corpus_flag_id" style="font-size: 12px">
                                            <option style="font-style: italic" value="">Select flag</option>
                                            {foreach from=$corpus_flags item=flag}
                                                <option value="{$flag.corpora_flag_id}" title="{$flag.name}"><em>{$flag.short}</em></option>
                                            {/foreach}
                                        </select> :
                                        {foreach from=$flags item=flag}
                                            <img class="flag" src="gfx/flag_{$flag.flag_id}.png" value="{$flag.flag_id}" />
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary corpus-export-add-button new_selector" title="Add selector"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <th style="vertical-align: top">Extractors<br/><small style="font-weight: normal">Define exported annotation and relation layers</small></th>
                            <td class="extractors"></td>
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
                                                    <option value="{$flag.corpora_flag_id}" title="{$flag.name}"><em>{$flag.short}</em></option>
                                                {/foreach}
                                            </select>
                                            :
                                            {foreach from=$flags item=flag}
                                                <img class="flag" src="gfx/flag_{$flag.flag_id}.png" value="{$flag.flag_id}"/>
                                            {/foreach}
                                        </div>
                                    </div>
                                    <b>Export</b>
                                    <select class="select_mode">
                                        <option selected value="standard">Standard</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                    <div class="element_user corpus-export-elements-panel" style="margin: 4px; max-height: 300px; overflow: auto; display: none;">
                                        <table class="corpus-export-elements-table" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; width: 100%;">
                                            <tr>
                                                <td class="corpus-export-stage-cell" style="background: white !important;">
                                                    <label>Stage: </label>
                                                    <select class="annotation_stage_select">
                                                        <option value="final">final</option>
                                                        <option value="agreement">agreement</option>
                                                        <option value="relationagreement">relation agreement</option>
                                                        <option value="new">new</option>
                                                        <option value="discarded">discarded</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="corpus-export-users-scroll" style="max-height: 200px !important; overflow: auto;">
                                                        <table class="export_users" style="width: 100%;">
                                                            <thead><th>User</th><th></th></thead>
                                                            <tbody>
                                                            {foreach from=$users item = user}
                                                                <tr>
                                                                    <td class="username">{$user.screename}</td>
                                                                    <td class="text-center"><input class="user_checkbox" value="{$user.user_id}" type="checkbox"></td>
                                                                </tr>
                                                            {/foreach}
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr><td>{include file="inc_widget_annotation_layers_and_subsets.tpl"}</td></tr>
                                            <tr><td>{include file="inc_widget_relation_structure.tpl"}</td></tr>
                                        </table>
                                    </div>
                                    <div class="elements corpus-export-elements-panel" style="margin: 4px; max-height: 300px; overflow: auto;">
                                        <table class="corpus-export-elements-table" style="border: 1px solid #7D7D09; background: #FFFFD8; padding: 5px; width: 100%;">
                                            <tr><td>{include file="inc_widget_annotation_layers_and_subsets.tpl"}</td></tr>
                                            <tr><td>{include file="inc_widget_relation_structure.tpl"}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary corpus-export-add-button new_extractor" title="Add extractor"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            </td>
                        </tr>
                    </table>
                    </div>
                </div>
                <div class="corpus-korpuskop-modal-step" data-step="3" style="display:none;">
                    <div class="corpus-korpuskop-step-summary">
                        <strong>Report summary:</strong>
                        <span class="corpus-korpuskop-step-summary-kind">Type: documents</span>
                        <span class="corpus-korpuskop-step-summary-separator">|</span>
                        <span class="corpus-korpuskop-step-summary-focus">Focus: none</span>
                    </div>
                    <div class="corpus-korpuskop-step-summary corpus-korpuskop-step-summary-secondary">
                        <strong>Export summary:</strong>
                        <span class="corpus-korpuskop-step-summary-format">Format: CLARIN Parquet ZST</span>
                        <span class="corpus-korpuskop-step-summary-separator">|</span>
                        <span class="corpus-korpuskop-step-summary-tagging">Tagging: Final or tagger</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="korpuskopModalPrev" style="display:none;"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button>
                <button type="button" class="btn btn-default" id="korpuskopModalNext"><i class="fa fa-arrow-right" aria-hidden="true"></i> Next</button>
                <button type="button" class="btn btn-default corpus-export-check-button" id="korpuskopCheckExport" style="display:none;"><i class="fa fa-check-square-o" aria-hidden="true"></i> Check form</button>
                <button type="button" class="btn btn-primary corpus-export-confirm-button" id="korpuskopStartExport" style="display:none;"><i class="fa fa-play" aria-hidden="true"></i> Start report</button>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-primary administration-content-panel corpus-korpuskop-panel">
    <div class="panel-heading administration-content-heading corpus-korpuskop-heading">
        <div class="corpus-korpuskop-heading-main">
            <span class="administration-content-heading-icon corpus-korpuskop-heading-icon"><i class="fa fa-bar-chart" aria-hidden="true"></i></span>
            <span>Corpus report</span>
        </div>
        <div class="corpus-korpuskop-heading-actions">
            <a href="index.php?page=corpus_tasks&amp;corpus={$corpus.id}" class="btn btn-xs btn-default"><i class="fa fa-tasks" aria-hidden="true"></i> Tasks</a>
            <a href="index.php?page=corpus_export&amp;corpus={$corpus.id}" class="btn btn-xs btn-default"><i class="fa fa-archive" aria-hidden="true"></i> Exports</a>
        </div>
    </div>
    <div class="panel-body corpus-korpuskop-body">
        {if $korpuskop_error}
            <div class="alert alert-danger">{$korpuskop_error|escape}</div>
        {/if}

        {if $korpuskop_notice}
            <div class="alert alert-success">{$korpuskop_notice|escape}</div>
        {/if}

        {if $korpuskop_history_error}
            <div class="alert alert-warning">{$korpuskop_history_error|escape}</div>
        {/if}

        <div class="corpus-korpuskop-launch-panel">
            <div class="corpus-korpuskop-launch-copy">
                <div class="corpus-korpuskop-launch-eyebrow">Guided report preparation</div>
                <h3 class="corpus-korpuskop-launch-title">Build a corpus report in a few guided steps</h3>
                <p class="corpus-korpuskop-intro">
                    Choose the corpus type, define the scope of data to include, and start the report from one place.
                    Inforex prepares the required dataset in the background, shows progress as it runs, and then launches the report automatically.
                </p>
                <ul class="corpus-korpuskop-launch-benefits">
                    <li>Get a ready-to-use corpus report package prepared for analysis.</li>
                    <li>Track both preparation and report generation progress in one view.</li>
                    <li>Return later and continue from recent runs without repeating setup.</li>
                </ul>
            </div>
            <div class="corpus-korpuskop-actions">
                <button type="button" class="btn btn-primary" id="korpuskopOpenExportModal"><i class="fa fa-play-circle" aria-hidden="true"></i> Prepare report</button>
            </div>
        </div>

        <div class="panel panel-default corpus-korpuskop-history-panel">
            <div class="panel-heading corpus-korpuskop-history-heading">
                <span>Recent runs</span>
                <div class="corpus-korpuskop-history-heading-meta">
                    <span class="corpus-korpuskop-refresh-label">Refresh:</span>
                    <button type="button" class="corpus-korpuskop-refresh-toggle corpus-korpuskop-refresh-toggle-off" id="korpuskopHistoryRefreshState" aria-pressed="false">
                        <span class="corpus-korpuskop-refresh-toggle-track"><span class="corpus-korpuskop-refresh-toggle-knob"></span></span>
                        <span class="corpus-korpuskop-refresh-toggle-text">Off</span>
                    </button>
                    <span class="corpus-korpuskop-refresh-time" id="korpuskopHistoryRefreshTime">Last updated: -</span>
                </div>
            </div>
            <div class="panel-body">
                <p class="text-muted corpus-korpuskop-history-help">
                    From history you can download the finished ZIP or remove the entry together with the report files.
                </p>
                <table class="table table-striped corpus-korpuskop-history-table">
                    <thead>
                        <tr>
                            <th class="corpus-korpuskop-col-id">ID</th>
                            <th class="corpus-korpuskop-col-task">Task</th>
                            <th class="corpus-korpuskop-col-status">Status</th>
                            <th class="corpus-korpuskop-col-variant">Variant</th>
                            <th class="corpus-korpuskop-col-size">Size</th>
                            <th class="corpus-korpuskop-col-user">User</th>
                            <th class="corpus-korpuskop-col-finished">Finished</th>
                            <th class="corpus-korpuskop-col-actions">Actions</th>
                        </tr>
                        <tr class="corpus-korpuskop-history-filter-row">
                            <th><input type="text" id="korpuskopHistoryFilterRunId" class="form-control input-sm corpus-korpuskop-history-filter-input" placeholder="Filter"></th>
                            <th><input type="text" id="korpuskopHistoryFilterTaskId" class="form-control input-sm corpus-korpuskop-history-filter-input" placeholder="Filter"></th>
                            <th>
                                <select id="korpuskopHistoryStatusFilter" class="form-control input-sm corpus-korpuskop-history-filter-select">
                                    <option value="all">All (0)</option>
                                    <option value="active">Active (0)</option>
                                    <option value="new">New (0)</option>
                                    <option value="process">Processing (0)</option>
                                    <option value="done">Done (0)</option>
                                    <option value="error">Error (0)</option>
                                </select>
                            </th>
                            <th>
                                <select id="korpuskopHistoryVariantFilter" class="form-control input-sm corpus-korpuskop-history-filter-select">
                                    <option value="all">All variants</option>
                                </select>
                            </th>
                            <th><input type="text" id="korpuskopHistoryFilterSize" class="form-control input-sm corpus-korpuskop-history-filter-input" placeholder="e.g. 12.5"></th>
                            <th><input type="text" id="korpuskopHistoryFilterUser" class="form-control input-sm corpus-korpuskop-history-filter-input" placeholder="Filter"></th>
                            <th><input type="text" id="korpuskopHistoryFilterFinished" class="form-control input-sm corpus-korpuskop-history-filter-input" placeholder="YYYY-MM-DD"></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="corpus-korpuskop-history-body">
                    {foreach from=$korpuskop_runs item=run}
                        <tr data-run-status="{$run.status|escape}" data-run-id="{$run.run_id|escape}" data-run-task-id="{$run.task_id|escape}" data-run-variant="{$run.input_kind|escape}" data-run-size="{if $run.file_size}{math equation="x / 1048576" x=$run.file_size format="%.2f"}{/if}" data-run-user="{$run.screename|default:'-'|escape}" data-run-finished="{$run.finished_at|escape}">
                            <td>{$run.run_id|escape}</td>
                            <td>{if $run.task_id}<a href="index.php?page=corpus_korpuskop&amp;corpus={$corpus.id}&amp;task_id={$run.task_id|escape}&amp;show_task=1">{$run.task_id|escape}</a>{else}<span class="text-muted">-</span>{/if}</td>
                            <td>
                                <span class="corpus-korpuskop-status-badge corpus-korpuskop-status-badge-{$run.status|escape}">
                                    {if $run.status == 'new'}
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                    {elseif $run.status == 'process'}
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                    {elseif $run.status == 'done'}
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                    {elseif $run.status == 'error'}
                                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    {else}
                                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    {/if}
                                    {$run.status|escape}
                                </span>
                            </td>
                            <td>{$run.input_kind|escape}</td>
                            <td>{if $run.file_size}{math equation="x / 1048576" x=$run.file_size format="%.2f"} MB{/if}</td>
                            <td>{$run.screename|default:'-'|escape}</td>
                            <td>{$run.finished_at|escape}</td>
                            <td class="corpus-korpuskop-history-actions">
                                {if $run.task_id}
                                    <a class="btn btn-xs btn-default" href="index.php?page=corpus_korpuskop&amp;corpus={$corpus.id}&amp;task_id={$run.task_id|escape}&amp;show_task=1" title="Show task status"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                {/if}
                                {if $run.status == 'done' && $run.real_run_id && $run.file_size}
                                    <a class="btn btn-xs btn-default" href="index.php?page=korpuskop_download&amp;corpus={$corpus.id}&amp;run_id={$run.real_run_id}" title="Download ZIP"><i class="fa fa-download" aria-hidden="true"></i></a>
                                {/if}
                                {if $run.real_run_id}
                                    <form method="post" class="corpus-korpuskop-delete-form" onsubmit="return confirm('Remove the history entry and related report files?');">
                                        <input type="hidden" name="korpuskop_action" value="delete_run">
                                        <input type="hidden" name="run_id" value="{$run.real_run_id|escape}">
                                        <button type="submit" class="btn btn-xs btn-danger" title="Remove history entry and files"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                {/if}
                            </td>
                        </tr>
                    {foreachelse}
                        <tr>
                            <td colspan="8" class="text-muted">No saved report history.</td>
                        </tr>
                    {/foreach}
                        <tr class="corpus-korpuskop-history-empty-row" style="display:none;">
                            <td colspan="8" class="text-muted">No runs match the selected filter.</td>
                        </tr>
                    </tbody>
                </table>
                <div class="corpus-korpuskop-history-pagination">
                    <div class="corpus-korpuskop-history-pagination-left">
                        <div class="corpus-korpuskop-history-pagination-summary" id="korpuskopHistoryPaginationSummary">Showing 0–0 of 0</div>
                        <label class="corpus-korpuskop-history-page-size-label" for="korpuskopHistoryPageSize">Rows per page</label>
                        <select id="korpuskopHistoryPageSize" class="form-control input-sm corpus-korpuskop-history-page-size-select">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                    <div class="corpus-korpuskop-history-pagination-actions">
                        <button type="button" class="btn btn-default btn-xs" id="korpuskopHistoryPrevPage"><i class="fa fa-chevron-left" aria-hidden="true"></i> Prev</button>
                        <span class="corpus-korpuskop-history-pagination-page" id="korpuskopHistoryPageIndicator">Page 1 / 1</span>
                        <button type="button" class="btn btn-default btn-xs" id="korpuskopHistoryNextPage">Next <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-info corpus-korpuskop-result-panel" id="korpuskopTaskProgress" data-task-id="{$korpuskop_active_task_id|escape}" data-export-id="{$korpuskop_active_export_id|escape}" style="{if !$korpuskop_active_task_id && !$korpuskop_active_export_id}display:none;{/if}">
            <div class="panel-heading">Active report task <span class="korpuskop-task-id-label">{if $korpuskop_active_task_id}#{$korpuskop_active_task_id|escape}{/if}</span></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="corpus-korpuskop-meta-box corpus-korpuskop-meta-box-status">
                            <span class="corpus-korpuskop-meta-label">Status</span>
                            <span class="corpus-korpuskop-meta-value korpuskop-task-status">new</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="corpus-korpuskop-meta-box corpus-korpuskop-meta-box-progress">
                            <span class="corpus-korpuskop-meta-label">Progress</span>
                            <span class="corpus-korpuskop-meta-value korpuskop-task-percent">0%</span>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="corpus-korpuskop-meta-box corpus-korpuskop-meta-box-queue">
                            <span class="corpus-korpuskop-meta-label">Queue</span>
                            <span class="corpus-korpuskop-meta-value korpuskop-task-queue">-</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="corpus-korpuskop-meta-box corpus-korpuskop-meta-box-stage">
                            <span class="corpus-korpuskop-meta-label">Stage</span>
                            <span class="corpus-korpuskop-meta-value korpuskop-task-stage">queued</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="corpus-korpuskop-meta-box corpus-korpuskop-meta-box-report">
                            <span class="corpus-korpuskop-meta-label">Report</span>
                            <a href="#" class="btn btn-xs btn-default korpuskop-task-download" style="display:none;"><i class="fa fa-download" aria-hidden="true"></i> Download ZIP</a>
                        </div>
                    </div>
                </div>

                <div class="progress corpus-korpuskop-progressbar">
                    <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">0%</div>
                </div>

                <div class="corpus-korpuskop-meta-box corpus-korpuskop-status-box">
                    <strong>Message</strong><br>
                    <span class="korpuskop-task-message">The task is waiting for export preparation to begin.</span>
                </div>

                <div class="corpus-korpuskop-meta-box corpus-korpuskop-status-box">
                    <strong>Export</strong><br>
                    <span class="korpuskop-export-status">not started</span>
                </div>

                <div class="corpus-korpuskop-meta-box corpus-korpuskop-status-box corpus-korpuskop-diagnostics-box">
                    <strong>Start mode</strong><br>
                    <span class="korpuskop-start-mode">not started</span>
                </div>
            </div>
        </div>
    </div>
</div>
