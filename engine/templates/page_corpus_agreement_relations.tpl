l{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
{include file="inc_header2.tpl"}
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9 col-main scrollingWrapper" style="padding: 0">
            <div class="panel panel-primary" style="margin: 5px;">
                <div class="panel-heading">Comparision</div>
                <div class="panel-body" style="padding: 0">
                    {if $annotator_a_id && $annotator_b_id}
                        <div id="agreement_details" class="scrolling">
                            {assign var=last_report_id value=0}
                            <table id="agreement" class="tablesorter" cellspacing="1">
                                <tr>
                                    <th style="text-align: center; width: 33%" colspan="5">Only&nbsp;A</th>
                                    <th style="text-align: center; width: 34%" colspan="5">A&nbsp;and&nbsp;B</th>
                                    <th style="text-align: center; width: 33%" colspan="5">Only&nbsp;B</th>
                                </tr>

                                {foreach from=$agreement key=report_id item=relations}
                                    <tr>
                                        <th colspan="15" style="text-align: center; background-color: #FFB347">
                                            Report {$report_id}</th>
                                    </tr>
                                    {foreach from=$relations key=span item=relation}
                                        <tr>
                                            <th colspan="15" style="text-align: center; background-color: #D9EDF7">
                                                <div style="display: inline-block;">[{$relation.data.source_bounds}]
                                                </div>
                                                <div style="display: inline-block; color: darkred;">{$relation.data.source_text}</div>
                                                <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                                <div style="display: inline-block; color: #1e9014;">{$relation.data.target_text}</div>
                                                <div style="display: inline-block;">[{$relation.data.target_bounds}]
                                                </div>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="5" class="text-center {if !empty($relation.a)}user_a{/if}"
                                                style="border-right: 1px solid #bce8f1;">
                                                {foreach from=$relation.a key=relation_type_id item=rel}
                                                    {$rel.name}
                                                    <br>
                                                {/foreach}
                                            </td>
                                            <td colspan="5"
                                                class="text-center {if !empty($relation.a_and_b)}a_and_b{/if}"
                                                style="border-right: 1px solid #bce8f1;">
                                                {foreach from=$relation.a_and_b key=relation_type_id item=rel}
                                                    {$rel.name}
                                                    <br>
                                                {/foreach}
                                            </td>
                                            <td colspan="5" class="text-center {if !empty($relation.b)}user_b{/if}">
                                                {foreach from=$relation.b key=relation_type_id item=rel}
                                                    {$rel.name}
                                                    <br>
                                                {/foreach}
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tr>
                                {/foreach}
                            </table>
                        </div>
                    {else}
                        <div class="alert alert-info">
                            <strong>Info!</strong> Set the configuration view.
                        </div>
                    {/if}
                </div>
            </div>
        </div>

        <form action="POST">
            <div class="col-md-3 col-config scrollingWrapper" style="padding: 0">
                <div class="panel panel-info" style="margin: 5px;">
                    <div class="panel-heading">View configuration</div>
                    <div class="panel-body scrolling" style="">
                        <input type="hidden" name="page" value="{$page}"/>
                        <input type="hidden" name="corpus" value="{$corpus.id}"/>

                        <div class="panel panel-default" style="margin: 5px;">
                            <div class="panel-heading">Relation types</div>
                            <div class="panel-body" style="">
                                {include file="inc_widget_relation_type_tree.tpl"}
                            </div>
                        </div>

                        <div class="panel panel-default" style="margin: 5px;">
                            <div class="panel-heading">Annotation types</div>
                            <div class="panel-body" style="">
                                {include file="inc_widget_annotation_type_tree.tpl"}
                            </div>
                        </div>

                        <div class="panel panel-default" style="margin: 5px;">
                            <div class="panel-heading">Documents</div>
                            <div class="panel-body" style="">
                                <h4>By flag</h4>
                                <select name="corpus_flag_id" class="corpus_flag_id" style="font-size: 12px">
                                    <option style="font-style: italic">Select flag</option>
                                    {foreach from=$corpus_flags item=flag}
                                        <option value="{$flag.corpora_flag_id}"
                                                {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if}
                                                title="{$flag.name}"><em>{$flag.short}</em></option>
                                    {/foreach}
                                </select>
                                <select name="flag_id" class="flag_type" style="font-size: 12px">
                                    <option style="font-style: italic">type</option>
                                    {foreach from=$flags item=flag}
                                        <option value="{$flag.flag_id}" style="background-image:url(gfx/flag_{$flag.flag_id}.png); background-repeat: no-repeat; padding-left: 20px;"
                                                {if $flag.flag_id==$flag_id}selected="selected"{/if}>{$flag.name}</option>
                                    {/foreach}
                                </select>

                                <h4>By subcorpus</h4>
                                <div style="vertical-align: middle; line-height: 20px">
                                    {foreach from=$subcorpora item=subcorpus}
                                        <label><input type="checkbox" class="subcorpus_id" name="subcorpus_ids[]"
                                                      value="{$subcorpus.subcorpus_id}"
                                                      {if in_array($subcorpus.subcorpus_id, $subcorpus_ids)}checked="checked"{/if} /> {$subcorpus.name}
                                        </label>
                                    {/foreach}
                                </div>
                            </div>
                        </div>

                        <div class="panel panel-default" style="margin: 5px;">
                            <div class="panel-heading">Users</div>
                            <div class="panel-body" style="">

                                {if $annotators|@count == 0}
                                    {capture assign=message}
                                        <em>There are no users with agreement annotations for the selected
                                            criteria.</em>
                                    {/capture}
                                    {include file="common_message.tpl"}
                                {else}
                                    <table class="tablesorter" cellspacing="1" style="width: 100%; margin-top: 6px;">
                                        <tr>
                                            <th>Annotator name</th>
                                            <th title="Number of relations">Rels*</th>
                                            <th title="Number of documents with user's relations">Docs</th>
                                            <th style="text-align: center">A</th>
                                            <th style="text-align: center">B</th>
                                        </tr>
                                        {foreach from=$annotators item=a}
                                            <tr{if $a.user_id == $annotator_a_id} class="user_a"{elseif $a.user_id == $annotator_b_id} class="user_b"{/if}>
                                                <td style="line-height: 20px">{$a.screename}</td>
                                                <td style="line-height: 20px; text-align: right">{$a.relation_count}</td>
                                                <td style="line-height: 20px; text-align: right">{$a.document_count}</td>
                                                <td style="text-align: center;"><input type="radio"
                                                                                       class="annotator_a_id"
                                                                                       name="annotator_a_id"
                                                                                       value="{$a.user_id}"
                                                                                       {if $a.user_id == $annotator_a_id}checked="checked"{/if}/>
                                                </td>
                                                <td style="text-align: center;"><input type="radio"
                                                                                       class="annotator_b_id"
                                                                                       name="annotator_b_id"
                                                                                       value="{$a.user_id}"
                                                                                       {if $a.user_id == $annotator_b_id}checked="checked"{/if}/>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </table>
                                    <em>*Only <i>agreement</i> relations.</em>
                                {/if}
                            </div>
                        </div>

                    </div>
                    <div class="panel-footer">
                        <input value="Apply configuration" class="btn btn-primary" id="apply"/>
                    </div>
                </div>

                <div class="panel panel-default" style="margin: 5px;">
                    <div class="panel-heading">Agreement</div>
                    <div class="panel-body" style="">
                        <div id="agreement_summary" class="scrolling">
                            <table class="tablesorter" cellspacing="1">
                                <tr>
                                    <th>Relation category</th>
                                    <th>Only A</th>
                                    <th>A and B</th>
                                    <th>Only B</th>
                                    <th>PCS</th>
                                </tr>
                                {foreach from=$pcs key=category item=data}
                                    <tr{if $category=="all"} class="highlight"{/if}>
                                        <td><a href="#" class="filter_by_category_name"
                                               title="Highlight rows containing annotations of given category">{$data.name}</a>
                                        </td>
                                        <td style="text-align: right" class="user_a">{$data.only_a}</td>
                                        <td style="text-align: right">{$data.a_and_b}</td>
                                        <td style="text-align: right" class="user_b">{$data.only_b}</td>
                                        <td style="text-align: right">{$data.pcs|number_format:0}%</td>
                                    </tr>
                                {/foreach}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{include file="inc_footer.tpl"}