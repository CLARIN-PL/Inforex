<div id="col-agreement" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper">
    <div class="panel panel-primary">
        <div class="panel-heading">Document content</div>
        <div class="panel-body">
            {if $no_translation}
                <div class="alert alert-warning">
                    <strong>Warning!</strong><br>
                    This document does not have a parent document, therefore it cannot be translated.<br>
                    Set Parent report ID at:
                    <a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=metadata&amp;id={$report_id}">
                        Metadata
                    </a>

                </div>
            {else}
                <div class="row scrollingAccordion">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">Translation</div>
                            <div class="panel-body" style="padding: 0">
                                <textarea id="leftContent" class="annotations scrolling content" style = "width: 100%;">{$content}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">Original document -
                                <a href="index.php?page=report&amp;corpus={$corpus_id}&amp;subpage=preview&amp;id={$parent_report.id}">
                                    {$parent_report.title}
                                </a>
                            </div>
                            <div class="panel-body" style="padding: 0">
                                <div id="rightContent" class="annotations content scrolling">
                                    <div style = "margin: 5px 15px 5px 15px;" id="report_content">{$parent_content|format_annotations}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
        <div class = "panel-footer clearfix">
            <button id = "saveTranslation" class = "btn btn-primary" style = "float: right;">Save</button>
        </div>
    </div>
</div>