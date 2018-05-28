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
	<p><i><a href="">Odświerz stronę.</a></i></p>
</div>

<div class="col-md-2 scrollingWrapper">
	<div class="panel panel-info">
		<div class="panel-heading">Words</div>
		<div class="panel-body annotations scrolling">
			Annotation set
			<select class = "form-control" id = "annotation_set_select">
				{foreach from = $annotation_sets item = annotation_set}
					<option {if $annotation_set.annotation_set_id == $selected_annotation_set}selected{/if} value = {$annotation_set.annotation_set_id}>{$annotation_set.name}</option>
				{/foreach}
			</select>
			<hr>
			<input type="hidden" name="wsd_word" value="{$wsd_word}"/>
			<input type="hidden" name="wsd_edit" value="{$wsd_edit}"/>
			Select word to navigate through their occurrences:
			<ul id="list_of_words">
			{foreach from=$words item=w}
				{if !$w.report_id}
					<li style="color: #888">{$w.word}</li>
				{else}
					<li {if $wsd_word == $w.name}class="marked"{/if}>
						<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$w.report_id}&amp;wsd_word={$w.name}&amp;aid={$w.annotation_id}">
							{$w.word}
						</a>
					</li>
				{/if}
			{/foreach}
			</ul>
		</div>
	</div>
</div>

<div class="col-main {if $flags_active}col-md-6{else}col-md-7{/if} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div class="panel-body scrolling annotations" id="content">
			{$content_inline|format_annotations}
		</div>
	{if $wsd_word}
		<div class="panel-footer">
			<div style="float: right; text-align: right" id="wsd_navigation">
				{if $next_word_not_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$next_word_not_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$next_word_not_annotation_id}">następne nieoznaczone słowo <b>{$wsd_word}</b> &raquo;</a>
				{else}
					<span style="color: #888">następne nieoznaczone słowo <b>{$wsd_word}</b> &raquo;</span>
				{/if}
				<br/>
				{if $next_word_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$next_word_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$next_word_annotation_id}">następne słowo <b>{$wsd_word}</b> &raquo;</a>
				{else}
					<span style="color: #888">następne słowo <b>{$wsd_word}</b> &raquo;</span>
				{/if}
			</div>

			<div>
				{if $prev_word_not_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$prev_word_not_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$prev_word_not_annotation_id}">&laquo; poprzednie nieoznaczone słowo <b>{$wsd_word}</b></a>
				{else}
				<span style="color: #888;">&laquo; poprzednie nieoznaczone słowo <b>{$wsd_word}</b></span>
				{/if}
				<br/>
				{if $prev_word_report_id}
					<a href="index.php?page=report&amp;corpus={$corpus.id}&amp;id={$prev_word_report_id}&amp;wsd_word={$wsd_word}&amp;aid={$prev_word_annotation_id}">&laquo; poprzednie słowo <b>{$wsd_word}</b></a>
				{else}
					<span style="color: #888">&laquo; poprzednie słowo <b>{$wsd_word}</b></span>
				{/if}
			</div>
		</div>
	{/if}
	</div>
</div>

<div class="col-md-3 scrollingWrapper">
	<div class="panel panel-info" id="widget_annotation">
		<div class="panel-heading">Words' senses</div>
		<div class="panel-body annotations scrolling" id="wsd_senses">
			<div style="text-align: center"><i>Zaznacz słowo</i></div>
		</div>
	</div>
</div>