{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables corpus-morphology-agreement-page">
    <div class="row corpus-morphology-agreement-grid">
        <div class="col-md-4 con-info scrollingWrapper corpus-morphology-agreement-list-column">
            <div class="panel administration-content-panel administration-wsd-panel corpus-morphology-agreement-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-files-o" aria-hidden="true"></i></span>
                    <span>Selected subcorpora</span>
                </div>
                <div class="panel-body corpus-morphology-agreement-list-body">
                    <div class="administration-table-wrapper corpus-morphology-agreement-reports-wrapper">
                        <table id="reports_table" class="table table-striped table-hover administration-table dataTable no-footer" cellspacing="0" width="100%">
                            <thead>
                            <tr role="row">
                                <th>ID</th>
                                <th>Title</th>
                                <th>Total tokens</th>
                                <th>Divergent tags</th>
                                <th>PSA</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <ul id="subcorpora-list"></ul>
                </div>
            </div>
        </div>

        <div class="col-md-5 col-main scrollingWrapper corpus-morphology-agreement-main-column">
            <div class="panel administration-content-panel administration-wsd-panel corpus-morphology-agreement-panel">
                <div class="panel-heading administration-content-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-exchange" aria-hidden="true"></i></span>
                    <span>Differing annotations</span>
                </div>
                <div class="panel-body corpus-morphology-agreement-diff-body">
                    <div class="administration-table-wrapper corpus-morphology-agreement-diff-wrapper">
                        <table id="difference_table" class="table table-striped table-hover administration-table corpus-morphology-agreement-diff-table" cellspacing="1">
                            <thead>
                            <tr>
                                <th>Tok range</th>
                                <th>Orth</th>
                                <th>1st user decision</th>
                                <th>2nd user decision</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-config scrollingWrapper corpus-morphology-agreement-sidebar-column">
            <form action="index.php" method="get">
                <input type="hidden" name="page" value="{$page}"/>
                <input type="hidden" name="corpus" value="{$corpus.id}"/>

                <div class="panel-group corpus-morphology-agreement-accordion corpus-agreement-accordion" id="corpusMorphologyAgreementAccordion">
                    <div class="panel administration-content-panel corpus-morphology-agreement-panel corpus-morphology-agreement-config-panel">
                        <div class="panel-heading administration-content-heading corpus-morphology-agreement-accordion-heading corpus-agreement-accordion-heading">
                            <a data-toggle="collapse" data-parent="#corpusMorphologyAgreementAccordion" href="#corpusMorphologyAgreementConfig" class="corpus-morphology-agreement-accordion-toggle corpus-agreement-accordion-toggle">
                                <span class="administration-content-heading-icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
                                <span>View configuration</span>
                                <i class="fa fa-chevron-down corpus-morphology-agreement-accordion-chevron corpus-agreement-accordion-chevron" aria-hidden="true"></i>
                            </a>
                        </div>
                        <div id="corpusMorphologyAgreementConfig" class="panel-collapse collapse in">
                            <div class="panel-body corpus-morphology-agreement-config-body">
                                <div class="corpus-morphology-agreement-config-card">
                                    <div class="corpus-morphology-agreement-config-card-heading">Documents</div>
                                    <div class="corpus-morphology-agreement-config-card-body corpus-morphology-agreement-documents-card-body">
                                        <div class="corpus-morphology-agreement-filter-block">
                                            <div class="corpus-morphology-agreement-filter-label">By flag</div>
                                            <div class="corpus-morphology-agreement-flag-row">
                                                <select name="corpus_flag_id" class="form-control">
                                                    <option value="">Select flag</option>
                                                    {foreach from=$corpus_flags item=flag}
                                                        <option value="{$flag.short}" {if $flag.short==$corpus_flag_id}selected="selected"{/if} title="{$flag.name|escape}">{$flag.short}</option>
                                                    {/foreach}
                                                </select>
                                                <select name="flag_id" class="form-control">
                                                    <option value="">type</option>
                                                    {foreach from=$flags item=flag}
                                                        <option value="{$flag.flag_id}" {if $flag.flag_id==$flag_id}selected="selected"{/if}>{$flag.name}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="corpus-morphology-agreement-filter-block">
                                            <div class="corpus-morphology-agreement-filter-label">By subcorpus</div>
                                            <div class="corpus-morphology-agreement-subcorpus-list">
                                                {foreach from=$subcorpora item=subcorpus}
                                                    <label class="corpus-morphology-agreement-subcorpus-option">
                                                        <input type="checkbox" name="subcorpus_ids[]" value="{$subcorpus.subcorpus_id}" {if in_array($subcorpus.subcorpus_id, $subcorpus_ids)}checked="checked"{/if} />
                                                        <span>{$subcorpus.name}</span>
                                                    </label>
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="corpus-morphology-agreement-config-card">
                                    <div class="corpus-morphology-agreement-config-card-heading">Comparison mode</div>
                                    <div class="corpus-morphology-agreement-config-card-body">
                                        <select name="comparision_mode" class="form-control">
                                            {foreach from=$comparision_modes key=k item=mode}
                                                <option value="{$k}" {if $k==$comparision_mode}selected="selected"{/if}>{$mode}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>

                                <div class="corpus-morphology-agreement-config-card">
                                    <div class="corpus-morphology-agreement-config-card-heading">Users</div>
                                    <div class="corpus-morphology-agreement-config-card-body">
                                        {if $annotators|@count == 0}
                                            <div class="corpus-morphology-agreement-empty corpus-morphology-agreement-sidebar-empty">
                                                <i class="fa fa-user-times" aria-hidden="true"></i>
                                                <div>
                                                    <strong>No agreement annotations</strong>
                                                    <span>There are no users with agreement annotations for the selected criteria.</span>
                                                </div>
                                            </div>
                                        {else}
                                            {if $globalPSC}
                                                <div class="corpus-morphology-agreement-global-psc">
                                                    Users PSC in selected documents:
                                                    <strong>{$globalPSC|string_format:"%.2f"}</strong>
                                                </div>
                                            {/if}
                                            <div class="administration-table-wrapper corpus-morphology-agreement-users-wrapper">
                                                <table class="table table-striped table-hover administration-table corpus-morphology-agreement-users-table" cellspacing="1">
                                                    <thead>
                                                    <tr>
                                                        <th class="td-center corpus-morphology-agreement-users-annotator-column" title="Annotator"><i class="fa fa-user" aria-hidden="true"></i></th>
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
                                                                <span class="corpus-morphology-agreement-user-badge" title="{$a.screename|escape}">
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
                                                        <td class="td-center"><span class="corpus-morphology-agreement-user-badge corpus-morphology-agreement-user-badge-final" title="Final annotations">FA</span></td>
                                                        <td class="td-right"><strong>{$annotation_set_final_count}</strong></td>
                                                        <td class="td-right"><strong>{$annotation_set_final_doc_count}</strong></td>
                                                        <td class="td-center"><input type="radio" name="annotator_a_id" value="final" {if "final" == $annotator_a_id}checked="checked"{/if}/></td>
                                                        <td class="td-center"><input type="radio" name="annotator_b_id" value="final" {if "final" == $annotator_b_id}checked="checked"{/if}/></td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="corpus-morphology-agreement-users-note">*Only <em>agreement</em> annotations different from default tagger decision.</div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                            <div class="panel-footer corpus-morphology-agreement-config-footer">
                                <button type="submit" class="btn btn-primary corpus-morphology-agreement-apply-btn" id="apply">
                                    <i class="fa fa-check" aria-hidden="true"></i>
                                    <span>Apply configuration</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var reports = {$reports|@json_encode};
    var subcorp = {$selectedSubcorp|@json_encode};
    var usersMorphoDisamb = [];
    var annotator_a_id = {$annotator_a_id|@json_encode};
    var annotator_b_id = {$annotator_b_id|@json_encode};
    var annotators = {$annotators|@json_encode};

    var selectedAnnotators = [];
    selectedAnnotators.push(annotators.find(
        function(it){ldelim}
            return it.user_id === annotator_a_id;
        {rdelim}));

    selectedAnnotators.push(annotators.find(
        function(it){ldelim}
            return it.user_id === annotator_b_id;
        {rdelim}));

    var reportsTable = $('#reports_table').DataTable({ldelim}
        scrollX: false,
        paging: true,
        pageLength: 10,
        lengthChange: false,
        searching: false,
        info: true,
        ordering: false,
        autoWidth: false,
        pagingType: "full_numbers",
        dom: 't<"administration-wsd-datatables-footer"ip>',
        createdRow: function(row, data) {ldelim}
            $('td:eq(1)', row).attr('title', data[1]);
        {rdelim}
    {rdelim});

    var diffTable = $('#difference_table').DataTable({ldelim}
        paging: true,
        pageLength: 10,
        lengthChange: false,
        searching: false,
        info: true,
        ordering: false,
        autoWidth: false,
        pagingType: "full_numbers",
        dom: 't<"administration-wsd-datatables-footer"ip>'
    {rdelim});

    var morphoAgreementModule = new MorphoAgreementPreview(
        reportsTable,
        diffTable,
        [annotator_a_id, annotator_b_id],
        reports,
        subcorp,
        usersMorphoDisamb
    );
</script>
{include file="inc_footer.tpl"}
