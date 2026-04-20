{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl" content_class="no_padding"}

<div class="container-fluid admin_tables annotation-distribution-page">
    <div class="panel administration-content-panel annotation-distribution-shell">
        <div class="panel-heading administration-content-heading annotation-distribution-heading annotation-distribution-main-heading">
            <div class="annotation-distribution-main-heading-copy">
                <span class="administration-content-heading-icon"><i class="fa fa-bar-chart" aria-hidden="true"></i></span>
                <span>Annotation frequency</span>
            </div>
        </div>

        <div class="panel-body annotation-distribution-body">
            <div class="annotation-distribution-toolbar">
                <form method="GET" action="index.php" class="annotation-distribution-form">
                    <input type="hidden" name="page" value="{$page}"/>
                    <input type="hidden" name="corpus" value="{$corpus.id}"/>

                    <span class="annotation-distribution-form-title">
                        <i class="fa fa-filter" aria-hidden="true"></i>
                        <span>Filters</span>
                    </span>

                    <label class="annotation-distribution-field">
                        <span>Stage</span>
                        <select name="annotation_stage">
                            <option value="">All</option>
                            {foreach from=$annotation_stages item=at}
                                <option value="{$at.stage}" {if $at.stage==$annotation_stage}selected="selected"{/if}>{$at.stage} ({$at.c})</option>
                            {/foreach}
                        </select>
                    </label>

                    <label class="annotation-distribution-field">
                        <span>Annotation set</span>
                        <select name="annotation_set_id">
                            <option value="">All</option>
                            {foreach from=$annotation_sets item=as}
                                <option value="{$as.annotation_set_id}" {if $as.annotation_set_id==$annotation_set_id}selected="selected"{/if}>{$as.name} ({$as.c})</option>
                            {/foreach}
                        </select>
                    </label>

                    <label class="annotation-distribution-field">
                        <span>Annotation type</span>
                        <select name="annotation_type_id">
                            <option value="">All</option>
                            {foreach from=$annotation_types item=at}
                                <option value="{$at.annotation_type_id}" {if $at.annotation_type_id==$annotation_type_id}selected="selected"{/if}>{$at.name} ({$at.c})</option>
                            {/foreach}
                        </select>
                    </label>

                    <label class="annotation-distribution-field">
                        <span>Subcorpus</span>
                        <select name="subcorpus_id">
                            <option value="">All</option>
                            {foreach from=$subcorpora item=s}
                                <option value="{$s.subcorpus_id}" {if $s.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$s.name}</option>
                            {/foreach}
                        </select>
                    </label>

                    <label class="annotation-distribution-field annotation-distribution-search">
                        <span>Phrase</span>
                        <input type="text" name="phrase" value="{$phrase}"/>
                    </label>

                    <div class="annotation-distribution-actions">
                        <button type="submit" class="btn btn-primary annotation-distribution-apply">
                            <i class="fa fa-check" aria-hidden="true"></i>
                            <span>Apply</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="row annotation-distribution-grid">
                <div class="col-md-4 annotation-distribution-column">
                    <div id="annotation_frequency" class="annotation-distribution-content-panel">
                        <div class="annotation-distribution-subheading">
                            <div class="annotation-distribution-heading-copy">
                                <span class="administration-content-heading-icon"><i class="fa fa-list-ol" aria-hidden="true"></i></span>
                                <span>Annotation frequency</span>
                            </div>
                        </div>
                        <div class="annotation-distribution-subbody">
                            <div class="flexigrid annotation-distribution-flexigrid">
                                <table id="annotation_frequency_table">
                                    <tr>
                                        <td class="annotation-distribution-loading-cell"><div>Loading ...</div></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8 annotation-distribution-column">
                    <div id="annotation_distribution" class="annotation-distribution-content-panel annotation-distribution-chart-panel">
                        <div class="annotation-distribution-subheading annotation-distribution-chart-heading">
                            <div class="annotation-distribution-heading-copy">
                                <span class="administration-content-heading-icon"><i class="fa fa-area-chart" aria-hidden="true"></i></span>
                                <span>Annotation distribution across subcorpora</span>
                            </div>
                            <span id="countby" class="annotation-distribution-countby">
                                <span class="annotation-distribution-countby-label">Count:</span>
                                <a href="#" class="active words" type="words">Words</a>
                                <span>/</span>
                                <a href="#" class="documents" type="documents">Documents</a>
                            </span>
                        </div>
                        <div class="annotation-distribution-subbody annotation-distribution-chart-body">
                            <div id="annotation_distribution_loading" class="administration-wsd-loading annotation-distribution-loading annotation-distribution-chart-loading">
                                <img src="gfx/ajax.gif" alt="Loading"/>
                                <span>Loading annotation distribution...</span>
                            </div>
                            <a id="chart_link" class="annotation-distribution-chart-link" href="#" download="annotation-distribution.png" title="Download chart as PNG" style="display: none;">
                                <i class="fa fa-download" aria-hidden="true"></i>
                                <span>PNG</span>
                            </a>
                            <div id="annotations_per_subcorpus" class="annotation-distribution-chart">There are no annotations to display</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
