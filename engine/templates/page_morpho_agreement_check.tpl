{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

{include file="inc_header2.tpl"}
	<div id="page_content">
		<div class="container-fluid" id="morpho-agreement-container">
			<div class="row">
				<div class="col-md-4 con-info scrollingWrapper">
					<div class="panel panel-default" style="margin: 5px;">
						<div class="panel-heading">Selected subcorpora</div>
						<div class="panel-body scrolling left-container">
							<table id="reports_table" class="table table-striped dataTable no-footer hover" cellspacing="0" width="100%">
								<thead>
								<tr role="row">
									<th>ID</th>
									<th>Title</th>
									<th>Total tokens</th>
									<th>Divergent tags</th>
									<th>PCS</th>
								</tr>
								</thead>
							</table>
							<ul id="subcorpora-list"></ul>
						</div>
					</div>

				</div>
				<div class="col-md-5 col-main scrollingWrapper" style="padding: 0">
					<div class="panel panel-primary " style="margin: 5px;">
						<div class="panel-heading">Differing annotations</div>
						<div class="panel-body scrolling" style="padding: 0">
							<table id="difference_table" class="table table-striped" cellspacing="1"">
								<thead>
								<tr>
									<th>Tok range</th>
									<th>Orth</th>
									<th>1st user decision</th>
									<th>2nd user decision</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>

				<form action="index.php" method="GET">
				<div class="col-md-3 col-config" style="padding: 0">
					<div class="panel panel-info scrollingWrapper" style="margin: 5px;">
						<div class="panel-heading">View configuration</div>
						<div class="panel-body scrolling" style="">
							<input type="hidden" name="page" value="morpho_agreement_check"/>
							<input type="hidden" name="corpus" value="{$corpus.id}"/>


							<div class="panel panel-default" style="margin: 5px;">
								<div class="panel-heading">Documents</div>
								<div class="panel-body" style="">
									<h4>By flag</h4>
									<select name="corpus_flag_id" style="font-size: 12px">
										<option style="font-style: italic">Select flag</option>
										{foreach from=$corpus_flags item=flag}
										<option value="{$flag.corpora_flag_id}" {if $flag.corpora_flag_id==$corpus_flag_id}selected="selected"{/if} title="{$flag.name}"><em>{$flag.short}</em></option>
										{/foreach}
									</select>
									<select name="flag_id" style="font-size: 12px">
										<option style="font-style: italic">type</option>
										{foreach from=$flags item=flag}
										<option value="{$flag.flag_id}" style="background-image:url(gfx/flag_{$flag.flag_id}.png); background-repeat: no-repeat; padding-left: 20px;" {if $flag.flag_id==$flag_id}selected="selected"{/if}>{$flag.name}</option>
										{/foreach}
									</select>

									<h4>By subcorpus</h4>
									<div style="vertical-align: middle; line-height: 20px">
										{foreach from=$subcorpora item=subcorpus}
										<label><input type="checkbox" name="subcorpus_ids[]" value="{$subcorpus.subcorpus_id}" {if in_array($subcorpus.subcorpus_id, $subcorpus_ids)}checked="checked"{/if} /> {$subcorpus.name}</label>
										{/foreach}
									</div>
								</div>
							</div>

							<div class="panel panel-default" style="margin: 5px;">
								<div class="panel-heading">Comparision mode</div>
								<div class="panel-body" style="">

									<select name="comparision_mode">
                                        {foreach from=$comparision_modes key=k item=mode}
											<option value="{$k}" {if $k==$comparision_mode}selected="selected"{/if}>{$mode}</option>
                                        {/foreach}
									</select>
								</div>
							</div>

							<div class="panel panel-default" style="margin: 5px;">
								<div class="panel-heading">Users</div>
								<div class="panel-body" style="">

									{if $annotators|@count == 0}
										{capture assign=message}
										<em>There are no users with agreement annotations for the selected criteria.</em>
										{/capture}
										{include file="common_message.tpl"}
									{else}
									<table class="tablesorter" cellspacing="1" style="width: 100%; margin-top: 6px;">
										<tr><th>Annotator name</th>
											<th title="Number of annotations">Anns*</th>
											<th title="Number of documents with user's annotations">Docs</th>
											<th style="text-align: center">A</th>
											<th style="text-align: center">B</th>
										</tr>
										{foreach from=$annotators item=a}
										<tr{if $a.user_id == $annotator_a_id} class="user_a"{elseif $a.user_id == $annotator_b_id} class="user_b"{/if}>
											<td style="line-height: 20px">{$a.screename}</td>
											<td style="line-height: 20px; text-align: right">{$a.annotation_count}</td>
											<td style="line-height: 20px; text-align: right">{$a.document_count}</td>
											<td style="text-align: center;"><input type="radio" name="annotator_a_id" value="{$a.user_id}" {if $a.user_id == $annotator_a_id}checked="checked"{/if}/></td>
											<td style="text-align: center;"><input type="radio" name="annotator_b_id" value="{$a.user_id}" {if $a.user_id == $annotator_b_id}checked="checked"{/if}/></td>
										</tr>
										{/foreach}
										<tr{if "final" == $annotator_a_id} class="user_a"{elseif "final" == $annotator_b_id} class="user_b"{/if} style="font-weight: bold">
											<td style="line-height: 20px;">Final annotations</td>
											<td style="line-height: 20px; text-align: right">{$annotation_set_final_count}</td>
											<td style="line-height: 20px; text-align: right">{$annotation_set_final_doc_count}</td>
											<td style="text-align: center;"><input type="radio" name="annotator_a_id" value="final" {if "final" == $annotator_a_id}checked="checked"{/if}/></td>
											<td style="text-align: center;"><input type="radio" name="annotator_b_id" value="final" {if "final" == $annotator_b_id}checked="checked"{/if}/></td>
										</tr>
									</table>
									<em>*Only <i>agreement</i> annotations different from default tagger decision.</em>
									{/if}
								</div>
							</div>

						</div>
						<div class="panel-footer">
							<input type="submit" value="Apply configuration" class="btn btn-primary" id="apply"/>
						</div>
					</div>
				</div>
				</form>

			</div>
		</div>
	</div>
{literal}
<script>
	var reports = {/literal}{$reports|@json_encode};{literal}
	var subcorp = {/literal}{$selectedSubcorp|@json_encode};{literal}
	var usersMorphoDisamb = []; //{/literal}{$usersMorphoDisambSet|@json_encode}; {literal}
	var annotator_a_id = {/literal}{$annotator_a_id|@json_encode}; {literal}
	var annotator_b_id = {/literal}{$annotator_b_id|@json_encode}; {literal}
	var annotators = {/literal}{$annotators|@json_encode}; {literal}

	console.log(annotators);

	var selectedAnnotators = [];
	selectedAnnotators.push(annotators.find(
	    function(it){
	        return it.user_id === annotator_a_id;
        }));

    selectedAnnotators.push(annotators.find(
	    function(it){
	        return it.user_id === annotator_b_id;
        }));

    var reportsTable = $('#reports_table').DataTable( {
        scrollY:        "65vh",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        fixedColumns:   {
            leftColumns: 2
        },
        fixedColumns:   true,
        bInfo:			false
    } );

    var diffTable = $('#difference_table');

	var morphoAgreementModule = new MorphoAgreementPreview(
	    reportsTable,
        diffTable,
		[annotator_a_id, annotator_b_id ],
        reports,
		subcorp,
		usersMorphoDisamb
	);
</script>
{/literal}
{include file="inc_footer.tpl"}