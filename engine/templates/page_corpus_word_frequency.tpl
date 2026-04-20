{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl" content_class="no_padding"}

<div class="container-fluid admin_tables corpus-word-frequency-page">
    <div class="panel administration-content-panel corpus-word-frequency-shell">
        <div class="panel-heading administration-content-heading corpus-word-frequency-main-heading">
            <div class="corpus-word-frequency-main-heading-copy">
                <span class="administration-content-heading-icon"><i class="fa fa-bar-chart" aria-hidden="true"></i></span>
                <span>Words frequency</span>
            </div>
            <div class="corpus-word-frequency-heading-actions corpus-word-frequency-main-actions">
                <button type="button" id="export_selected" class="btn corpus-word-frequency-export-btn corpus-word-frequency-export-btn-primary">
                    <i class="fa fa-table" aria-hidden="true"></i>
                    <span>Frequency List CSV</span>
                </button>
                <button type="button" id="export_by_subcorpora" class="btn corpus-word-frequency-export-btn corpus-word-frequency-export-btn-secondary">
                    <i class="fa fa-bar-chart" aria-hidden="true"></i>
                    <span>Distribution Chart CSV</span>
                </button>
            </div>
        </div>
        <div class="panel-body corpus-word-frequency-body">
            <div class="corpus-word-frequency-toolbar">
                <form method="GET" action="index.php" class="corpus-word-frequency-form">
                    <input type="hidden" name="page" value="{$page}"/>
                    <input type="hidden" name="corpus" value="{$corpus.id}"/>

                    <div class="corpus-word-frequency-form-title">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        <span>Filters</span>
                    </div>

                    <label class="corpus-word-frequency-field corpus-word-frequency-field-pos">
                        <span>Part of speech</span>
                        <select name="ctag">
                            <option value="">All</option>
                            {foreach from=$classes item=class}
                                <option value="{$class}" {if $class==$ctag}selected="selected"{/if}>{$class}</option>
                            {/foreach}
                        </select>
                    </label>

                    <label class="corpus-word-frequency-field">
                        <span>Subcorpus</span>
                        <select name="subcorpus_id">
                            <option value="">All</option>
                            {foreach from=$subcorpora item=s}
                                <option value="{$s.subcorpus_id}" {if $s.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$s.name}</option>
                            {/foreach}
                        </select>
                    </label>

                    <label class="corpus-word-frequency-field corpus-word-frequency-search">
                        <span>Phrase</span>
                        <input type="text" name="phrase" value="{$phrase}"/>
                    </label>

                    <div class="corpus-word-frequency-actions">
                        <button type="submit" class="btn btn-primary corpus-word-frequency-apply">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>Apply</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="row corpus-word-frequency-grid">
                <div class="col-md-4 corpus-word-frequency-column">
                    <div id="words_frequency" class="panel corpus-word-frequency-content-panel">
                        <div class="panel-heading corpus-word-frequency-subheading">
                            <div class="corpus-word-frequency-heading-copy">
                                <i class="fa fa-list-ol" aria-hidden="true"></i>
                                <span>Words frequency</span>
                            </div>
                        </div>
                        <div class="panel-body corpus-word-frequency-subbody">
                            <div id="words_frequency_loading" class="administration-wsd-loading corpus-word-frequency-loading">
                                <img src="gfx/ajax.gif" alt="Loading"/>
                                <span>Loading word frequencies...</span>
                            </div>
                            <div id="words_frequency_empty" class="administration-wsd-loading corpus-word-frequency-empty-state" style="display: none;">
                                <div class="corpus-word-frequency-empty-copy">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    <div class="corpus-word-frequency-empty-text">
                                        <strong>No matching words</strong>
                                        <span>Try changing the phrase, part of speech, or subcorpus filters.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flexigrid corpus-word-frequency-flexigrid corpus-word-frequency-panel-content is-hidden">
                                <table id="words_frequences">
                                    <tr>
                                        <td style="vertical-align: middle"><div>Loading ... </div></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 corpus-word-frequency-column">
                    <div id="words_distribution" class="panel corpus-word-frequency-content-panel corpus-word-frequency-chart-panel">
                        <div class="panel-heading corpus-word-frequency-subheading corpus-word-frequency-chart-heading">
                            <div class="corpus-word-frequency-heading-copy">
                                <i class="fa fa-area-chart" aria-hidden="true"></i>
                                <span>Word distribution across subcorpora</span>
                            </div>
                            <div id="countby" class="corpus-word-frequency-countby">
                                <span>Count</span>
                                <a href="#" class="active words" type="words">Words</a>
                                <span class="corpus-word-frequency-countby-separator">/</span>
                                <a href="#" class="documents" type="documents">Documents</a>
                            </div>
                        </div>
                        <div class="panel-body corpus-word-frequency-subbody corpus-word-frequency-chart-body">
                            <div id="words_distribution_loading" class="administration-wsd-loading corpus-word-frequency-loading corpus-word-frequency-chart-loading">
                                <img src="gfx/ajax.gif" alt="Loading"/>
                                <span>Loading subcorpus distribution...</span>
                            </div>
                            <div class="corpus-word-frequency-chart-stage corpus-word-frequency-panel-content">
                                <a id="chart_link" class="corpus-word-frequency-chart-link" href="#" download="word-distribution.png" title="Download chart as PNG" style="display: none;">
                                    <i class="fa fa-download" aria-hidden="true"></i>
                                    <span>PNG</span>
                                </a>
                                <div id="words_per_subcorpus" class="corpus-word-frequency-chart-empty">There are no words to display</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
