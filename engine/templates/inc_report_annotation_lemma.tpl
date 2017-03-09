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
 
<table style="width: 100%; margin-top: 5px;">
	<tr>
		<td style="vertical-align: top"> 
			<div class="column" id="widget_text">
				<div class="ui-widget ui-widget-content ui-corner-all">
					<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Document content</div>
					<div id="content">
						<div id="leftContent" style="width: 100%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
							<div style="margin: 5px" class="contentBox">{$content|format_annotations}</div>
						</div>
					</div>
				</div>
			</div>
		</td>
		<td style="vertical-align: top; width: 400px;">
			<div class="ui-widget ui-widget-content ui-corner-all">
				<div class="ui-widget ui-widget-header ui-helper-clearfix ui-corner-all">Annotations</div>
				<div>
					<table id="annotationLemmas" style="width: 100%">
						<tr>
							<th>Phrase</th>
							<th>Lemma</th>
							<th style="width: 60px">Actions</th>
							<th style="width: 60px">Status</th>
						</tr>
					</table>
				</div>
			</div>
		</td style="vertical-align: top">
		<td style="width: 330px; vertical-align: top; overflow: none; ">
			<div id="rightPanelAccordion" class="ui-accordion ui-widget ui-helper-reset">			
				<h3 id="cell_annotation_layers_header" class="ui-accordion-header ui-helper-reset ui-state-active ui-corner-top" aria-expanded="true" role="tab" tabindex="0">
					<span class="ui-icon ui-icon-triangle-1-s"></span>
					<a tabindex="-1" href="#">View configuration</a>
				</h3>
				<div style="vertical-align: top;padding-top: 12px; padding-bottom: 12px;display:block" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content-active" role="tabpanel">

					<h2>Working mode</h2>
					<input type="hidden" id="annotation_mode" value="{$annotation_mode}"/>
					<ul id="annotation_mode_list">
						{if "annotate"|has_corpus_role}
							<li><input type="radio" class="radio" name="annotation_mode" value="final" title="Work on final annotations"/> public annotations</li>
						{/if}
						{if "annotate_agreement"|has_corpus_role}
							<li><input type="radio" class="radio" name="annotation_mode" value="agreement" title="Work on annotations for agreement measurement"/> agreement</li>
						{/if}
					</ul>

					<h2>Annotation layers</h2>

					{include file="inc_widget_annotation_type_tree.tpl"}

					{*<input class="button" type="button" value="Apply configuration" id="apply"/>*}
					<form method="GET" action="index.php">
						{* The information about selected annotation sets, subsets and types is passed through cookies *}
						{* The information about selected users is paseed through cookies *}
						<input type="hidden" name="page" value="report"/>
						<input type="hidden" name="corpus" value="{$corpus.id}"/>
						<input type="hidden" name="subpage" value="annotation_lemma"/>
						<input type="hidden" name="id" value="{$report.id}"/>
						<input class="button" type="submit" value="Apply configuration" id="apply"/>
					</form>

				</div>		 		
			</div>
		</td>
	</tr>
</table>

