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

<div id="col-document" class="col-md-4 scrollingWrapper report-autoextension-document-column">
	<div class="panel panel-primary administration-content-panel report-autoextension-panel report-autoextension-document-panel">
		<div class="panel-heading administration-content-heading report-autoextension-heading">
			<span class="administration-content-heading-icon report-autoextension-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div class="panel-body report-autoextension-document-body">
			<div id="content" class="contentBox annotations scrolling content report-autoextension-document-content">{$content|format_annotations}</div>
		</div>
	</div>
</div>

<div id="col-content" class="col-main col-md-{perspectivecolwidth base=4 config=3} scrollingWrapper report-autoextension-verification-column">
	<form method="POST" action="index.php?page=report&amp;corpus={$corpus.id}&amp;subpage=autoextension&amp;id={$report_id}&amp;annotation_set_id={$annotation_set_id}">
		<div class="panel panel-primary administration-content-panel report-autoextension-panel report-autoextension-verification-panel">
			<div class="panel-heading administration-content-heading report-autoextension-heading">
				<span class="administration-content-heading-icon report-autoextension-heading-icon"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
				<span>Annotations to verify</span>
			</div>
			<div class="panel-body report-autoextension-verification-body">
				<div id="annotationList" class="scrolling report-autoextension-annotation-list">
					{if $annotations|@count > 0 }
						<div class="annotations">
							<table class="table table-striped bootstraped-annotations report-autoextension-table" cellspacing="1">
								<thead>
								<tr>
									<th>Type</th>
									<th style="width: 200px">Text</th>
									<th class="decision">Later</th>
									<th class="decision">Accept</th>
									<th class="decision">Discard</th>
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
										<td class="decision decision-later">
											<input type="radio" name="annSub[{$ann.id}]" value="later" checked="checked"/>
										</td>
										<td class="decision decision-accept">
											<input type="radio" name="annSub[{$ann.id}]" value="accept" />
										</td>
										<td class="decision decision-discard">
											<input type="radio" name="annSub[{$ann.id}]" value="discard"/>
										</td>
										<td class="decision-change">
											<input type="radio" name="annSub[{$ann.id}]" value="change" style="display: none"/>
											  <select class="form-control input-sm" name="annChange[{$ann.id}]" size="1">
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
						<div class="report-autoextension-empty-state">
							{include file="common_message.tpl"}
						</div>
					{/if}
				</div>
			</div>
			<div class="panel-footer report-autoextension-actions">
				<button type="submit" class="btn btn-primary report-autoextension-auto-button" id="buttonAutoannotate">
					<i class="fa fa-magic" aria-hidden="true"></i>
					<span>Auto annotate</span>
				</button>
				<button type="submit" class="btn btn-default report-autoextension-save-button" id="buttonSave" {if $annotations|@count==0}disabled="disabled"{/if}>
					<i class="fa fa-floppy-o" aria-hidden="true"></i>
					<span>Save all</span>
				</button>
				<input type="hidden" name="action" value="report_set_annotations_stage"/>
				<input type="hidden" name="annotation_set_id" value="{$annotation_set_id}"/>
			</div>
			<div class="panel-footer info-refresh report-autoextension-message-footer" style="display: none">
				<div class="alert alert-info report-autoextension-alert">
					<strong>Info!</strong> New annotations were recognized.
					<a href="index.php?page=report&corpus={$corpus.id}&subpage=autoextension&id={$report_id}"><span class="glyphicon glyphicon-refresh"></span> Refresh</a> the page.
				</div>
			</div>
			<div class="panel-footer info-notfound report-autoextension-message-footer" style="display: none">
				<div class="alert alert-warning report-autoextension-alert">
					<strong>Info!</strong> No new annotations found.
				</div>
			</div>
		</div>
	</form>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper report-autoextension-config-column" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info administration-content-panel report-autoextension-panel report-autoextension-config-panel">
		<div class="panel-heading administration-content-heading report-config-heading report-autoextension-heading">
			<span class="administration-content-heading-icon report-autoextension-heading-icon"><i class="fa fa-cog" aria-hidden="true"></i></span>
			<span>View configuration</span>
		</div>
		<div class="panel-body scrolling report-autoextension-config-body">

			<div id="cell_annotation_wait" class="report-autoextension-loading" style="display: none;">
				<span>Trwa wczytywanie danych</span>
				<img src="gfx/ajax.gif" />
			</div>

			{if $annotation_sets|@count > 0}
				<div id="annotation_sets">
					<table class="table table-striped report-autoextension-sets-table" cellspacing="1">
						<tr>
							<th>Annotation set</th>
							<th>New</th>
							<th>Final</th>
							<th>Discarded</th>
						</tr>
							{foreach from=$annotation_sets item=set}
						<tr{if $set.annotation_set_id==$annotation_set_id} class="selected"{/if}>
							<td><a href="?page=report&amp;corpus={$corpus.id}&amp;subpage=autoextension&amp;id={$report.id}&amp;annotation_set_id={$set.annotation_set_id}">{$set.annotation_set_name}</a></td>
							<td class="report-autoextension-count">{$set.count_new}</td>
							<td class="report-autoextension-count">{$set.count_final}</td>
							<td class="report-autoextension-count">{$set.count_discarded}</td>
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
