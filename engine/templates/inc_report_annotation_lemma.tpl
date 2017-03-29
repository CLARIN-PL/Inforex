{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-md-4 scrollingWrapper">
	<div class="panel panel-default">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="padding: 0">
			<div id="content">
				<div id="leftContent" style="width: 100%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
					<div style="margin: 5px" class="contentBox">{$content|format_annotations}</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="col-lemmas" class="col-main {if $flags_active && $config_active}col-md-4{elseif $flags_active}col-md-7{elseif $config_active}col-md-5{else}col-md-8{/if} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Annotation lemmas</div>
		<div id="annotationLemmas" class="panel-body scrolling" style="padding: 0">
			<table class="table table-striped">
				<thead>
					<tr>
						<th style="width: 250px">Phrase</th>
						<th>Lemma</th>
						<th style="width: 60px; text-align: center">Actions</th>
						<th style="width: 80px; text-align: center">Status</th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info">
		<div class="panel-heading">Configuration</div>
		<div id="configuration" class="panel-body scrolling" style="padding: 2px">

			<div class="panel panel-warning">
				<div class="panel-heading">Guideline</div>
				<div class="panel-body">
					You can use keys to navigate the list of annotations:
					<ul>
						<li>Use <em>UP</em> and <em>DOWN</em> arrows to move between fields with lemma values.</li>
						<li>Use <em>Ctrl+Space</em> to copy the phrase as a lemma.</li>
						<li>Use <em>Enter</em> to save the lemma for the current annotation.</li>
					</ul>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Working mode</div>
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
			<div class="panel panel-default">
				<div class="panel-heading">Annotations</div>
				<div class="panel-body">
					{include file="inc_widget_annotation_type_tree.tpl"}
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<form method="GET" action="index.php">
				{* The information about selected annotation sets, subsets and types is passed through cookies *}
				{* The information about selected users is paseed through cookies *}
				<input type="hidden" name="page" value="report"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="hidden" name="subpage" value="annotation_lemma"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<input class="btn btn-primary" type="submit" value="Apply configuration" id="apply"/>
			</form>
		</div>
	</div>
</div>