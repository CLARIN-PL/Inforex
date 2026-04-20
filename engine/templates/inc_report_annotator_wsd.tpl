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
	<p><i><a href="">Odśwież stronę.</a></i></p>
</div>
<div class="col-main {if $flags_active}col-md-6{else}col-md-7{/if} scrollingWrapper report-annotator-wsd-content-column">
	<div class="panel panel-primary administration-content-panel report-annotator-wsd-panel report-annotator-wsd-content-panel">
		<div class="panel-heading administration-content-heading report-annotator-wsd-heading">
			<span class="administration-content-heading-icon report-annotator-wsd-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div class="panel-body scrolling annotations report-annotator-wsd-document-content" id="content">
			{$content_inline|format_annotations}
		</div>
	{if $wsd_word}
		<div class="panel-footer report-annotator-wsd-navigation">
			<div class="report-annotator-wsd-navigation-next" id="wsd_navigation">
				{if $next_word_not_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$next_word_not_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$next_word_not_annotation_id}">następne nieoznaczone słowo <b>{$wsd_word}</b> &raquo;</a>
				{else}
					<span>następne nieoznaczone słowo <b>{$wsd_word}</b> &raquo;</span>
				{/if}
				<br/>
				{if $next_word_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$next_word_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$next_word_annotation_id}">następne słowo <b>{$wsd_word}</b> &raquo;</a>
				{else}
					<span>następne słowo <b>{$wsd_word}</b> &raquo;</span>
				{/if}
			</div>

			<div class="report-annotator-wsd-navigation-prev">
				{if $prev_word_not_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$prev_word_not_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$prev_word_not_annotation_id}">&laquo; poprzednie nieoznaczone słowo <b>{$wsd_word}</b></a>
				{else}
				<span>&laquo; poprzednie nieoznaczone słowo <b>{$wsd_word}</b></span>
				{/if}
				<br/>
				{if $prev_word_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$prev_word_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$prev_word_annotation_id}">&laquo; poprzednie słowo <b>{$wsd_word}</b></a>
				{else}
					<span>&laquo; poprzednie słowo <b>{$wsd_word}</b></span>
				{/if}
			</div>
		</div>
	{/if}
	</div>
</div>

<div class="col-md-3 scrollingWrapper report-annotator-wsd-senses-column">
	<div class="panel panel-info administration-content-panel report-annotator-wsd-panel report-annotator-wsd-senses-panel" id="widget_annotation">
		<div class="panel-heading administration-content-heading report-annotator-wsd-heading">
			<span class="administration-content-heading-icon report-annotator-wsd-heading-icon"><i class="fa fa-sitemap" aria-hidden="true"></i></span>
			<span>Word senses</span>
		</div>
		<div class="panel-body annotations scrolling report-annotator-wsd-senses-body" id="wsd_senses">
			<div class="report-annotator-wsd-empty-state"><i class="fa fa-info-circle" aria-hidden="true"></i> Zaznacz słowo</div>
		</div>
	</div>
</div>

<div class="col-md-2 scrollingWrapper report-annotator-wsd-sidebar-column">
	<div class="panel panel-info administration-content-panel report-annotator-wsd-panel report-annotator-wsd-mode-panel">
		<div class="panel-heading administration-content-heading report-annotator-wsd-heading">
			<span class="administration-content-heading-icon report-annotator-wsd-heading-icon"><i class="fa fa-toggle-on" aria-hidden="true"></i></span>
			<span>Working mode</span>
		</div>
		<div class="panel-body report-annotator-wsd-mode-body">
			<input type="hidden" id="annotation_mode" value="{$annotation_mode}"/>
			<div id="annotation_mode_list">
				{if "annotate"|has_corpus_role}
					<div class="radio report-annotator-wsd-radio" title="Work on final annotations and relations">
						<label><input type="radio" class="radio" name="annotation_mode_wsd" value="final"/> final</label>
					</div>
				{else}
					<div class="report-annotator-wsd-role-warning">
						<h6>You are missing "annotate" role to annotate this corpus.</h6>
					</div>
				{/if}

				{if "annotate_agreement"|has_corpus_role}
					<div class="radio report-annotator-wsd-radio" title="Work on annotations and relations for agreement measurement">
						<label><input type="radio" class="radio" name="annotation_mode_wsd" value="agreement"/> agreement</label>
					</div>
				{else}
					<div class="report-annotator-wsd-role-warning">
						<h6>You are missing "annotate_agreement" role to annotate this corpus in agreement mode.</h6>
					</div>
				{/if}
			</div>
		</div>
	</div>
	<div class="panel panel-info administration-content-panel report-annotator-wsd-panel report-annotator-wsd-words-panel">
		<div class="panel-heading administration-content-heading report-annotator-wsd-heading">
			<span class="administration-content-heading-icon report-annotator-wsd-heading-icon"><i class="fa fa-list-ul" aria-hidden="true"></i></span>
			<span>Words</span>
		</div>
		<div class="panel-body annotations scrolling report-annotator-wsd-words-body">
			<label for="annotation_set_select" class="report-annotator-wsd-label">Annotation set</label>
			<select class="form-control" id="annotation_set_select">
				{foreach from = $annotation_sets item = annotation_set}
					<option {if $annotation_set.annotation_set_id == $selected_annotation_set}selected{/if} value = {$annotation_set.annotation_set_id}>{$annotation_set.name}</option>
				{/foreach}
			</select>
			<input type="hidden" name="wsd_word" value="{$wsd_word}"/>
			<input type="hidden" name="wsd_edit" value="{$wsd_edit}"/>

			{*<input type="text" class="form-control" name="wsd_filter_words" placeholder="filter words...">*}
			<p class="report-annotator-wsd-help">Select word to navigate through their occurrences:</p>

			<div class="form-group report-annotator-wsd-options">
				<div class="checkbox report-annotator-wsd-checkbox">
					<label><input type="checkbox" name="ignore_duplicates" value="ignore_duplicates"> Hide words without occurrences</label>
				</div>
			</div>

			<ul id="list_of_words" class="report-annotator-wsd-word-list">
			{foreach from=$words item=w}
				{if !$w.report_id}
					<li class="wsd_word_without_occurrence report-annotator-wsd-word-muted">{$w.word}</li>
				{else}
					<li {if $wsd_word_id == $w.annotation_type_id}class="marked"{/if}>
						<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$w.report_id}&amp;wsd_word={$w.name}&amp;annotation_type_id={$w.annotation_type_id}&amp;aid={$w.annotation_id}">
							{$w.word}
						</a>
					</li>
				{/if}
			{/foreach}
			</ul>
		</div>
	</div>
</div>

	<script>
        // global variable used in different places
		var annotationModeFieldName = 'annotation_mode_wsd';
	</script>
