{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

<div id="col-content" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if}">
    {assign var="action" value="metadata_save"}
    {assign var="button_text" value="Save"}
    {assign var="header" value="Edit metadata"}
    {*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

        <div class="panel panel-primary">
            <div class="panel-heading">{$header}</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6 scrollingWrapper">
                        <div class="panel panel-default">
                            <div class="panel-heading">Common metadata</div>
                            <div class="panel-body scrolling">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input disabled class="form-control" type="text" name="title" value="{$row.title}">
                                </div>
                                <div class="form-group">
                                    <label for="author">Author</label>
                                    <input disabled class="form-control" type="text" name="author" value="{$row.author}" tabindex="0">
                                </div>
                                <div class="form-group">
                                    <label for="source">Source</label>
                                    <input disabled class="form-control" type="text" name="source" value="{$row.source}">
                                </div>
                                <div class="form-group">
                                    <label for="filename">Filename</label>
                                    <input disabled class="form-control" type="text" name="filename" value="{$row.filename}">
                                </div>
                                <div class="form-group">
                                    <label for="subcorpus_id">Subcorpus</label>
                                    <select disabled class="form-control" name="subcorpus_id">
                                        <option value="" {if $row.subcorpus_id==""}selected="selected"{/if}>[unassigned]
                                        </option>
                                        {foreach from=$subcorpora item=sub}
                                            <option value="{$sub.subcorpus_id}"
                                                    {if $sub.subcorpus_id==$row.subcorpus_id}selected="selected"{/if}>{$sub.name}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select disabled class="form-control" name="status">
                                        {foreach from=$statuses item=status}
                                            <option value="{$status.id}"
                                                    {if $status.id==$row.status}selected="selected"{/if}>{$status.status}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input disabled class="form-control" type="text" name="date" value="{$row.date}"/>
                                    <span style="color: green">released, published or created</span>
                                </div>
                                <div class="form-group">
                                    <label for="format">Format</label>
                                    <select disabled class="form-control" name="format">
                                        {foreach from=$formats item=format}
                                            <option value="{$format.id}"
                                                    {if $format.id==$row.format_id}selected="selected"{/if}>{$format.format}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="form-group parent_select">
                                    <label for="parent_report_id">Parent report ID</label>
                                    <input disabled class = "form-control" type = "text" name = "parent_report_id" value = "{$parent_report.title}">
                                    {if $row.parent_report_id != null}
                                        <a href="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=preview&amp;id={$row.parent_report_id}">
                                            <p style = "margin-top: 5px;">{$parent_report.title}</p>
                                        </a>
                                    {/if}
                                </div>
                                <div class="form-group">
                                    <label for="lang">Language</label>
                                    <input disabled class="form-control" type="text" name="lang" value="{$report_language}">
                                </div>
                            </div>
                        </div>
                        <div class="panel panel-default">
                            <div class="panel-heading">Custom metadata</div>
                            <div class="panel-body scrolling">
                                {if $features|@count==0}
                                    {capture assign=message}
                                        <em>No custom metadata were defined for this corpus.</em>
                                    {/capture}
                                    {include file="common_message.tpl"}
                                {/if}
                                {foreach from=$features item=f}
                                    {if $f.value}
                                        {assign var="value" value=$f.value}
                                    {else}
                                        {assign var="value" value=$metadata_values[$f.field]}
                                    {/if}
                                    <div class="form-group">
                                        <label for="ext_{$f.field}">
                                            {if $f.field_name != ""}
                                                {$f.field_name}
                                            {else}
                                                {$f.field}
                                            {/if}
                                        </label>
                                        {if $f.type == "enum"}
                                            <select disabled class="form-control" name="ext_{$f.field}">
                                                {if $f.default == "empty"}
                                                    <option value = "null" {if $f.value == null}selected{/if}>Empty (NOT DEFINED)</option>
                                                {/if}
                                                {foreach from=$f.field_values item=v}
                                                    {if $value != null}
                                                        <option value="{$v}" {if $v==$value}selected="selected"{/if}>{$v}</option>
                                                    {else}
                                                        <option value="{$v}" {if $v == $f.default}selected="selected"{/if}>{$v}</option>
                                                    {/if}
                                                {/foreach}
                                            </select>
                                        {else}
                                            {if $value != null}
                                                <input disabled class = "form-control" type="text" name="ext_{$f.field}" value="{$value}"/>
                                            {elseif $f.default == "empty"}
                                                <input disabled class = "form-control" type="text" name="ext_{$f.field}" value="{$value}"/>
                                            {else}
                                                <input disabled class = "form-control" type="text" name="ext_{$f.field}" value="{$f.default}"/>
                                            {/if}
                                        {/if}
                                        {if $f.comment}
                                            <span style="color: green">{$f.comment}</span>
                                        {/if}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 scrollingWrapper">
                        <input id="report_id" type="hidden" name="report_id" value="{$row.id}">
                        <input type="hidden" name="action" value="{$action}"/>
                        {if $add_content}
                            <div id="add_content_box" class="panel panel-default">
                                <div class="panel-heading">Content</div>
                                <div class="panel-body scrolling">
                                    <div style="border: 1px solid #cdcdcd; background: #fefefe;" id="add_content">
                                <textarea name="content"
                                          id="{$add_content}">{if $row.content==""} {else}{$row.content}{/if}</textarea>
                                    </div>
                                </div>
                            </div>
                        {else}
                            <div id="col-config">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Document content</div>
                                    <div class="panel-body" style="padding: 5px">
                                        <div class="{$report.format} scrolling">{$content}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading clearfix">
                                    Translations
                                    <select style = "float: right; width: 150px;" class = "form-control" id = "translation_language">
                                        {if empty($translation_languages)}
                                            <option>-</option>
                                        {/if}
                                        {foreach from = $translation_languages item = language}
                                            <option {if $language.code == $selected_language}selected{/if} value = "{$language.code}">
                                                {if $language.language == null}
                                                    Unspecified
                                                {else}
                                                    {$language.language}
                                                {/if}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="panel-body" style="padding: 5px">
                                    <div id = "current_translation" class="{$report.format} scrolling">
                                        {if empty($translation_languages)}
                                            <div class = "row text-center">
                                                <h3> There are no translations. </h3>
                                            </div>
                                        {else}
                                            {$selected_translation.content}
                                        {/if}
                                    </div>
                                </div>
                                <div class = "panel-footer clearfix">
                                    <div class = "col-sm-12 text-center">
                                        <button disabled id = "prev" class = "btn btn-default previous_translation">
                                            <span style = "font-size: 20px;" class="glyphicon glyphicon-circle-arrow-left" aria-hidden="true"></span>
                                        </button>
                                        <button disabled id = "next" class = "btn btn-default next_translation">
                                            <span style = "font-size: 20px;" class="glyphicon glyphicon-circle-arrow-right" aria-hidden="true"></span>
                                        </button>
                                        <h6 id = "translation_number" style = "float: right;"></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">Images</div>
                                <div class="panel-body" style="padding: 5px">
                                    <div class="{$report.format} scrolling">
                                        {if empty($images)}
                                            <div class = "row text-center">
                                                <h3> This document has no images. </h3>
                                            </div>

                                        {/if}

                                        {foreach from = $images item = image_row}
                                            <div class = "row">
                                                {foreach from = $image_row item = image}
                                                    <div class = "col-sm-4">
                                                        <div class = "thumbnail">
                                                            <img src="image.php?id={$image.id}_{$image.name}" class="img-rounded" alt="Report image">
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="modal fade" id="image_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Image</h4>
            </div>
            <div class="modal-body text-center">
                <div class = "thumbnail">
                    <img id = "image_preview" src="" class="img-rounded" alt="Report image">
                </div>
            </div>
        </div>
    </div>
</div>

