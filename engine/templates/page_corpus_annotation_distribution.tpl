{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl" content_class="no_padding"}
<div class="panel panel-primary">
	<div class="panel-heading">Annotation frequency</div>
	<div class="panel-body" style="padding: 0">

		<div style="background: #eee; border-bottom: 1px solid #aaa; padding: 5px;">
			{*<input type="button" id="export_by_subcorpora" style="float: right" value="Export frequency distribution to a CSV file" class="button"/>*}
			{*<input type="button" id="export_selected" style="float: right" value="Export current frequency list to a CSV file" class="button"/>*}
			<form method="GET" action="index.php">
				<input type="hidden" name="page" value="{$page}"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<div class="filter" style="margin-top: 3px;">
					<b>Filters:</b>
				</div>

				<div class="filter" style="margin-top: 3px;">
					<span>Annotation stage:</span>
					<select name="annotation_stage" style="vertical-align: middle;">
					<option value="">all</a>
					{foreach from=$annotation_stages item=at}
						<option value="{$at.stage}" {if $at.stage==$annotation_stage}selected="selected"{/if}>{$at.stage} ({$at.c})</a>
					{/foreach}
				</select>
				</div>

				<div class="filter" style="margin-top: 3px;">
				<span>Annotation set:</span>
				<select name="annotation_set_id" style="vertical-align: middle;">
					<option value="">all</a>
					{foreach from=$annotation_sets item=as}
						<option value="{$as.annotation_set_id}" {if $as.annotation_set_id==$annotation_set_id}selected="selected"{/if}>{$as.name} ({$as.c})</a>
					{/foreach}
				</select>
				</div>

				<div class="filter" style="margin-top: 3px;">
				<span>Annotation type:</span>
				<select name="annotation_type_id" style="vertical-align: middle;">
					<option value="">all</a>
					{foreach from=$annotation_types item=at}
						<option value="{$at.annotation_type_id}" {if $at.annotation_type_id==$annotation_type_id}selected="selected"{/if}>{$at.name} ({$at.c})</a>
					{/foreach}
				</select>
				</div>

				<div class="filter" style="margin-top: 3px;">
				<span>Subcorpus:</span>
				<select name="subcorpus_id" style="vertical-align: middle;">
					<option value="">all</a>
					{foreach from=$subcorpora item=s}
						<option value="{$s.subcorpus_id}" {if $s.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$s.name}</a>
					{/foreach}
				</select>
				</div>

				<div class="filter" style="margin-top: 3px;">
					<span>Phrase:</span>
					<input type="text" name="phrase" value="{$phrase}"/>
				</div>

				<div class="filter" style="padding: 0; margin-top: 5px;">
					<input type="submit" class="btn btn-primary btn-xs" value="Apply">
				</div>
				<div style="clear: both;"></div>
			</form>
		</div>

		<div class="container-fluid" style="margin-top: 5px;">
			<div class="row">
				<div class="col-md-3">
					<div id="annotation_frequency">
						<div class="panel panel-default">
							<div class="panel-heading">Annotation frequency</div>
							<div class="panel-body" style="padding: 5px">
								<div class="flexigrid">
									<table id="annotation_frequency_table">
									  <tr>
										  <td style="vertical-align: middle"><div>Loading ... </div></td>
									  </tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-9">
					<div id="annotation_distribution">
						<div class="panel panel-default">
							<div class="panel-heading">
								<div id="countby">Count: <a href="#" class="active words" type="words">words</a>/<a href="#" class="documents" type="documents">documents</a></div>
								Annotation distribution across subcorpora
							</div>
							<div class="panel-body" style="padding: 0">
								<div id="annotations_per_subcorpus">There are no annotations to display</div>
								<div id="chart_link" target="_blank" style="text-align: right"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
