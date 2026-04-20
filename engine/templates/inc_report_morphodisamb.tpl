{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div id="dialog" title="Błąd" style="display: none;">
    <p>
        <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
        <span class="message"></span>
    </p>
    <p><i><a href="">Refresh page.</a></i></p>
</div>

<div class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper report-morphodisamb-column" id="col-main">
    <div class="panel-group report-morphodisamb-accordion" id="morphodisambAccordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default report-morphodisamb-accordion-panel">
            <div class="panel-heading report-morphodisamb-accordion-heading" role="tab">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#morphodisambAccordion" href="#collapseInstructions" aria-expanded="false" aria-controls="collapseInstructions" class="report-morphodisamb-accordion-toggle">
                        <span class="report-morphodisamb-accordion-copy">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <span>Instructions</span>
                        </span>
                        <i class="fa fa-caret-down report-morphodisamb-accordion-chevron" aria-hidden="true"></i>
                    </a>
                </h4>
            </div>
            <div id="collapseInstructions" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body report-morphodisamb-instructions-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <ul class="report-morphodisamb-instructions-list">
                                <li>
                                    Choose tags using up <i class="fa fa-caret-up" aria-hidden="true"></i> or down <i class="fa fa-caret-down" aria-hidden="true"></i> arrow keys and pressing space bar or by clicking on the selected tag.
                                </li>
                                <li>
                                    By holding CTLR button you can select multiple tags.
                                </li>
                                <li>
                                    Tags marked with <i class="fa fa-check-circle" aria-hidden="true"></i> icon and blue background will be saved as your decision.
                                </li>
                                <li>
                                    In order to save the chosen tag for token in the middle card move to the next card. This is accomplished by clicking on the arrow buttons on the left and right or pressing left <i class="fa fa-caret-left" aria-hidden="true"></i> or right <i class="fa fa-caret-right" aria-hidden="true"></i> arrow keys.
                                </li>
                                <li>
                                    To add missing tag fill the 'base' and 'tag' inputs and press "<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add interpretation option" (note that both 'base' and 'tag' are required).
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-primary administration-content-panel report-morphodisamb-content-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
            <span>Document content</span>
        </div>
        <div id="widget_text" class="panel-body report-viewer-content-body report-morphodisamb-content-body">
            <div id="content">
                <div id="leftContent" class="annotations scrolling content report-preview-document-content report-morphodisamb-document-content">
                    <div class="contentBox {$report.format} report-preview-content-box report-morphodisamb-content-box">{$content|format_annotations}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-primary administration-content-panel report-morphodisamb-module-panel">
        <div class="panel-heading administration-content-heading report-viewer-main-heading">
            <span class="administration-content-heading-icon report-viewer-heading-icon"><i class="fa fa-code-fork" aria-hidden="true"></i></span>
            <span>Morphological disambiguation</span>
        </div>
        <div id="widget_text" class="panel-body report-viewer-content-body report-morphodisamb-module-body">
            <div id="morpho-tagger" class="row report-morphodisamb-tagger">
                <div class="overlay" data-module-id="overlay">
                    <p data-module-id="overlay-text">Please choose annotators to compare. <br>
                        (Press <i class="fa fa-cog fa-4" aria-hidden="true"></i> icon in top right corner)
                    </p>
                </div>
                <div class="col-sm-1 report-morphodisamb-nav-column">
                    <button id="prev" type="button" class="btn btn-secondary btn-side-morpho">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </button>
                </div>

                <div class="col-sm-2 token-card">
                    <div class="token-card-content">
                        <h4 class="morpho-token text-center">Token</h4>
                        <ul class="possible-tags-list">
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-2 token-card">
                    <div class="token-card-content">
                        <h4 class="morpho-token text-center">Token</h4>
                        <ul class="possible-tags-list">
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-2 token-card card-main">
                    <div class="token-card-content">
                        <h4 class="morpho-token text-center">Token</h4>
                        <ul class="possible-tags-list">
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                        </ul>
                        <hr>

                        <div class="form-inline" id="editable-select-container">
                            <input id="lemma-base" type="text" class="form-control" placeholder="base">
                            <select id="editable-select" class="form-control" placeholder="tag"></select>
                        </div>
                        <button type="button" id="add-tag" class="btn btn-primary btn-block">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            <span>Add interpretation option</span>
                        </button>
                    </div>
                </div>
                <div class="col-sm-2 token-card">
                    <div class="token-card-content">
                        <h4 class="morpho-token text-center">Token</h4>
                        <ul class="possible-tags-list">
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-2 token-card">
                    <div class="token-card-content">
                        <h4 class="morpho-token text-center">Token</h4>
                        <ul class="possible-tags-list">
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                            <li>lemma tag:1:2:3</li>
                        </ul>
                    </div>
                </div>

                <div class="col-sm-1 report-morphodisamb-nav-column">
                    <button id="next" type="button" class="btn btn-secondary btn-side-morpho">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {ldelim}
        var morphoTokenTags = {$tokensTags|@json_encode};
        var morphoModule = new MorphoTagger($('#morpho-tagger'), $('span.token'), morphoTokenTags, $('#editable-select'));
    {rdelim});
</script>
