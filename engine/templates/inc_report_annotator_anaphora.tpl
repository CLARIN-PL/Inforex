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
 
<div class="col-main {if $flags_active}col-md-8{else}col-md-9{/if} scrollingWrapper report-anaphora-content-column">
	<div class="panel panel-primary administration-content-panel report-anaphora-panel report-anaphora-content-panel" id="widget_text">
		<div class="panel-heading administration-content-heading report-anaphora-heading">
			<span class="administration-content-heading-icon report-anaphora-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div class="panel-body report-anaphora-content-body" id="content">
			<div id="leftContent" style="float:left; width:49%;" class="annotations scrolling content report-anaphora-document-column report-anaphora-source-content">{$content_inline|format_annotations}</div>
			<div id="rightContent" style="width:49%;" class="annotations scrolling content report-anaphora-document-column report-anaphora-target-content">{$content_inline2|format_annotations}</div>
			<div style="clear:both"></div>
		</div>
	</div>
</div>

<div class="col-md-3 scrollingWrapper report-anaphora-tools-column">
	<div class="panel panel-info administration-content-panel report-anaphora-panel report-anaphora-create-panel">
		<div class="panel-heading administration-content-heading report-anaphora-heading">
			<span class="administration-content-heading-icon report-anaphora-heading-icon"><i class="fa fa-link" aria-hidden="true"></i></span>
			<span>Create relation</span>
		</div>
		<div id="relationTypesList" class="panel-body annotations report-anaphora-create-body">
			<table class="table table-striped annotations report-anaphora-selection-table" cellspacing="1">
				<thead>
					<tr>
						<th>Source</th>
						<th></th>
						<th>Target</th>
					</tr>
				 </thead>
				 <tbody>
					<tr>
						<td id="anaphoraSource"></td>
						<td class="report-anaphora-arrow">↦</td>
						<td id="anaphoraTarget"></td>
					</tr>
				 </tbody>
			</table>
			<ul class="report-anaphora-relation-types">
				{foreach from=$availableRelations item=relation}
					<li>
						{* <input type="radio" relation_id="{$relation.id}" name="quickAdd"/> *}
						<span title="{$relation.description}" class="addRelation token hiddenAnnotation report-anaphora-relation-type" relation_id="{$relation.id}">{$relation.name}</span>
						<small>{$relation.description}</small>
					</li>
				{/foreach}
				{* <li><input type="radio" relation_id="0" name="quickAdd" value="0" checked="checked"/>&nbsp;<i>disable quick mode</i></li> *}
			 </ul>
		</div>
	</div>
	<div id="cell_annotation_wait" class="report-anaphora-loading" style="display: none;">
		<span>Trwa wczytywanie danych</span>
		<img src="gfx/ajax.gif" />
		<input type="hidden" id="report_id" value="{$row.id}"/>
	</div>
	<div class="panel panel-info administration-content-panel report-anaphora-panel report-anaphora-relations-panel">
		<div class="panel-heading administration-content-heading report-anaphora-heading">
			<span class="administration-content-heading-icon report-anaphora-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
			<span>Anaphora relations</span>
		</div>
		<div id="relationList" class="panel-body annotations relationsContainer scrolling report-anaphora-relations-body">
			<table class="table table-striped tablesorter report-anaphora-relations-table" cellspacing="1">
				<thead>
					<tr>
						<th>Source</th>
						<th>Relation</th>
						<th>Target</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="relationListContainer">
				{foreach from=$allrelations item=relation}
					<tr>
						<td><span class="{$relation.source_type}" title="an#{$relation.source_id}:{$relation.source_type}">{$relation.source_text}</span></td>
						<td>{$relation.name}</td>
						<td><span class="{$relation.target_type}" title="an#{$relation.target_id}:{$relation.target_type}">{$relation.target_text}</span></td>
						<td class="relationDelete" source_id="{$relation.source_id}" target_id="{$relation.target_id}" relation_id="{$relation.id}" type_id="{$relation.relation_type_id}" title="Delete relation"><i class="fa fa-trash" aria-hidden="true"></i></td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>
