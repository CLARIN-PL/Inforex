{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-primary">
	<div class="panel-heading">{$button_text}</div>
	<form method="POST">
	<div class="panel-body scrolling">
			<input id="report_id" type="hidden" name="report_id" value="{$row.id}">
			<input type="hidden" name="action" value="{$action}"/>

			{if $add_content}
				<div id="add_content_box">
					<div class="panel panel-default">
						<div class="panel-heading">Content</div>
						<div class="panel-body">

							<div style="border: 1px solid #cdcdcd; background: #fefefe;" id="add_content">
								<textarea name="content" id="{$add_content}">{if $row.content==""} {else}{$row.content}{/if}</textarea>
							</div>
						</div>
					</div>
					<input type="submit" value="{$button_text}" class="btn btn-primary"/>
				</div>
			{/if}

			<div class="panel panel-default">
				<div class="panel-heading">Common metadata</div>
				<div class = "panel-body">
					<div class = "form-group">
						<label for = "title">Title</label>
						<input class = "form-control" type="text" name="title" value="{$row.title}">
					</div>
					<div class = "form-group">
						<label for = "author">Author</label>
						<input class = "form-control" type="text" name="author" value="{$row.author}" tabindex="0">
					</div>
					<div class = "form-group">
						<label for = "source">Source</label>
						<input class = "form-control" type="text" name="source" value="{$row.source}">
					</div>
					<div class = "form-group">
						<label for = "subcorpus_id">Subcorpus</label>
						<select class = "form-control" name="subcorpus_id">
							<option value="" {if $row.subcorpus_id==""}selected="selected"{/if}>[unassigned]</option>
							{foreach from=$subcorpora item=sub}
								<option value="{$sub.subcorpus_id}" {if $sub.subcorpus_id==$row.subcorpus_id}selected="selected"{/if}>{$sub.name}</option>
							{/foreach}
						</select>
					</div>
					<div class = "form-group">
						<label for = "status">Status</label>
						<select class = "form-control"  name="status">
							{foreach from=$statuses item=status}
								<option value="{$status.id}" {if $status.id==$row.status}selected="selected"{/if}>{$status.status}</option>
							{/foreach}
						</select>
					</div>
					<div class = "form-group">
						<label for = "date">Date</label>
						<input class = "form-control" type="text" name="date" value="{$row.date}"/>
						<span style="color: green">released, published or created</span>
					</div>
					<div class = "form-group">
						<label for = "format">Format</label>
						<select class = "form-control" name="format">
							{foreach from=$formats item=format}
								<option value="{$format.id}" {if $format.id==$row.format_id}selected="selected"{/if}>{$format.format}</option>
							{/foreach}
						</select>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Custom metadata</div>
				<div class = "panel-body">
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
						<div class = "form-group">
							<label for = "ext_{$f.field}">
								{if $f.field_name != ""}
                                    {$f.field_name}
								{else}
									{$f.field}
								{/if}
							</label>
							{if $f.type == "enum"}
								<select class = "form-control" name="ext_{$f.field}">
									{foreach from=$f.field_values item=v}
										<option value="{$v}" {if $v==$value}selected="selected"{/if}>{$v}</option>
									{/foreach}
									{if $f.null == "Yes"}
										<option value="null">NULL (not defined)</option>
									{/if}
								</select>
							{else}
								<input class = "form-control" type="text" name="ext_{$f.field}" value="{$value}"/>
							{/if}
							{if $f.comment}
								<span style="color: green">{$f.comment}</span>
							{/if}
						</div>
					{/foreach}
				</div>
			</div>
	</div>
	{if !$add_content}
		<div class = "panel-footer clearfix">
			<input type="submit" value="{$button_text}" style = "float: right;" class="btn btn-primary"/>
		</div>
	{/if}
	</form>
</div>