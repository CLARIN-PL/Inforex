{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper report-edit-content-column">
{if false}
<div style="background: #E03D19; padding: 1px; margin: 10px; ">
    <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;"> <img src="gfx/lock.png" title="No access" style="vertical-align: middle"/>This document has annotations so the edition is temporary disabled.</div>
</div>
{else}

	{if $confirm}
		<div class="panel panel-warning administration-content-panel report-edit-confirm-panel">
			<div class="panel-heading administration-content-heading report-edit-main-heading" id="content">
                <span class="administration-content-heading-icon report-edit-heading-icon"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i></span>
                <span>List of annotations that will be automatically updated</span>
            </div>
			<div class="panel-body scrolling report-edit-confirm-body">
				<table class="table table-striped report-edit-confirm-table" id="table-annotations">
					<tr>
						<th>Action</th>
						<th>Id</th>
						<th>From</th>
						<th>To</th>
						<th>Type</th>
						<th>Text</th>
					</tr>
					{foreach from=$confirm_changed item=c}
						{if $c.action == "removed" }
							<tr>
								<td style="color:red">deleted</td>
								<td>{$c.data1->id}</td>
								<td>{$c.data1->from}</td>
								<td>{$c.data1->to}</td>
								<td>{$c.annotation_type_name}</td>
								<td class="annotations"><span class="{$c.annotation_type_name}">{$c.data1->text}</span></td>
							</tr>
						{else}
							<tr>
								<td style="color:blue;">changed</td>
								<td>{$c.data2->id}</td>
								<td>{$c.data2->from}
									{if $c.data1->from != $c.data2->from} (<span style='text-decoration: line-through; color: #777'>{$c.data1->from}</span>){/if}
									</td>
								<td>{$c.data2->to}
									{if $c.data1->to != $c.data2->to} (<span style='text-decoration: line-through; color: #777'>{$c.data1->to}</span>){/if}
									</td>
								<td>{$c.annotation_type_name}
									{if $c.data1->type_id != $c.data2->type_id} (<span style='text-decoration: line-through; color: #777'> {$c.annotation_type_name} </span>){/if}
									</td>
								<td><span class="{$c.data2->type}">{$c.data2->text} </span>
									{if $c.data1->text != $c.data2->text} (<span style='text-decoration: line-through; color: #777'>{$c.data1->text}</span>){/if}
									{if $c.action == "remove_whitespaces"}(<span style='color: #777'>remove begin/end whitespaces</span>){/if}
									</td>
							</tr>
						{/if}
					{/foreach}
				</table>
			</div>
			<div class="panel-footer report-edit-footer">
				<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
					<input type="hidden" value="{$confirm_content|escape}" name="content"/>
					<input type="hidden" value="{$confirm_comment}" name="comment"/>
					<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
					<input type="hidden" value="{$row.format_id}" name="format" id="format"/>
					<input type="hidden" value="document_save" name="action"/>
					<input type="hidden" value="1" name="confirm"/>
					<button type="submit" class="btn btn-success report-edit-confirm-button"><i class="fa fa-check" aria-hidden="true"></i> Confirm</button>
				</form>
				<a class="btn btn-warning report-edit-cancel-button" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
			</div>
		</div>

		<a id="toggle-edit-form" class="report-edit-reopen-link" href="#"><i class="fa fa-pencil" aria-hidden="true"></i> Re-edit the document</a>
		<div id="edit-form" class="report-edit-hidden-form" style="display: none">
			<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
				<div class="report-edit-editor-frame">
					<textarea name="content" id="report_content">{$confirm_content|escape}</textarea>
				</div>
				<button type="submit" name="formatowanie" id="formating" class="btn btn-success report-edit-save-button"><i class="fa fa-check" aria-hidden="true"></i> Save</button>
				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="document_save" name="action"/>
				<input type="hidden" value="2" name="step"/>
			</form>
		</div>

	{else}
	<div class="panel panel-primary administration-content-panel report-edit-panel">
		<div class="panel-heading administration-content-heading report-edit-main-heading">
            <span class="administration-content-heading-icon report-edit-heading-icon"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
            <span>Edit content</span>
        </div>
		<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
			<div class="panel-body report-edit-body">
				{include file="inc_report_wrong_changes.tpl"}
                {if $full_edit_disabled_reason}
                    <div class="alert alert-warning report-edit-alert">
                        {$full_edit_disabled_reason}
                        Content length: {$content_edit_length}. Annotations: {$annotations_count}.
                    </div>
                {/if}
                {if $disable_codemirror}
                    <div class="alert alert-info report-edit-alert">
                        Code editor is disabled by default in this perspective to keep the page responsive. Plain textarea mode is active.
                    </div>
                {/if}
				{* ToDo: Probably it will be removed. This metadata can be changed in another perspective.
				<div class = "row">
					<div class = "col-lg-6">
						<label for = "status">Status:</label>
						<select class = "form-control" name = "status" id = "status">
							{foreach from = $select_status item = status}
								<option {if $status.id == $selected_status} selected {/if} value = "{$status.id}">{$status.status}</option>
							{/foreach}
						</select>
					</div>
					<div class = "col-lg-6">
						<label for = "format">Format:</label>
						<select class = "form-control" name = "format" id = "format">
							{foreach from = $select_format key = name item = id}
								<option {if $id == $selected_format} selected {/if} value = "{$id}">{$name}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<hr>
				*}
				<div class="panel panel-default report-edit-card">
					<div class="panel-heading report-edit-card-heading">
                        <span class="report-edit-card-title"><i class="fa fa-file-code-o" aria-hidden="true"></i> Document content</span>
						<span class="report-edit-mode-actions">
							Edit mode:
						{if $active_edit_type eq 'full'}
							<a class="btn btn-xs btn-primary report-edit-mode-button active" disabled="disabled">Full - content and annotation</a>
							<a href="#" class="btn btn-xs btn-default edit_type report-edit-mode-button" id="no_annotation">Simple - structure tags only</a>
						{else}
                            {if $full_edit_disabled}
							<a class="btn btn-xs btn-default report-edit-mode-button" disabled="disabled" title="Disabled for large documents">Full - content and annotation</a>
                            {else}
							<a href="#" class="btn btn-xs btn-default edit_type report-edit-mode-button" id="full">Full - content and annotation</a>
                            {/if}
							<a href="#" class="btn btn-xs btn-primary report-edit-mode-button active" disabled="disabled">Simple - structure tags only</a>
						{/if}
						</span>
                    </div>
					<div class="panel-body report-edit-card-body" id="edit_content_panel">
                        <div class="report-edit-toolbar">
                            {if $disable_codemirror}
                                <a href="#" class="btn btn-xs btn-default report-edit-tool-button" id="enable_codemirror"><i class="fa fa-code" aria-hidden="true"></i> Enable code editor</a>
                            {else}
                                <a href="#" class="btn btn-xs btn-default report-edit-tool-button" id="disable_codemirror_button"><i class="fa fa-align-left" aria-hidden="true"></i> Use plain textarea</a>
                            {/if}
                        </div>
						<div id="edit_content">
							<textarea name="content" class="scrolling report-edit-textarea" id="report_content">{if $wrong_changes}{$wrong_document_content|escape}{else}{$content_edit|escape}{/if}</textarea>
						</div>
					</div>
				</div>
				<div class="panel panel-default report-edit-card report-edit-comment-card">
					<div class="panel-heading report-edit-card-heading">
                        <span class="report-edit-card-title"><i class="fa fa-comment-o" aria-hidden="true"></i> Comment</span>
                    </div>
					<div class="panel-body report-edit-comment-body">
						<div id="edit_comment">
							<textarea placeholder = "Your comment..." rows = "2" name="comment" id="report_comment" class="report-edit-comment"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class = "panel panel-footer clearfix report-edit-footer">
				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="2" name="step"/>
				<input type="hidden" value="document_save" name="action"/>
                <input type="hidden" value="{if $disable_codemirror}1{else}0{/if}" id="disable_codemirror"/>
                <input type="hidden" value="{$use_codemirror|default:0}" id="use_codemirror"/>
				{if $ex}
					<div class="report-edit-error">The document cannot be modified as an exception raised<br/><b>{$ex->getMessage()}</b>.</div>
				{/if}
				<button type="submit" class="btn btn-primary report-edit-save-button" name="formatowanie" id="formating"><i class="fa fa-check" aria-hidden="true"></i> Save</button>
			</div>
		</form>
	</div>
	{/if}
{/if}

</div>
