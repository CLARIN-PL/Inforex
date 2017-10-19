{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div class="panel panel-info col-config">
	<div class="panel-heading" role="tab" id="headingConfiguration">
		<h4 class="panel-title">
			<a data-toggle="collapse" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseConfiguration" aria-expanded="false" aria-controls="collapseConfiguration">
				View configuration</a>
		</h4>
	</div>
	<div id="collapseConfiguration" class="panel-collapse collapse {if $active_accordion=="collapseConfiguration"}in{/if}" style="padding: 2px;">
		<div class="scrollingAccordion">
		<div id="annotation_layers" class="scrolling">
		   {* Poniższe opcje dostępne wyłącznie w widoku do edycji anotacji *}
		   {if $subpage=="annotator"}
			<div class="panel panel-default">
				<div class="panel-heading">Working mode</div>
				<div class="panel-body">
				   <input type="hidden" id="annotation_mode" value="{$annotation_mode}"/>
				   <div id="annotation_mode_list">
					   {if "annotate"|has_corpus_role}
						   <div class="radio" title="Work on final annotations and relations">
							   <label><input type="radio" class="radio" name="annotation_mode" value="final"/> final</label>
						   </div>
					   {/if}
					   {if "annotate_agreement"|has_corpus_role}
						   <div class="radio" title="Work on annotations and relations for agreement measurement">
							   <label><input type="radio" class="radio" name="annotation_mode" value="agreement"/> agreement</label>
						   </div>
					   {/if}
                       {if "annotate_agreement"|has_corpus_role}
                           <div class="radio" title="Work on annotations for agreement measurement. Unable to edit annotations.">
                               <label><input type="radio" class="radio" name="annotation_mode" value="relation_agreement"/> relation agreement</label>
                           </div>
                       {/if}
				   </div>
				</div>
			</div>
		   {/if}

			<div class="panel panel-default">
				<div class="panel-heading">Annotations</div>
				<div class="panel-body">
				    {if $subpage=="preview"}
						<div class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-sm-2" for="email">Stage:</label>
								<div class="col-sm-10">
									<select name="stage_annotations" class="form-control" id="sel1">
                                        {foreach from=$stages_annotations item=s}
											<option value="{$s}" {if $s==$stage_annotations}selected="selected"{/if}>{$s}</option>
                                        {/foreach}
									</select>
								</div>
							</div>
						</div>
				    {/if}
					{include file="inc_widget_annotation_type_tree.tpl"}
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">Relations</div>
				<div class="panel-body">
                    {if $subpage=="preview"}
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="sel2">Stage:</label>
                                <div class="col-sm-10">
                                    <select name="stage_relations" class="form-control" id="sel2">
                                        {foreach from=$stages_relations item=s}
                                            <option value="{$s}" {if $s==$stage_relations}selected="selected"{/if}>{$s}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    {/if}
					{include file="inc_widget_relation_sets.tpl"}
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">Other</div>
				<div class="panel-body">
					<table class="table table-striped">
						<tr>
							<td>Display every sentence separately</td>
							<td style="text-align: center; width: 100px"><input id="splitSentences" type="checkbox" {if $smarty.cookies.splitSentences=="true"}checked="checked"{/if} style="vertical-align: middle"/></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<div class="panel-footer scrollingFix">
			<button id="applyLayer" class="btn btn-primary">Apply configuration</button>
		</div>
	</div>
	</div>
</div>
