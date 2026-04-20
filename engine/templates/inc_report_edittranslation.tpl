<div id="col-agreement" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper report-edittranslation-content-column">
    <div class="panel panel-primary administration-content-panel report-edittranslation-content-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-language" aria-hidden="true"></i></span>
            <span>Document content</span>
        </div>
        <div class="panel-body report-viewer-content-body report-edittranslation-content-body">
            {if $no_translation}
                <div class="report-edittranslation-warning">
                    <div class="report-edittranslation-warning-title">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                        <span>Translation unavailable</span>
                    </div>
                    <div class="report-edittranslation-warning-text">
                        This document does not have a parent document, so it cannot be translated.
                    </div>
                    <div class="report-edittranslation-warning-link">
                        Set Parent report ID in
                        <a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=metadata&amp;id={$report_id}">
                            Metadata
                        </a>
                    </div>
                </div>
            {else}
                <div class="row scrollingAccordion report-edittranslation-grid">
                    <div class="col-md-6 report-edittranslation-pane-column">
                        <div class="panel panel-default report-edittranslation-pane">
                            <div class="panel-heading report-edittranslation-pane-heading">
                                <span class="report-edittranslation-pane-icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                                <span>Translation</span>
                            </div>
                            <div class="panel-body report-edittranslation-pane-body">
                                <textarea id="leftContent" class="annotations scrolling content report-edittranslation-textarea">{$content}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 report-edittranslation-pane-column">
                        <div class="panel panel-default report-edittranslation-pane">
                            <div class="panel-heading report-edittranslation-pane-heading">
                                <span class="report-edittranslation-pane-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
                                <span>Original document</span>
                                <a class="report-edittranslation-source-link" href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=preview&amp;id={$parent_report.id}">
                                    {$parent_report.title}
                                </a>
                            </div>
                            <div class="panel-body report-edittranslation-pane-body">
                                <div id="rightContent" class="annotations content scrolling report-edittranslation-source">
                                    <div id="report_content" class="report-edittranslation-source-content">{$parent_content|format_annotations}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
        <div class="panel-footer report-edittranslation-footer">
            <button id="saveTranslation" class="btn btn-primary report-edittranslation-save-button">
                <i class="fa fa-check" aria-hidden="true"></i>
                <span>Save</span>
            </button>
        </div>
    </div>
</div>
