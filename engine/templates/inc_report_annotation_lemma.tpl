{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-md-4 scrollingWrapper report-annotation-lemma-content-column">
	<div class="panel panel-primary administration-content-panel report-annotation-lemma-panel report-annotation-lemma-content-panel">
		<div class="panel-heading administration-content-heading report-annotation-lemma-heading">
			<span class="administration-content-heading-icon report-annotation-lemma-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div class="panel-body report-annotation-lemma-content-body">
			<div id="content">
				<div id="leftContent" style="width: 100%;" class="annotations scrolling content report-annotation-lemma-document-content">
					<div class="contentBox report-annotation-lemma-content-box">{$content|format_annotations}</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="col-lemmas" class="col-main {if $flags_active && $config_active}col-md-4{elseif $flags_active}col-md-7{elseif $config_active}col-md-5{else}col-md-8{/if} scrollingWrapper report-annotation-lemma-list-column">
	<div class="panel panel-primary administration-content-panel report-annotation-lemma-panel report-annotation-lemma-list-panel">
		<div class="panel-heading administration-content-heading report-annotation-lemma-heading">
			<span class="administration-content-heading-icon report-annotation-lemma-heading-icon"><i class="fa fa-language" aria-hidden="true"></i></span>
			<span>Annotation lemmas</span>
		</div>
		<div id="annotationLemmas" class="panel-body scrolling report-annotation-lemma-list-body">
			<table class="table table-striped annotations report-annotation-lemma-table">
				<thead>
					<tr>
						<th>Phrase</th>
						<th>Lemma</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<div class="panel-footer report-annotation-lemma-actions">
			<button class="btn btn-primary report-annotation-lemma-autofill-button" id="autofill">
				<i class="fa fa-magic" aria-hidden="true"></i>
				<span>Autofill empty lemmas</span>
			</button>
			<button class="btn btn-default report-annotation-lemma-save-button" id="save_all">
				<i class="fa fa-floppy-o" aria-hidden="true"></i>
				<span>Save all</span>
			</button>
		</div>
	</div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper report-annotation-lemma-config-column" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info administration-content-panel report-annotation-lemma-panel report-annotation-lemma-config-panel">
		<div class="panel-heading administration-content-heading report-config-heading report-annotation-lemma-heading">
			<span class="administration-content-heading-icon report-annotation-lemma-heading-icon"><i class="fa fa-cog" aria-hidden="true"></i></span>
			<span>Configuration</span>
		</div>
		<div id="configuration" class="panel-body scrolling report-annotation-lemma-config-body">

			<div class="panel panel-warning report-annotation-lemma-config-section">
				<div class="panel-heading"><i class="fa fa-keyboard-o" aria-hidden="true"></i> Guideline</div>
				<div class="panel-body">
					You can use keys to navigate the list of annotations:
					<ul>
						<li>Use <em>UP</em> and <em>DOWN</em> arrows to move between fields with lemma values.</li>
						<li>Use <em>Ctrl+Space</em> to copy the phrase as a lemma.</li>
						<li>Use <em>Enter</em> to save the lemma for the current annotation.</li>
					</ul>
				</div>
			</div>
			<div class="panel panel-default report-annotation-lemma-config-section">
				<div class="panel-heading"><i class="fa fa-toggle-on" aria-hidden="true"></i> Working mode</div>
				<div class="panel-body">
					<input type="hidden" id="annotation_mode" value="{$annotation_mode}"/>
					<div id="annotation_mode_list">
                        {if "annotate"|has_corpus_role}
							<div class="radio">
								<label><input type="radio" class="radio" name="annotation_mode" value="final" title="Work on final annotations"/> public annotations</label>
							</div>
                        {/if}
                        {if "annotate_agreement"|has_corpus_role}
							<div class="radio">
								<label><input type="radio" class="radio" name="annotation_mode" value="agreement" title="Work on annotations for agreement measurement"/> agreement</label>
							</div>
                        {/if}
					</div>
				</div>
			</div>
			<div class="panel panel-default report-annotation-lemma-config-section">
				<div class="panel-heading"><i class="fa fa-tags" aria-hidden="true"></i> Annotations</div>
				<div class="panel-body">
					{include file="inc_widget_annotation_type_tree.tpl"}
				</div>
			</div>
		</div>
		<div class="panel-footer report-annotation-lemma-config-footer">
			<form method="GET" action="index.php">
				{* The information about selected annotation sets, subsets and types is passed through cookies *}
				{* The information about selected users is paseed through cookies *}
				<input type="hidden" name="page" value="report"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="hidden" name="subpage" value="annotation_lemma"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<button class="btn btn-primary report-annotation-lemma-apply-button" type="submit" id="apply">
					<i class="fa fa-check" aria-hidden="true"></i>
					<span>Apply configuration</span>
				</button>
			</form>
		</div>
	</div>
</div>
