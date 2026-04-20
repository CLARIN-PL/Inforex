{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-main {if $flags_active}col-md-5{else}col-md-6{/if} scrollingWrapper report-viewer-content-column report-cleanup-content-column">
	<div class="panel panel-primary administration-content-panel report-viewer-content-panel report-cleanup-content-panel">
		<div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
            <span>Edit content</span>
        </div>
		<div class="panel-body report-viewer-content-body report-cleanup-content-body">
			{include file="inc_report_wrong_changes.tpl"}
			<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
                {if $full_edit_disabled_reason}
                    <div class="alert alert-warning report-cleanup-alert">
                        {$full_edit_disabled_reason}
                        Content length: {$content_edit_length}. Annotations: {$annotations_count}.
                    </div>
                {/if}
                {if $disable_codemirror}
                    <div class="alert alert-info report-cleanup-alert">
                        Code editor is disabled by default in this perspective to keep the page responsive. Plain textarea mode is active.
                    </div>
                {/if}
                <div class="report-cleanup-toolbar">
                    {if $disable_codemirror}
                        <a href="#" class="btn btn-xs btn-default report-cleanup-tool-button" id="enable_codemirror"><i class="fa fa-code" aria-hidden="true"></i> Enable code editor</a>
                    {else}
                        <a href="#" class="btn btn-xs btn-default report-cleanup-tool-button" id="disable_codemirror_button"><i class="fa fa-align-left" aria-hidden="true"></i> Use plain textarea</a>
                    {/if}
                </div>
				<div id="edit_content" class="report-viewer-document-content report-cleanup-edit-content">
					<textarea name="content" class="scrolling report-cleanup-textarea" id="report_content">{if $wrong_changes}{$wrong_document_content|escape}{else}{$content_edit|escape}{/if}</textarea>
				</div>

				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="document_update_content" name="action"/>
                <input type="hidden" value="{if $disable_codemirror}1{else}0{/if}" id="disable_codemirror"/>
                <input type="hidden" value="{$use_codemirror|default:0}" id="use_codemirror"/>
				<div class="panel-footer report-cleanup-footer">
                    {if $ex}
						<div class="alert alert-danger">
							The document cannot be modified as an exception raised<br/><b>{$ex->getMessage()}</b>.
						</div>
                    {elseif $annotations_count>0}
						<div class="alert alert-danger">
							This document cannot be edited in this perspective because it contains annotations. Use <a href="index.php?page=report&subpage=edit&id={$report_id}">Content</a> instead.
						</div>
					{else}
						<button type="submit" class="btn btn-primary report-cleanup-save-button" name="formatowanie" id="formating">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>Save</span>
                        </button>
                    {/if}
				</div>
			</form>
		</div>
	</div>
</div>

<div id="col-source" class="{if $flags_active}col-md-6{else}col-md-6{/if} scrollingWrapper report-viewer-content-column report-cleanup-source-column">
	<div class="panel panel-info administration-content-panel report-viewer-content-panel report-cleanup-source-panel">
		<div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-external-link" aria-hidden="true"></i></span>
            <span>Source</span>
        </div>
		<div class="panel-body report-viewer-content-body report-cleanup-source-body">
            {if $row.source}
                <div class="report-cleanup-source-placeholder" id="source_placeholder">
                    <div class="panel panel-default administration-content-panel report-cleanup-source-placeholder-panel">
                        <div class="panel-heading administration-content-heading report-cleanup-source-placeholder-heading">
                            <span class="administration-content-heading-icon report-cleanup-source-placeholder-heading-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
                            <span>Load source when needed</span>
                        </div>
                        <div class="panel-body report-cleanup-source-placeholder-body">
                            <div class="report-cleanup-source-placeholder-head">
                                <div class="report-cleanup-source-placeholder-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></div>
                                <div class="report-cleanup-source-placeholder-copy">
                                    <div class="report-cleanup-source-placeholder-text">The source panel is deferred to keep this perspective responsive for large documents and heavy source pages.</div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer report-cleanup-source-placeholder-actions">
                            <button type="button" class="btn btn-default report-cleanup-load-source-button" id="load_source" onclick="loadCleanupSourceFrame(); return false;">
                                <i class="fa fa-bolt" aria-hidden="true"></i>
                                <span>Load source</span>
                            </button>
                        </div>
                    </div>
                </div>
			    <iframe src="about:blank" data-src="{$row.source}" class="scrolling report-cleanup-source-frame report-cleanup-source-frame-lazy" id="cleanup_source_frame"></iframe>
            {else}
                <div class="report-cleanup-source-placeholder report-cleanup-source-placeholder-empty">
                    <div class="report-cleanup-source-placeholder-icon"><i class="fa fa-unlink" aria-hidden="true"></i></div>
                    <div class="report-cleanup-source-placeholder-copy">
                        <div class="report-cleanup-source-placeholder-title">No source available</div>
                        <div class="report-cleanup-source-placeholder-text">This document does not have a source URL assigned.</div>
                    </div>
                </div>
            {/if}
		</div>
		<div class="panel-footer report-cleanup-footer">
			Link: <a href="{$row.source}" target="_blank">{$row.source}</a>
		</div>
	</div>
</div>
