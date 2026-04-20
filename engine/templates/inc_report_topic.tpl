{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="col-content" class="{if $flags_active}col-md-7{else}col-md-8{/if} scrollingWrapper report-viewer-content-column report-topic-content-column">
    <div class="panel panel-primary administration-content-panel report-viewer-content-panel report-topic-content-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
            <span>Document content</span>
        </div>
        <div class="panel-body report-viewer-content-body">
            <div id="content" class="annotations scrolling content report-viewer-document-content report-topic-document-content">
                <div class="contentBox report-viewer-content-box">{$content_inline|format_annotations}</div>
            </div>
        </div>
    </div>
</div>

<div id="col-topic" class="col-md-4 scrollingWrapper report-viewer-content-column report-topic-sidebar-column">
    <div class="panel panel-info administration-content-panel report-viewer-content-panel report-topic-sidebar-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
            <span>Topic</span>
        </div>
        <div class="panel-body report-viewer-content-body report-topic-sidebar-body">
            <div id="list_of_topics" class="scrolling report-topic-list-wrapper">
                <ul class="topics report-topic-list">
                {foreach from=$topics item=topic}
                    <li>
                        <a href="#" id="topic_{$topic.id}" class="report-topic-link{if $row.type==$topic.id} marked{/if}">
                            <span class="report-topic-link-label">{$topic.name}</span>
                        </a>
                    </li>
                {/foreach}
                </ul>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="report_id" value="{$row.id}"/>
