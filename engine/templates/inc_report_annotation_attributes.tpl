{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-md-4 scrollingWrapper report-annotation-attributes-content-column">
	<div class="panel panel-primary administration-content-panel report-annotation-attributes-panel report-annotation-attributes-content-panel">
		<div class="panel-heading administration-content-heading report-annotation-attributes-heading">
			<span class="administration-content-heading-icon report-annotation-attributes-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div class="panel-body report-annotation-attributes-content-body">
			<div id="content">
				<div id="leftContent" style="width: 100%;" class="annotations scrolling content report-annotation-attributes-document-content">
					<div class="contentBox report-annotation-attributes-content-box">{$content|format_annotations}</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="col-lemmas" class="col-main {if $flags_active && $config_active}col-md-4{elseif $flags_active}col-md-7{elseif $config_active}col-md-5{else}col-md-8{/if} scrollingWrapper report-annotation-attributes-list-column">
	<div class="panel panel-primary administration-content-panel report-annotation-attributes-panel report-annotation-attributes-list-panel">
		<div class="panel-heading administration-content-heading report-annotation-attributes-heading">
			<span class="administration-content-heading-icon report-annotation-attributes-heading-icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
			<span>Annotation attributes</span>
		</div>
		<div id="annotationLemmas" class="panel-body scrolling report-annotation-attributes-list-body">
			<table class="table table-striped report-annotation-attributes-table">
				<thead>
					<tr>
						<th>Type</th>
						<th>Phrase/Lemma</th>
						<th>Attributes</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$annotations item=an}
					<tr class="annotation" annotation_id="{$an.id}">
						<td>{$an.type}</td>
						<td class="annotations"><span class="annotation_set_{$an.group_id} {$an.type}">{$an.text}</span> <br/> {$an.lemma} </td>
						<td><table class="report-annotation-attributes-inner-table">
							{foreach from=$an.attributes item=attr}
								<tr class="attribute" saved_value="{$attr.value}" attribute_id="{$attr.shared_attribute_id}">
									<td class="name"><label>{$attr.name}</label>: </td>
									<td>
										<select type="text" class="shared_attribute form-control" name="an_{$an.id}" annotation_id="{$an.id}" shared_attribute_id="{$attr.shared_attribute_id}" value="{$attr.value}">
											<option value="{$attr.value}">{$attr.value}</option>
										</select>
									</td>
									<td class="status"><span style='color:#999'>no change<span></td>
									<td class="actions"><a href="#" class="save_attribute_value"><i class="fa fa-floppy-o" aria-hidden="true"></i></a></td>
								</tr>
							{/foreach}
							</table>
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
		<div class="panel-footer report-annotation-attributes-actions">
			<button class="btn btn-primary report-annotation-attributes-autofill-button" id="autofill">
				<i class="fa fa-magic" aria-hidden="true"></i>
				<span>Autofill empty attributes</span>
			</button>
			<button class="btn btn-default report-annotation-attributes-save-button" id="save_all">
				<i class="fa fa-floppy-o" aria-hidden="true"></i>
				<span>Save all</span>
			</button>
		</div>
	</div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper report-annotation-attributes-config-column" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info administration-content-panel report-annotation-attributes-panel report-annotation-attributes-config-panel">
		<div class="panel-heading administration-content-heading report-config-heading report-annotation-attributes-heading">
			<span class="administration-content-heading-icon report-annotation-attributes-heading-icon"><i class="fa fa-cog" aria-hidden="true"></i></span>
			<span>Configuration</span>
		</div>
		<div id="configuration" class="panel-body scrolling report-annotation-attributes-config-body">
			<div class="panel panel-default report-annotation-attributes-config-section">
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
			<div class="panel panel-default report-annotation-attributes-config-section">
				<div class="panel-heading"><i class="fa fa-tags" aria-hidden="true"></i> Annotations</div>
				<div class="panel-body">
					{include file="inc_widget_annotation_type_tree.tpl"}
				</div>
			</div>
		</div>
		<div class="panel-footer report-annotation-attributes-config-footer">
			<form method="GET" action="index.php">
				{* The information about selected annotation sets, subsets and types is passed through cookies *}
				{* The information about selected users is paseed through cookies *}
				<input type="hidden" name="page" value="report"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="hidden" name="subpage" value="annotation_attributes"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<button class="btn btn-primary report-annotation-attributes-apply-button" type="submit" id="apply">
					<i class="fa fa-check" aria-hidden="true"></i>
					<span>Apply configuration</span>
				</button>
			</form>
		</div>
	</div>
</div>
