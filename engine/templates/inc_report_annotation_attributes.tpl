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
						<th>Type</th>
						<th style="min-width: 35%">Phrase/Lemma</th>
						<th style="min-width: 45%">Attributes</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$annotations item=an}
					<tr class="annotation" annotation_id="{$an.id}">
						<td>{$an.type}</td>
						<td class="annotations"><span class="annotation_set_{$an.group_id} {$an.type}">{$an.text}</span> <br/> {$an.lemma} </td>
						<td><table style="width: 100%">
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
		<div class="panel-footer">
			<button class="btn btn-primary" id="autofill">Autofill empty attributes</button>
			<button class="btn btn-default" id="save_all">Save all</button>
		</div>
	</div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info">
		<div class="panel-heading">Configuration</div>
		<div id="configuration" class="panel-body scrolling" style="padding: 2px">
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
				<input type="hidden" name="subpage" value="annotation_attributes"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<input class="btn btn-primary" type="submit" value="Apply configuration" id="apply"/>
			</form>
		</div>
	</div>
</div>