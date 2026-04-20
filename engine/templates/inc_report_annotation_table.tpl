{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div id="col-content" class="col-main {if $flags_active && $config_active}col-md-11{elseif $flags_active}col-md-11{elseif $config_active}col-md-12{else}col-md-12{/if} scrollingWrapper report-annotation-table-column">
    <div class="panel panel-default administration-content-panel report-annotation-table-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-tags" aria-hidden="true"></i></span>
            <span>Annotations</span>
        </div>
        <div class="panel-body report-viewer-content-body report-annotation-table-body">
            <div id="content">
                <div id="leftContent" class="annotations scrolling content report-annotation-table-scroll">
                    <div class="administration-table-wrapper report-annotation-table-wrapper">
                        <table class="table table-striped table-hover administration-table report-annotation-table" id="public" cellspacing="1">
                            <thead>
                            <tr>
                                <th>Text phrase</th>
                                <th>Lemma</th>
                                <th>Category</th>
                                <th>Cross-language ID</th>
                            </tr>
                            </thead>
                            <tbody id="public_corpora_table">
                            {foreach from=$anns item=ann}
                                <tr title="{$ann.id}">
                                    <td class="report-annotation-table-text" title="{$ann.text|escape}">{$ann.text}</td>
                                    <td class="report-annotation-table-lemma" title="{$ann.lemma|escape}">{$ann.lemma}</td>
                                    <td class="report-annotation-table-category" title="{$ann.type|escape}">{$ann.type}</td>
                                    <td class="report-annotation-table-eid" title="{$ann.eid|escape}">{$ann.eid}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
