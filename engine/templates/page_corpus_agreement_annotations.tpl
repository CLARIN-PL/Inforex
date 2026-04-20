{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}
{include file="inc_header2.tpl"}
<div class="container-fluid admin_tables corpus-agreement-annotations-page">
    <div class="row corpus-agreement-annotations-grid">
        <div class="col-md-9 col-main scrollingWrapper corpus-agreement-annotations-main-column">
            <div class="panel administration-content-panel corpus-agreement-annotations-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-exchange" aria-hidden="true"></i></span>
                    <span>Comparison</span>
                </div>
                <div class="panel-body corpus-agreement-annotations-comparison-body">
                    {if $annotator_a_id && $annotator_b_id}
                        <div id="agreement_details" class="scrolling corpus-agreement-annotations-table-scroll">
                            {assign var=last_report_id value=0}
                            <table id="agreement" class="table table-striped table-hover administration-table corpus-agreement-comparison-table" cellspacing="1">
                                <thead>
                                <tr>
                                    <th class="corpus-agreement-comparison-group-heading">A</th>
                                    <th class="corpus-agreement-comparison-group-heading">A and B</th>
                                    <th class="corpus-agreement-comparison-group-heading">B</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach from=$agreement.annotations key=ank item=an}
                                    {if $last_report_id != $an.report_id}
                                        <tr class="corpus-agreement-report-row">
                                            <td colspan="3">Doc. {$an.report_id}</td>
                                        </tr>
                                        {assign var=last_report_id value=$an.report_id}
                                    {/if}
                                    <tr>
                                        <td class="corpus-agreement-group-cell corpus-agreement-group-cell-a {if array_key_exists($ank, $agreement.only_a)}user_a {$an.annotation_name}{/if}">
                                            {if array_key_exists($ank, $agreement.only_a)}
                                                {if $comparision_mode == "distinct_types"}
                                                    <div class="corpus-agreement-single-entry"><em>{$an.annotation_name}</em> [{$an.type_id}]</div>
                                                {else}
                                                    <div class="corpus-agreement-entry-grid">
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-id" title="ID: {$an.id}">
                                                            <i class="fa fa-hashtag" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-range">[{$an.from},{$an.to}]</div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-text"><em>{$an.text}</em></div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-lemma">{if $an.lemma}{$an.lemma}{else}<i>n/a</i>{/if}</div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-type">[{$an.annotation_name}]</div>
                                                    </div>
                                                {/if}
                                            {else}
                                                <div class="corpus-agreement-empty-group"></div>
                                            {/if}
                                        </td>

                                        <td class="corpus-agreement-group-cell corpus-agreement-group-cell-ab {if array_key_exists($ank, $agreement.a_and_b)}{$agreement.annotations_a[$ank].annotation_name} {$agreement.annotations_b[$ank].annotation_name}{/if}">
                                            {if array_key_exists($ank, $agreement.a_and_b)}
                                                {if $comparision_mode == "distinct_types"}
                                                    <div class="corpus-agreement-single-entry"><em>{$an.annotation_name}</em> [{$an.type_id}]</div>
                                                {else}
                                                    <div class="corpus-agreement-entry-grid corpus-agreement-entry-grid-shared">
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-id" title="A: {$agreement.annotations_a[$ank].id} | B: {$agreement.annotations_b[$ank].id}">
                                                            <i class="fa fa-hashtag" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-range">[{$an.from},{$an.to}]</div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-text"><em>{$an.text}</em></div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-lemma">
                                                            {if $agreement.annotations_a[$ank].lemma != $agreement.annotations_b[$ank].lemma}
                                                                <span class="corpus-agreement-diff">
                                                                    {if $agreement.annotations_a[$ank].lemma}{$agreement.annotations_a[$ank].lemma}{else}<i>n/a</i>{/if}<br/>
                                                                    {if $agreement.annotations_b[$ank].lemma}{$agreement.annotations_b[$ank].lemma}{else}<i>n/a</i>{/if}
                                                                </span>
                                                            {else}
                                                                {if $an.lemma}{$an.lemma}{else}<i>n/a</i>{/if}
                                                            {/if}
                                                        </div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-type">
                                                            {if $agreement.annotations_a[$ank].annotation_name != $agreement.annotations_b[$ank].annotation_name}
                                                                <span class="corpus-agreement-diff">[{$agreement.annotations_a[$ank].annotation_name}]<br/>[{$agreement.annotations_b[$ank].annotation_name}]</span>
                                                            {else}
                                                                [{$an.annotation_name}]
                                                            {/if}
                                                        </div>
                                                    </div>
                                                {/if}
                                            {else}
                                                <div class="corpus-agreement-empty-group"></div>
                                            {/if}
                                        </td>

                                        <td class="corpus-agreement-group-cell corpus-agreement-group-cell-b {if array_key_exists($ank, $agreement.only_b)}user_b {$an.annotation_name}{/if}">
                                            {if array_key_exists($ank, $agreement.only_b)}
                                                {if $comparision_mode == "distinct_types"}
                                                    <div class="corpus-agreement-single-entry"><em>{$an.annotation_name}</em> [{$an.type_id}]</div>
                                                {else}
                                                    <div class="corpus-agreement-entry-grid">
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-id" title="ID: {$an.id}">
                                                            <i class="fa fa-hashtag" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-range">[{$an.from},{$an.to}]</div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-text"><em>{$an.text}</em></div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-lemma">{if $an.lemma}{$an.lemma}{else}<i>n/a</i>{/if}</div>
                                                        <div class="corpus-agreement-entry-cell corpus-agreement-entry-type">[{$an.annotation_name}]</div>
                                                    </div>
                                                {/if}
                                            {else}
                                                <div class="corpus-agreement-empty-group"></div>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    {else}
                        <div class="corpus-agreement-empty">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                            <div>
                                <strong>Set the configuration first</strong>
                                <span>Select annotation set and annotators to see the comparison.</span>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        <div class="col-md-3 scrollingWrapper corpus-agreement-annotations-sidebar-column">
            <form method="get" action="index.php">
                <input type="hidden" name="page" value="{$page}">
                <input type="hidden" name="corpus" value="{$corpus.id}">

                <div class="panel-group corpus-agreement-accordion" id="corpusAgreementAccordion">
                    <div class="panel administration-content-panel corpus-agreement-annotations-panel corpus-agreement-config-panel">
                        <div class="panel-heading administration-content-heading corpus-agreement-accordion-heading">
                            <a data-toggle="collapse" data-parent="#corpusAgreementAccordion" href="#corpusAgreementConfig" class="corpus-agreement-accordion-toggle">
                                <span class="administration-content-heading-icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
                                <span>View configuration</span>
                                <i class="fa fa-chevron-down corpus-agreement-accordion-chevron" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div id="corpusAgreementConfig" class="panel-collapse collapse in">
                            <div class="panel-body corpus-agreement-config-body">
                                <div class="corpus-agreement-config-card">
                                    <div class="corpus-agreement-config-card-heading">Comparison mode</div>
                                    <div class="corpus-agreement-config-card-body">
                                        <select name="comparision_mode" class="form-control">
                                            {foreach from=$comparision_modes key=k item=mode}
                                                <option value="{$k}" {if $k==$comparision_mode}selected="selected"{/if}>{$mode}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="corpus-agreement-config-card">
                                    <div class="corpus-agreement-config-card-heading">Annotation types</div>
                                    <div class="corpus-agreement-config-card-body corpus-agreement-annotation-types-card-body">
                                        {include file="inc_widget_annotation_type_tree.tpl"}
                                    </div>
                                </div>

                                <div class="corpus-agreement-config-card">
                                    <div class="corpus-agreement-config-card-heading">Documents</div>
                                    <div class="corpus-agreement-config-card-body corpus-agreement-documents-card-body">
                                        <div class="corpus-agreement-filter-block">
                                            <div class="corpus-agreement-filter-label">By flag</div>
                                            <div class="corpus-agreement-flag-row">
                                                <select name="corpus_flag_id" class="form-control corpus_flag_id">
                                                    <option value="0">Select flag</option>
                                                    {foreach from=$corpus_flags item=flag}
                                                        <option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if} title="{$flag.name}">{$flag.short}</option>
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

                                        <div class="corpus-agreement-filter-block">
                                            <div class="corpus-agreement-filter-label">By subcorpus</div>
                                            <div class="corpus-agreement-subcorpus-list">
                                                {foreach from=$subcorpora item=subcorpus}
                                                    <label class="corpus-agreement-subcorpus-option">
                                                        <input type="checkbox" class="subcorpus_id" name="subcorpus_ids[]" value="{$subcorpus.subcorpus_id}" {if in_array($subcorpus.subcorpus_id, $subcorpus_ids)}checked="checked"{/if}/>
                                                        <span>{$subcorpus.name}</span>
                                                    </label>
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="corpus-agreement-config-card">
                                    <div class="corpus-agreement-config-card-heading">Users</div>
                                    <div class="corpus-agreement-config-card-body">
                                        {if $annotators|@count == 0}
                                            <div class="corpus-agreement-empty corpus-agreement-sidebar-empty">
                                                <i class="fa fa-user-times" aria-hidden="true"></i>
                                                <div>
                                                    <strong>No agreement annotations</strong>
                                                    <span>There are no users with agreement annotations for the selected criteria.</span>
                                                </div>
                                            </div>
                                        {else}
                                            <div class="administration-table-wrapper corpus-agreement-users-table-wrapper">
                                                <table class="table table-striped table-hover administration-table corpus-agreement-users-table" cellspacing="1">
                                                    <thead>
                                                    <tr>
                                                        <th class="td-center corpus-agreement-users-annotator-column" title="Annotator"><i class="fa fa-user" aria-hidden="true"></i></th>
                                                        <th class="td-right" title="Number of annotations">Anns*</th>
                                                        <th class="td-right" title="Number of documents with user's annotations">Docs</th>
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
                                                                <span class="corpus-agreement-user-badge" title="{$a.screename|escape}">
                                                                    {if $annotator_abbr|strlen > 1}
                                                                        {$annotator_abbr}
                                                                    {else}
                                                                        {$annotator_short}
                                                                    {/if}
                                                                </span>
                                                            </td>
                                                            <td class="td-right">{$a.annotation_count}</td>
                                                            <td class="td-right">{$a.document_count}</td>
                                                            <td class="td-center"><input type="radio" name="annotator_a_id" value="{$a.user_id}" {if $a.user_id == $annotator_a_id}checked="checked"{/if}/></td>
                                                            <td class="td-center"><input type="radio" name="annotator_b_id" value="{$a.user_id}" {if $a.user_id == $annotator_b_id}checked="checked"{/if}/></td>
                                                        </tr>
                                                    {/foreach}
                                                    <tr{if "final" == $annotator_a_id} class="user_a"{elseif "final" == $annotator_b_id} class="user_b"{/if}>
                                                        <td class="td-center"><span class="corpus-agreement-user-badge corpus-agreement-user-badge-final" title="Final annotations">FA</span></td>
                                                        <td class="td-right"><strong>{$annotation_set_final_count}</strong></td>
                                                        <td class="td-right"><strong>{$annotation_set_final_doc_count}</strong></td>
                                                        <td class="td-center"><input type="radio" name="annotator_a_id" value="final" {if "final" == $annotator_a_id}checked="checked"{/if}/></td>
                                                        <td class="td-center"><input type="radio" name="annotator_b_id" value="final" {if "final" == $annotator_b_id}checked="checked"{/if}/></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="corpus-agreement-users-note">*Only <em>agreement</em> annotations.</div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer corpus-agreement-config-footer">
                                <button type="submit" class="btn btn-primary corpus-agreement-apply-btn" id="apply">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    <span>Apply configuration</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="panel administration-content-panel corpus-agreement-annotations-panel">
                        <div class="panel-heading administration-content-heading corpus-agreement-accordion-heading">
                            <a data-toggle="collapse" data-parent="#corpusAgreementAccordion" href="#corpusAgreementSummary" class="corpus-agreement-accordion-toggle">
                                <span class="administration-content-heading-icon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
                                <span>Agreement</span>
                                <i class="fa fa-chevron-down corpus-agreement-accordion-chevron" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div id="corpusAgreementSummary" class="panel-collapse collapse in">
                            <div class="panel-body corpus-agreement-summary-body">
                                <div id="agreement_summary" class="scrolling corpus-agreement-summary-scroll">
                                    <div class="administration-table-wrapper corpus-agreement-summary-table-wrapper">
                                        <table class="table table-striped table-hover administration-table corpus-agreement-summary-table" cellspacing="1">
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
                                                    <td class="corpus-agreement-summary-category-cell">
                                                        <a href="#"
                                                           class="filter_by_category_name corpus-agreement-summary-category-link"
                                                           title="{$category|escape}">
                                                            {$category}
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
