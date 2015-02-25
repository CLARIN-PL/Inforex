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

<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top; width: 140px">
			<div class="column" >
				<div class="ui-widget ui-widget-content ui-corner-all">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Słowa:</div>
					<div style="padding: 5px;" class="annotations scrolling">
					<input type="hidden" name="wsd_word" value="{$wsd_word}"/>
					<input type="hidden" name="wsd_edit" value="{$wsd_edit}"/>
					Szybkie przeglądanie wybranych słów:
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
		</td> 
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Treść raportu:</div>
					<div id="content" style="padding: 5px;" class="annotations scrolling">{$content_inline|format_annotations}</div>
				</div>
			</div>
			{if $wsd_word}				
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

			{/if}
		</td>
		<td style="width: 370px; vertical-align: top;" id="cell_annotation_add">
			<div class="column" id="widget_annotation">
				<div class="ui-widget ui-widget-content ui-corner-all">			
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Sensy:</div>
				<div id="wsd_senses" class="scrolling" style="padding: 5px">
					<div style="text-align: center"><i>Zaznacz słowo</i></div>
				</div>
			</div>
		</td>		
	</tr>
</table>
</div>