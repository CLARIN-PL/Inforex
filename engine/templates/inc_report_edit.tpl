{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper">

{if false}
<div style="background: #E03D19; padding: 1px; margin: 10px; ">
    <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;"> <img src="gfx/lock.png" title="No access" style="vertical-align: middle"/>This document has annotations so the edition is temporary disabled.</div>
</div>
{else}

	{if $confirm}
		<div class="panel panel-warning">
			<div class="panel-heading" id="content">List of annotations that will be automatically updated</div>
			<div class="panel-body scrolling">
				<table class="table table-striped" id="table-annotations" style="width: 99%">
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
			<div class="panel-footer">
				<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}" style="display: table-cell">
					<input type="hidden" value="{$confirm_content|escape}" name="content"/>
					<input type="hidden" value="{$confirm_comment}" name="comment"/>
					<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
					<input type="hidden" value="{$row.format_id}" name="format" id="format"/>
					<input type="hidden" value="document_save" name="action"/>
					<input type="hidden" value="1" name="confirm"/>
					<input type="submit" value="confirm" class="btn btn-success"/>
				</form>
				<div style="display: table-cell; padding-left: 10px;"><a class="btn btn-warning" href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">cancel</a></div>
			</div>
		</div>

		<i><a id="toggle-edit-form" href="#">&raquo; re-edit the document &laquo;</a></i>
		<div id="edit-form" style="display: none">
			<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
				<div style="border-top: 1px solid black; border-bottom: 1px solid black; background: white; ">
					<textarea name="content" id="report_content">{$confirm_content|escape}</textarea>
				</div>
				<input type="submit" value="Save" name="formatowanie" id="formating" class="btn btn-success"/>
				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="document_save" name="action"/>
				<input type="hidden" value="2" name="step"/>
			</form>
		</div>

	{else}
	<div class="panel panel-primary">
		<div class="panel-heading">Edit content</div>
		<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
			<div class="panel-body">
				{include file="inc_report_wrong_changes.tpl"}
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
				<div class="panel panel-default">
					<div class="panel-heading">
						<span style="padding-left: 10px; float: right;">
							Edit mode:
						{if $active_edit_type eq 'full'}
							<a class="btn btn-xs btn-primary" disabled="disabled">Full &mdash; content and annotation</a>
							<a href="#" class="btn btn-xs btn-default edit_type" id="no_annotation">Simple &mdash; structure tags only</a>
						{else}
							<a class="btn btn-xs btn-default edit_type" id="full">Full &mdash; content and annotation</a>
							<a href="#" class="btn btn-xs btn-primary" disabled="disabled">Simple &mdash; structure tags only</a>
						{/if}
						</span>
						Document content</div>
					<div class="panel-body" style="padding: 0;">
						<div id="edit_content">
							<textarea name="content" class="scrolling" id="report_content">{if $wrong_changes}{$wrong_document_content|escape}{else}{$content_edit|escape}{/if}</textarea>
						</div>
					</div>
				</div>
				<hr>
				<div class="panel panel-default">
					<div class="panel-heading">Comment</div>
					<div class="panel-body" style="padding: 0; min-height: 50px;">
						<div id="edit_comment">
							<textarea placeholder = "Your comment..." rows = "2" name="comment" style="border:none; width:100%" id="report_comment"></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class = "panel panel-footer clearfix" style = "margin-bottom: 0px;">
				<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
				<input type="hidden" value="2" name="step"/>
				<input type="hidden" value="document_save" name="action"/>
				{if $ex}
					<div style="color: red">The document cannot be modified as an exception raised<br/><b>{$ex->getMessage()}</b>.</div>
				{/if}
				<input type="submit" class="btn btn-primary" style = "float: right;" value="Save" name="formatowanie" id="formating"/>
			</div>
		</form>
	</div>
	{/if}
{/if}

</div>
