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

<div id="col-document-annotation-categories" class="col-md-4 scrollingWrapper report-viewer-content-column report-topic-sidebar-column">
    <div class="panel panel-info administration-content-panel report-viewer-content-panel report-document-annotation-categories-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
            <span>Document annotation categories</span>
        </div>
        <div class="panel-body report-viewer-content-body report-topic-sidebar-body report-document-annotation-categories-body">
            <form method="POST" class="report-document-annotation-categories-form" onsubmit="return false;">
                <input type="hidden" name="action" value="document_annotation_categories_save"/>
                <input type="hidden" name="report_id" value="{$row.id}"/>

                <div class="report-document-annotation-categories-toolbar">
                    <div class="report-document-annotation-categories-toolbar-copy">
                        <div class="report-document-annotation-categories-toolbar-title">Whole-document categories</div>
                        <div class="report-document-annotation-categories-toolbar-help">Selections are saved automatically after each checkbox change.</div>
                    </div>
                    <span class="report-document-annotation-categories-status text-muted is-saved" id="documentAnnotationCategoriesStatus">Saved</span>
                </div>

                <div class="report-document-annotation-categories-selected">
                    <div class="report-document-annotation-categories-selected-header">
                        <div class="report-document-annotation-categories-selected-title">Selected categories</div>
                        <span class="report-document-annotation-categories-selected-count" id="documentAnnotationCategoriesSelectedCount">0</span>
                    </div>
                    <ul class="report-document-annotation-categories-selected-list" id="documentAnnotationCategoriesSelectedList">
                        {foreach from=$document_annotation_category_selected item=selected}
                            <li>
                                <span class="report-document-annotation-categories-selected-set">{$selected.set_name}</span>
                                <span class="report-document-annotation-categories-selected-separator">/</span>
                                <span class="report-document-annotation-categories-selected-subset">{if $selected.subset_name}{$selected.subset_name}{else}Uncategorized{/if}</span>
                                <span class="report-document-annotation-categories-selected-separator">/</span>
                                <span class="report-document-annotation-categories-selected-type">{$selected.type_name}</span>
                            </li>
                        {/foreach}
                    </ul>
                    <div class="report-document-annotation-categories-empty{if $document_annotation_category_selected|@count > 0} report-document-annotation-categories-empty-hidden{/if}" id="documentAnnotationCategoriesEmpty">
                        No categories selected for this document.
                    </div>
                </div>

                <div class="scrolling report-topic-list-wrapper report-document-annotation-categories-list-wrapper">
                    {if $document_annotation_category_tree|@count > 0}
                        {foreach from=$document_annotation_category_tree item=set}
                            <div class="report-document-annotation-categories-set">
                                <div class="report-document-annotation-categories-set-title">{$set.name}</div>
                                {if $set.description}
                                    <div class="report-document-annotation-categories-set-description">{$set.description}</div>
                                {/if}

                                {foreach from=$set.subsets item=subset}
                                    <div class="report-document-annotation-categories-subset">
                                        <div class="report-document-annotation-categories-subset-title">{$subset.name}</div>
                                        {if $subset.description}
                                            <div class="report-document-annotation-categories-subset-description">{$subset.description}</div>
                                        {/if}
                                        <ul class="report-document-annotation-categories-type-list">
                                            {foreach from=$subset.types item=type}
                                                <li>
                                                    <label class="report-document-annotation-categories-type-option">
                                                        <input
                                                            type="checkbox"
                                                            class="document-annotation-category-checkbox"
                                                            name="annotation_type_ids[]"
                                                            value="{$type.id}"
                                                            data-set-name="{$set.name|escape}"
                                                            data-subset-name="{$subset.name|escape}"
                                                            data-type-name="{$type.name|escape}"
                                                            {if in_array($type.id, $document_annotation_category_selected_ids)}checked="checked"{/if}/>
                                                        <span class="report-document-annotation-categories-type-copy">
                                                            <span class="report-document-annotation-categories-type-name">{$type.name}</span>
                                                            {if $type.description}
                                                                <span class="report-document-annotation-categories-type-description">{$type.description}</span>
                                                            {/if}
                                                        </span>
                                                    </label>
                                                </li>
                                            {/foreach}
                                        </ul>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                    {else}
                        <div class="report-document-annotation-categories-empty">
                            No annotation sets are configured for this perspective in this corpus.
                        </div>
                    {/if}
                </div>
            </form>
        </div>
    </div>
</div>
