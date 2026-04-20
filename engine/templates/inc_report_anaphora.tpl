{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div class="col-main {if $flags_active}col-md-8{else}col-md-9{/if} scrollingWrapper report-anaphora-view-content-column">
	<div class="panel panel-primary administration-content-panel report-anaphora-view-panel report-anaphora-view-content-panel" id="widget_text">
		<div class="panel-heading administration-content-heading report-anaphora-view-heading">
			<span class="administration-content-heading-icon report-anaphora-view-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div id="content" class="panel-body annotations scrolling report-anaphora-view-document-content">{$content_inline|format_annotations}</div>
	</div>
</div>

<div class="col-md-3 scrollingWrapper report-anaphora-view-relations-column">
	<div class="panel panel-info administration-content-panel report-anaphora-view-panel report-anaphora-view-relations-panel">
		<div class="panel-heading administration-content-heading report-anaphora-view-heading">
			<span class="administration-content-heading-icon report-anaphora-view-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
			<span>Anaphora relations</span>
		</div>
		<div class="panel-body annotations scrolling report-anaphora-view-relations-body">
			<table class="table table-striped tablesorter report-anaphora-view-relations-table" cellspacing="1">
				<thead>
					<tr>
						<th>Source</th>
						<th>Relation</th>
						<th>Target</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$relations item=relation}
					<tr>
						<td><span class="{$relation.ans_type}" title="an#{$relation.source_id}:{$relation.ans_type}">{$relation.ans_text}</span></td>
						<td>{$relation.relation_name}</td>
						<td><span class="{$relation.ant_type}" title="an#{$relation.target_id}:{$relation.ant_type}">{$relation.ant_text}</span></td>
					</tr>
				{foreachelse}
					<tr>
						<td colspan="3" class="report-anaphora-view-empty">No anaphora relations in this document.</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
<input type="hidden" id="report_id" value="{$row.id}"/>
