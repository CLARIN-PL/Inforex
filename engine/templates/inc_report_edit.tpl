{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{if false}
<div style="background: #E03D19; padding: 1px; margin: 10px; ">
    <div style="background: #FFF194; padding: 5px; color: #733B0E; font-size: 16px; font-weight: bold;"> <img src="gfx/lock.png" title="No access" style="vertical-align: middle"/>This document has annotations so the edition is temporary disabled.</div>
</div>
{else}

	{if $confirm}
	<div style="background: lightyellow; border: 1px solid #D9BB73; padding: 5px; margin: 5px;" id="content">
		<h1>Confirm the changes</h1>
		<h2>List of annotations that will be automatically updated</h2>
			<table cellspacing="1" class="table tablesorter" id="table-annotations" style="text-align: center; width: 99%">
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
							<td>{$c.data1->type}</td>
							<td class="annotations"><span class="{$c.data1->type}">{$c.data1->text}</span></td>
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
							<td>{$c.data2->type} 
								{if $c.data1->type != $c.data2->type} (<span style='text-decoration: line-through; color: #777'>{$c.data1->type}</span>){/if}
								</td>
							<td><span class="{$c.data2->type}">{$c.data2->text}</span>
							 	{if $c.data1->text != $c.data2->text} (<span style='text-decoration: line-through; color: #777'>{$c.data1->text}</span>){/if}
							 	{if $c.action == "remove_whitespaces"}(<span style='color: #777'>remove begin/end whitespaces</span>){/if}
							 	</td>
						</tr>			
					{/if}
				{/foreach}
			</table>
            <form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}" style="display: table-cell">
                <input type="hidden" value="{$confirm_content|escape}" name="content"/>
                <input type="hidden" value="{$confirm_comment}" name="comment"/>
                <input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
                <input type="hidden" value="{$row.format_id}" name="format" id="format"/>
                <input type="hidden" value="document_save" name="action"/>
                <input type="hidden" value="1" name="confirm"/>
                <input type="submit" value="confirm"/>
            </form>         
            <div style="display: table-cell"><a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">cancel</a></div>
	
	</div>
	
	<i><a id="toggle-edit-form" href="#">&raquo; re-edit the document &laquo;</a></i>
	<div id="edit-form" style="display: none">
		<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
			<div style="border-top: 1px solid black; border-bottom: 1px solid black; background: white; ">
				<textarea name="content" id="report_content">{$confirm_content|escape}</textarea>
			</div>
			<input type="submit" value="Save" name="formatowanie" id="formating"/>
			<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
			<input type="hidden" value="document_save" name="action"/>
			<input type="hidden" value="2" name="step"/>
		</form>
	</div>
	
	{else}
		{include file="inc_report_wrong_changes.tpl"}
	<form method="post" action="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$row.id}">
	
	    <h2>Meta</h2>
	    <span style="padding-left: 10px">Status:</span> {$select_status} 
	    <span style="padding-left: 10px">Format:</span> {$select_format}
	    		
        <h2>Content</h2>
        <span style="padding-left: 10px">
        {if $active_edit_type eq 'full'}
        	Current edit mode: <b>full &mdash; content and annotation</b> (switch to <a href="#" class="edit_type" id="no_annotation">simple &mdash; structure tags only</a>)
        {else}
			Current edit mode: <b>simple &mdash; structure tags only</b> (switch to <a href="#" class="edit_type" id="full">full &mdash; content and annotation</a>)
        {/if}
        </span>
		<div style="border-top: 1px solid black; border-bottom: 1px solid black; background: white;" id="edit_content">
			<textarea name="content" id="report_content">{if $wrong_changes}{$wrong_document_content|escape}{else}{$content_edit|escape}{/if}</textarea>
		</div>
		<h2>Comment</h2>
		<div style="border-top: 1px solid black; border-bottom: 1px solid black;background: white;" id="edit_comment">
			<textarea name="comment" style="border:none; width:100%" id="report_comment"></textarea>
		</div>		
		
		{if $ex}
		  <div style="color: red">The document cannot be modified as an exception raised<br/><b>{$ex->getMessage()}</b>.</div>
		{else}		
		  <input type="submit" class="button" value="Save" name="formatowanie" id="formating"/>
		{/if}
		<input type="hidden" value="{$row.id}" name="report_id" id="report_id"/>
		<input type="hidden" value="document_save" name="action"/>
		<input type="hidden" value="2" name="step"/>
	</form>
	
	{/if}
{/if}