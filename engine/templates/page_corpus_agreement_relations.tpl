{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
{include file="inc_header2.tpl"}
<div class="container-fluid admin_tables corpus-relation-agreement-page">
    <div class="row corpus-relation-agreement-grid">
        <div class="col-md-9 col-main scrollingWrapper corpus-relation-agreement-main-column">
            <div class="panel administration-content-panel corpus-relation-agreement-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-random" aria-hidden="true"></i></span>
                    <span>Comparison</span>
                </div>
                <div class="panel-body corpus-relation-agreement-comparison-body">
                    {if $annotator_a_id && $annotator_b_id}
                        <div id="agreement_details" class="scrolling corpus-relation-agreement-table-scroll">
                            <table id="agreement" class="table table-striped table-hover administration-table corpus-relation-agreement-comparison-table" cellspacing="1">
                                <thead>
                                <tr>
                                    <th class="corpus-relation-agreement-group-heading">A</th>
                                    <th class="corpus-relation-agreement-group-heading">A and B</th>
                                    <th class="corpus-relation-agreement-group-heading">B</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach from=$agreement key=report_id item=relations}
                                    <tr class="corpus-relation-agreement-report-row">
                                        <td colspan="3">Doc. {$report_id}</td>
                                    </tr>
                                    {foreach from=$relations key=span item=relation}
                                        <tr class="corpus-relation-agreement-span-row">
                                            <td colspan="3">
                                                <div class="corpus-relation-agreement-span-content">
                                                    <span class="corpus-relation-agreement-span-bounds">[{$relation.data.source_bounds}]</span>
                                                    <span class="corpus-relation-agreement-span-source">{$relation.data.source_text}</span>
                                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                    <span class="corpus-relation-agreement-span-target">{$relation.data.target_text}</span>
                                                    <span class="corpus-relation-agreement-span-bounds">[{$relation.data.target_bounds}]</span>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="corpus-relation-agreement-group-cell corpus-relation-agreement-group-cell-a {if !empty($relation.a)}user_a{/if}">
                                                {if !empty($relation.a)}
                                                    <div class="corpus-relation-agreement-type-list">
                                                        {foreach from=$relation.a key=relation_type_id item=rel}
                                                            <span class="corpus-relation-agreement-type-badge" title="{$rel.name|escape}">{$rel.name}</span>
                                                        {/foreach}
                                                    </div>
                                                {else}
                                                    <div class="corpus-relation-agreement-empty-group"></div>
                                                {/if}
                                            </td>
                                            <td class="corpus-relation-agreement-group-cell corpus-relation-agreement-group-cell-ab {if !empty($relation.a_and_b)}a_and_b{/if}">
                                                {if !empty($relation.a_and_b)}
                                                    <div class="corpus-relation-agreement-type-list">
                                                        {foreach from=$relation.a_and_b key=relation_type_id item=rel}
                                                            <span class="corpus-relation-agreement-type-badge" title="{$rel.name|escape}">{$rel.name}</span>
                                                        {/foreach}
                                                    </div>
                                                {else}
                                                    <div class="corpus-relation-agreement-empty-group"></div>
                                                {/if}
                                            </td>
                                            <td class="corpus-relation-agreement-group-cell corpus-relation-agreement-group-cell-b {if !empty($relation.b)}user_b{/if}">
                                                {if !empty($relation.b)}
                                                    <div class="corpus-relation-agreement-type-list">
                                                        {foreach from=$relation.b key=relation_type_id item=rel}
                                                            <span class="corpus-relation-agreement-type-badge" title="{$rel.name|escape}">{$rel.name}</span>
                                                        {/foreach}
                                                    </div>
                                                {else}
                                                    <div class="corpus-relation-agreement-empty-group"></div>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    {else}
                        <div class="corpus-relation-agreement-empty">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <div>
                                <strong>Set the configuration first</strong>
                                <span>Select relation and annotation types together with annotators to see the comparison.</span>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        <div class="col-md-3 scrollingWrapper corpus-relation-agreement-sidebar-column">
            <form action="index.php" method="get">
                <input type="hidden" name="page" value="{$page}"/>
                <input type="hidden" name="corpus" value="{$corpus.id}"/>

                <div class="panel-group corpus-relation-agreement-accordion corpus-agreement-accordion" id="corpusRelationAgreementAccordion">
                    <div class="panel administration-content-panel corpus-relation-agreement-panel corpus-relation-agreement-config-panel">
                        <div class="panel-heading administration-content-heading corpus-relation-agreement-accordion-heading corpus-agreement-accordion-heading">
                            <a data-toggle="collapse" data-parent="#corpusRelationAgreementAccordion" href="#corpusRelationAgreementConfig" class="corpus-relation-agreement-accordion-toggle corpus-agreement-accordion-toggle">
                                <span class="administration-content-heading-icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
                                <span>View configuration</span>
                                <i class="fa fa-chevron-down corpus-relation-agreement-accordion-chevron corpus-agreement-accordion-chevron" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div id="corpusRelationAgreementConfig" class="panel-collapse collapse in">
                            <div class="panel-body corpus-relation-agreement-config-body">
                                <div class="corpus-relation-agreement-config-card">
                                    <div class="corpus-relation-agreement-config-card-heading">Relation types</div>
                                    <div class="corpus-relation-agreement-config-card-body corpus-relation-agreement-relation-types-card-body">
                                        {include file="inc_widget_relation_type_tree.tpl"}
                                    </div>
                                </div>

                                <div class="corpus-relation-agreement-config-card">
                                    <div class="corpus-relation-agreement-config-card-heading">Annotation types</div>
                                    <div class="corpus-relation-agreement-config-card-body corpus-relation-agreement-annotation-types-card-body">
                                        {include file="inc_widget_annotation_type_tree.tpl"}
                                    </div>
                                </div>

                                <div class="corpus-relation-agreement-config-card">
                                    <div class="corpus-relation-agreement-config-card-heading">Documents</div>
                                    <div class="corpus-relation-agreement-config-card-body corpus-relation-agreement-documents-card-body">
                                        <div class="corpus-relation-agreement-filter-block">
                                            <div class="corpus-relation-agreement-filter-label">By flag</div>
                                            <div class="corpus-relation-agreement-flag-row">
                                                <select name="corpus_flag_id" class="form-control corpus_flag_id">
                                                    <option value="0">Select flag</option>
                                                    {foreach from=$corpus_flags item=flag}
                                                        <option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if} title="{$flag.name|escape}">{$flag.short}</option>
                                                    {/foreach}
                                                </select>
                                                <select name="flag_id" class="form-control flag_type">
                                                    <option value="0">type</option>
                                                    {foreach from=$flags item=flag}
                                                        <option value="{$flag.flag_id}" {if $flag.flag_id==$flag_id}selected="selected"{/if}>{$flag.name}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="corpus-relation-agreement-filter-block">
                                            <div class="corpus-relation-agreement-filter-label">By subcorpus</div>
                                            <div class="corpus-relation-agreement-subcorpus-list">
                                                {foreach from=$subcorpora item=subcorpus}
                                                    <label class="corpus-relation-agreement-subcorpus-option">
                                                        <input type="checkbox" class="subcorpus_id" name="subcorpus_ids[]" value="{$subcorpus.subcorpus_id}" {if in_array($subcorpus.subcorpus_id, $subcorpus_ids)}checked="checked"{/if}/>
                                                        <span>{$subcorpus.name}</span>
                                                    </label>
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="corpus-relation-agreement-config-card">
                                    <div class="corpus-relation-agreement-config-card-heading">Users</div>
                                    <div class="corpus-relation-agreement-config-card-body">
                                        {if (!is_array($annotators)) || $annotators|@count == 0}
                                            <div class="corpus-relation-agreement-empty corpus-relation-agreement-sidebar-empty">
                                                <i class="fa fa-user-times" aria-hidden="true"></i>
                                                <div>
                                                    <strong>No agreement relations</strong>
                                                    <span>There are no users with agreement relations for the selected criteria.</span>
                                                </div>
                                            </div>
                                        {else}
                                            <div class="administration-table-wrapper corpus-relation-agreement-users-table-wrapper">
                                                <table class="table table-striped table-hover administration-table corpus-relation-agreement-users-table" cellspacing="1">
                                                    <thead>
                                                    <tr>
                                                        <th class="td-center corpus-relation-agreement-users-annotator-column" title="Annotator"><i class="fa fa-user" aria-hidden="true"></i></th>
                                                        <th class="td-right" title="Number of relations">Rels*</th>
                                                        <th class="td-right" title="Number of documents with user's relations">Docs</th>
                                                        <th class="td-center">A</th>
                                                        <th class="td-center">B</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    {foreach from=$annotators item=a}
                                                        <tr{if $a.user_id == $annotator_a_id} class="user_a"{elseif $a.user_id == $annotator_b_id} class="user_b"{/if}>
                                                            <td class="td-center">
                                                                {assign var=annotator_name_normalized value=$a.screename|regex_replace:"/[^[:alnum:]]+/":" "|trim}
                                                                {assign var=annotator_abbr value=$annotator_name_normalized|regex_replace:"/(^|[[:space:]]+)([[:alnum:]])[[:alnum:]]*/":"$2"|regex_replace:"/[^[:alnum:]]+/":""|upper}
                                                                {assign var=annotator_short value=$a.screename|regex_replace:"/[^[:alnum:]]+/":""|truncate:3:""|upper}
                                                                <span class="corpus-relation-agreement-user-badge" title="{$a.screename|escape}">
                                                                    {if $annotator_abbr|strlen > 1}
                                                                        {$annotator_abbr}
                                                                    {else}
                                                                        {$annotator_short}
                                                                    {/if}
                                                                </span>
                                                            </td>
                                                            <td class="td-right">{$a.relation_count}</td>
                                                            <td class="td-right">{$a.document_count}</td>
                                                            <td class="td-center"><input type="radio" class="annotator_a_id" name="annotator_a_id" value="{$a.user_id}" {if $a.user_id == $annotator_a_id}checked="checked"{/if}/></td>
                                                            <td class="td-center"><input type="radio" class="annotator_b_id" name="annotator_b_id" value="{$a.user_id}" {if $a.user_id == $annotator_b_id}checked="checked"{/if}/></td>
                                                        </tr>
                                                    {/foreach}
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="corpus-relation-agreement-users-note">*Only <em>agreement</em> relations.</div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer corpus-relation-agreement-config-footer">
                                <button type="submit" class="btn btn-primary corpus-relation-agreement-apply-btn" id="apply">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    <span>Apply configuration</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="panel administration-content-panel corpus-relation-agreement-panel">
                        <div class="panel-heading administration-content-heading corpus-relation-agreement-accordion-heading corpus-agreement-accordion-heading">
                            <a data-toggle="collapse" data-parent="#corpusRelationAgreementAccordion" href="#corpusRelationAgreementSummary" class="corpus-relation-agreement-accordion-toggle corpus-agreement-accordion-toggle">
                                <span class="administration-content-heading-icon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                                <span>Agreement</span>
                                <i class="fa fa-chevron-down corpus-relation-agreement-accordion-chevron corpus-agreement-accordion-chevron" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div id="corpusRelationAgreementSummary" class="panel-collapse collapse in">
                            <div class="panel-body corpus-relation-agreement-summary-body">
                                <div id="agreement_summary" class="scrolling corpus-relation-agreement-summary-scroll">
                                    <div class="administration-table-wrapper corpus-relation-agreement-summary-table-wrapper">
                                        <table class="table table-striped table-hover administration-table corpus-relation-agreement-summary-table" cellspacing="1">
                                            <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th class="td-right">A</th>
                                                <th class="td-right">A and B</th>
                                                <th class="td-right">B</th>
                                                <th class="td-right">PCS</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            {foreach from=$pcs key=category item=data}
                                                <tr{if $category=="all"} class="highlight"{/if}>
                                                    <td class="corpus-relation-agreement-summary-category-cell">
                                                        <a href="#" class="filter_by_category_name corpus-relation-agreement-summary-category-link" title="{$data.name|escape}">
                                                            {$data.name}
                                                        </a>
                                                    </td>
                                                    <td class="td-right user_a">{$data.only_a}</td>
                                                    <td class="td-right">{$data.a_and_b}</td>
                                                    <td class="td-right user_b">{$data.only_b}</td>
                                                    <td class="td-right">{$data.pcs|number_format:0}%</td>
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
            </form>
        </div>
    </div>
</div>

{include file="inc_footer.tpl"}
