{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="dialog" title="Błąd" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
		<span class="message"></span>
	</p>
	<p><i><a href="">Refresh page.</a></i></p>
</div>

<div id="col-content" class="col-main col-md-{perspectivecolwidth base=3 config=3} scrollingWrapper">
	<div class="panel panel-default">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="padding: 0" style="">
			<div id="content" style="padding: 5px;" class="contentBox annotations scrolling content">{$content|format_annotations}</div>
		</div>
	</div>
</div>

<div id="col-bootstrap" class="col-md-5 scrollingWrapper">
	<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=autoextension&amp;id={$report_id}&amp;annotation_set_id={$annotation_set_id}">
		<div class="panel panel-primary">
			<div class="panel-heading">Annotations to verify</div>
			<div class="panel-body" style="padding: 0">
				<div id="annotationList" class="scrolling">
					{if $annotations|@count > 0 }
						<div class="annotations">
							<table class="table table-striped bootstraped-annotations" cellspacing="1">
								<thead>
								<tr>
									<th>Type</th>
									<th style="width: 200px">Text</th>
									<th>Later</th>
									<th>Accept</th>
									<th>Discard</th>
									<th colspan="2">Change&nbsp;to</th>
								</tr>
								</thead>
								<tbody>
								{foreach from=$annotations item=ann}
									<tr class="annotation" annotation_id="{$ann.id}">
										<td>{$ann.type}</td>
										<td>
											<span class="annotation_set_{$ann.group_id} {$ann.type}" title="an#{$ann.id}:{$ann.type}">{$ann.text}</span>
										</td>
										<td style="text-align: center; background: #ccc">
											<input type="radio" name="annSub[{$ann.id}]" value="later" checked="checked"/>
										</td>
										<td style="text-align: center; background: #A5FF8A">
											<input type="radio" name="annSub[{$ann.id}]" value="accept" />
										</td>
										<td style="text-align: center; background: #FFBBBB">
											<input type="radio" name="annSub[{$ann.id}]" value="discard"/>
										</td>
										<td style="text-align: center; background: lightyellow">
											<input type="radio" name="annSub[{$ann.id}]" value="change" style="display: none"/>
											  <select class="form-control" name="annChange[{$ann.id}]" size="1">
												   <option value="-">-</option>
												   {foreach from=$annotation_types[$ann.group_id] item=type}
													   <option value="{$type.annotation_type_id}">{$type.name}</option>
												   {/foreach}
												</select>
										</td>
									</tr>
								{/foreach}
								</tbody>
							</table>
						</div>
					{else}
						{capture assign=message}
							There are no annotations in this document to verify.
						{/capture}
						{include file="common_message.tpl"}
					{/if}
				</div>
			</div>
			<div class="panel-footer">
				<input type="submit" class="btn btn-default" value="Save" id="buttonSave" style="float: right"/>
				<input type="submit" class="btn btn-primary" value="Auto annotate" id="buttonAutoannotate"/>
				<input type="hidden" name="action" value="report_set_annotations_stage"/>
				<input type="hidden" name="annotation_set_id" value="{$annotation_set_id}"/>
			</div>
			<div class="panel-footer info-refresh" style="display: none">
				<div class="alert alert-info" style="margin: 4px;">
					<strong>Info!</strong> New annotations were recognized.
					<a href="index.php?page=report&corpus={$corpus.id}&subpage=autoextension&id={$report_id}"><span class="glyphicon glyphicon-refresh"></span> Refresh</a> the page.
				</div>
			</div>
			<div class="panel-footer info-notfound" style="display: none">
				<div class="alert alert-warning" style="margin: 4px;">
					<strong>Info!</strong> No new annotations found.
				</div>
			</div>
		</div>
	</form>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info">
		<div class="panel-heading">View configuration</div>
		<div class="panel-body scrolling">

			<div id="cell_annotation_wait" style="display: none;">
				Trwa wczytywanie danych
				<img src="gfx/ajax.gif" />
			</div>

			{if $annotation_sets|@count > 0}
				<div id="annotation_sets">
					<table class="table table-striped" cellspacing="1">
						<tr>
							<th>Annotation set</th>
							<th>New</th>
							<th>Final</th>
							<th>Discarded</th>
							{foreach from=$annotation_sets item=set}
						<tr{if $set.annotation_set_id==$annotation_set_id} class="selected"{/if}>
							<td><a href="?page=report&amp;corpus={$corpus.id}&amp;=autoextension&amp;id={$report.id}&amp;annotation_set_id={$set.annotation_set_id}">{$set.annotation_set_name}</a></td>
							<td style="width: 50px; text-align: right">{$set.count_new}</td>
							<td style="width: 50px; text-align: right">{$set.count_final}</td>
							<td style="width: 50px; text-align: right">{$set.count_discarded}</td>
						</tr>
						{/foreach}
					</table>
				</div>
			{else}
				{capture assign=message}
					There are no annotations in this document.
				{/capture}
				{include file="common_message.tpl"}
			{/if}
		</div>
	</div>
</div>

