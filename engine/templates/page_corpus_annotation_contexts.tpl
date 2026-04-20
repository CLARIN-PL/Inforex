{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

{if $annotation_stages|@count==0}
    {capture assign=message}
    There are no annotations in this corpora to display.
    {/capture}
    {include file="common_message.tpl"}
{else}

<div class="container-fluid admin_tables annotation-contexts-page">
    <div class="row annotation-contexts-grid">

        <div class="col-md-2 annotation-contexts-column">
            <div class="panel administration-content-panel annotation-contexts-panel annotation-contexts-sidebar-panel">
                <div class="panel-heading administration-content-heading annotation-contexts-heading">
                    <span class="annotation-contexts-heading-copy">
                        <span class="administration-content-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
                        <span>Annotations</span>
                    </span>
                </div>
                <div class="panel-body annotation-contexts-panel-body">
                    <div id="annotation_stages_types" class="annotation-contexts-stack">
                        <div id="annotation_stages" class="annotation-contexts-card">
                            <div class="annotation-contexts-card-heading">Annotation stage</div>
                            <div class="annotation-contexts-card-body">
                                <div class="annotation-contexts-scroll">
                                    <table class="table table-striped table-hover administration-table annotation-contexts-table">
                                        <tbody>
                                        {foreach from=$annotation_stages item=stage}
                                            <tr{if $stage.stage==$annotation_stage} class="selected"{/if}>
                                                <td class="annotation-contexts-count-cell">{$stage.count}</td>
                                                <td><a href="index.php?corpus={$corpus.id}&amp;page=corpus_annotation_contexts&amp;annotation_stage={$stage.stage}&amp;annotation_type_id={$annotation_type_id}">{$stage.stage}</a></td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {if $annotation_stage}
                        <div id="annotation_types" class="annotation-contexts-card">
                            <div class="annotation-contexts-card-heading">Annotation types</div>
                            <div class="annotation-contexts-card-body">
                                <div id="annotation-types" class="annotation-contexts-scroll annotation-contexts-types-scroll">
                                    <table class="table table-striped table-hover administration-table annotation-contexts-table" cellspacing="1">
                                        <tbody>
                                        {assign var="last_set" value=""}
                                        {foreach from=$annotation_types item=type}
                                            {if $last_set != $type.annotation_set_id}
                                            <tr class="annotation_set">
                                                <td colspan="2">{$type.description}</td>
                                                {assign var="last_set" value=$type.annotation_set_id}
                                            </tr>
                                            {/if}
                                            <tr{if $type.annotation_type_id==$annotation_type_id} class="selected"{/if}>
                                                <td class="annotation-contexts-count-cell">{$type.count}</td>
                                                <td><a href="index.php?corpus={$corpus.id}&amp;page=corpus_annotation_contexts&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}">{$type.name}</a></td>
                                            </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-2 annotation-contexts-column">
            {if $annotation_stage && $annotation_type_id}
            <div class="panel administration-content-panel annotation-contexts-panel annotation-contexts-sidebar-panel">
                <div class="panel-heading administration-content-heading annotation-contexts-heading">
                    <span class="annotation-contexts-heading-copy">
                        <span class="administration-content-heading-icon"><i class="fa fa-font" aria-hidden="true"></i></span>
                        <span>Text forms</span>
                    </span>
                </div>
                <div class="panel-body annotation-contexts-panel-body">
                    <div id="annotation_texts" class="annotation-contexts-stack">
                        <div class="annotation-contexts-card">
                            <div class="annotation-contexts-card-heading">Text forms</div>
                            <div class="annotation-contexts-card-body">
                                <div id="annotation_orths" class="annotation-contexts-scroll annotation-contexts-forms-scroll">
                                    <table class="table table-striped table-hover administration-table annotation-contexts-table" cellspacing="1">
                                        <tbody>
                                        {foreach from=$annotation_orths item=type}
                                        <tr{if $type.text==$annotation_orth} class="selected"{/if}>
                                            <td class="annotation-contexts-count-cell">{$type.count}</td>
                                            <td><a href="index.php?corpus={$corpus.id}&amp;page=corpus_annotation_contexts&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}&amp;annotation_orth={$type.text}">{$type.text}</a></td>
                                        </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                    {if $annotation_orths|@count==0}
                                    <div class="annotation-contexts-empty">Choose annotation type.</div>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <div class="annotation-contexts-card">
                            <div class="annotation-contexts-card-heading">Lemmas</div>
                            <div class="annotation-contexts-card-body">
                                <div id="annotation_lemmas" class="annotation-contexts-scroll annotation-contexts-forms-scroll">
                                    <table class="table table-striped table-hover administration-table annotation-contexts-table" cellspacing="1">
                                        <tbody>
                                        {foreach from=$annotation_lemmas item=type}
                                        <tr{if $type.text==$annotation_lemma} class="selected"{/if}>
                                            <td class="annotation-contexts-count-cell">{$type.count}</td>
                                            <td><a href="index.php?corpus={$corpus.id}&amp;page=corpus_annotation_contexts&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}&amp;annotation_lemma={$type.text}">{$type.text}</a></td>
                                        </tr>
                                        {/foreach}
                                        </tbody>
                                    </table>
                                    {if $annotation_lemmas|@count==0 && $annotation_orths|@count>0}
                                    <div class="annotation-contexts-empty">Selected annotations do not have lemmas.</div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
        </div>

        <div class="col-md-8 annotation-contexts-column">
            {if $annotation_stage && $annotation_type_id}
            <div class="panel administration-content-panel annotation-contexts-panel">
                <div class="panel-heading administration-content-heading annotation-contexts-heading">
                    <span class="annotation-contexts-heading-copy">
                        <span class="administration-content-heading-icon"><i class="fa fa-align-left" aria-hidden="true"></i></span>
                        <span>Contexts</span>
                    </span>
                    {if $annotation_type_id}
                    <button type="button" id="export_selected" class="btn btn-primary annotation-contexts-export-button annotation-contexts-heading-export">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        <span>Export CSV</span>
                    </button>
                    {else}
                    <button type="button" class="btn btn-primary annotation-contexts-export-button annotation-contexts-heading-export disabled" disabled="disabled" title="Select annotation type to enable the export">
                        <i class="fa fa-download" aria-hidden="true"></i>
                        <span>Export CSV</span>
                    </button>
                    {/if}
                </div>
                <div class="panel-body annotation-contexts-panel-body annotation-contexts-main-body">
                    <div id="annotation_contexts" class="annotation-contexts-table-shell">
                        <div class="flexigrid">
                            <table id="table-annotations">
                                <tr>
                                    <td class="annotation-contexts-loading-cell">
                                        <div>Loading ... <img style="vertical-align: baseline" title="" src="gfx/flag_4.png"></div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
        </div>
    </div>
</div>

{/if}

{include file="inc_footer.tpl"}
