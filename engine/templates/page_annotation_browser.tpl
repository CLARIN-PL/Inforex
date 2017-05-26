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

<div class="panel panel-primary">
    <div class="panel-heading">Annotation browser</div>
    <div class="panel-body" style="padding: 5px">
        <div class="container-fluid">
            <div class="row">

                <div class="col-md-2">
                    <div id="annotation_stages_types" class="scrollingWrapper">
                        <div id="annotation_stages">
                            <div class="panel panel-default">
                                <div class="panel-heading">Annotation stage</div>
                                <div class="panel-body" style="padding: 0">
                                    <table class="table table-striped">
                                    {assign var="last_set" value=""}
                                    {foreach from=$annotation_stages item=stage}
                                    <tr{if $stage.stage==$annotation_stage} class="selected"{/if}>
                                        <td style="text-align: right; width: 50px">{$stage.count}</td>
                                        <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$stage.stage}&amp;annotation_type_id={$annotation_type_id}">{$stage.stage}</a></td>
                                    </tr>
                                    {/foreach}
                                    </table>
                                </div>
                            </div>
                        </div>
                        {if $annotation_stage}
                        <div id="annotation_types">
                            <div class="panel panel-default">
                                <div class="panel-heading">Annotation types</div>
                                <div class="panel-body" style="padding: 0">
                                    <div id="annotation-types" class="scrolling" style="overflow: auto;height: 500px; ">
                                        <table class="table table-striped" cellspacing="1">
                                        {assign var="last_set" value=""}
                                        {foreach from=$annotation_types item=type}
                                        {if $last_set != $type.annotation_set_id}
                                        <tr class="annotation_set">
                                            <td colspan="2">{$type.description}</td>
                                            {assign var="last_set" value=$type.annotation_set_id}
                                        </tr>
                                        {/if}
                                        <tr{if $type.annotation_type_id==$annotation_type_id} class="selected"{/if}>
                                            <td style="text-align: right; width: 50px">{$type.count}</td>
                                            <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}">{$type.name}</a></td>
                                        </tr>
                                        {/foreach}
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>

                <div class="col-md-2">
                {if $annotation_stage && $annotation_type_id}
                    <div id="annotation_texts" class="scrollingWrapper">
                        <div class="panel panel-default">
                            <div class="panel-heading">Text forms</div>
                            <div class="panel-body" style="padding: 0">
                                <div id="annotation_orths" class="scrolling" style="overflow: auto; height: 100px; ">
                                    <table class="table table-striped" cellspacing="1">
                                    {foreach from=$annotation_orths item=type}
                                    <tr{if $type.text==$annotation_orth} class="selected"{/if}>
                                        <td style="text-align: right">{$type.count}</td>
                                        <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}&amp;annotation_orth={$type.text}">{$type.text}</a></td>
                                    </tr>
                                    {/foreach}
                                    </table>
                                    {if $annotation_orths|@count==0}
                                    <i>Choose annotation type.</i>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Lemmas</div>
                            <div class="panel-body" style="padding: 0">
                                <div id="annotation_lemmas" class="scrolling">
                                    <table class="table table-striped" cellspacing="1">
                                    {foreach from=$annotation_lemmas item=type}
                                    <tr{if $type.text==$annotation_lemma} class="selected"{/if}>
                                        <td style="text-align: right">{$type.count}</td>
                                        <td><a href="index.php?corpus={$corpus.id}&amp;page=annotation_browser&amp;annotation_stage={$annotation_stage}&amp;annotation_type_id={$type.annotation_type_id}&amp;annotation_lemma={$type.text}">{$type.text}</a></td>
                                    </tr>
                                    {/foreach}
                                    </table>
                                    {if $annotation_lemmas|@count==0 && $annotation_orths|@count>0}
                                    <i>Selected annotations do not have lemmas.</i>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
                </div>

                <div class="col-md-8">
                {if $annotation_stage && $annotation_type_id }
                    <div id="annotation_contexts" class="scrollingWrapper">
                        <div class="panel panel-default">
                            <div class="panel-heading">Contexts</div>
                            <div class="panel-body" style="padding: 0">
                                <div class="flexigrid">
                                    <table id="table-annotations">
                                      <tr>
                                          <td style="vertical-align: middle"><div>Loading ... <img style="vertical-align: baseline" title="" src="gfx/flag_4.png"></div></td>
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
    </div>

    <div id="export" class="panel-footer" style="clear: both;">
        {*<input type="button" id="export_all" value="Export all annotations to CSV" class="button"/>*}
        {if $annotation_type_id}
        <input type="button" id="export_selected" value="Export selected annotations to CSV" class="btn btn-primary"/>
        {else}
        <input type="button" value="Export selected annotations to CSV" class="btn btn-primary disabled" disabled="disabled" title="Select annotation type to enable the export"/>
        {/if}
    </div>
</div>

{/if}

{include file="inc_footer.tpl"}