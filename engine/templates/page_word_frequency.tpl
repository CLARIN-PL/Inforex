{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl" content_class="no_padding"}
<div class="panel panel-primary">
	<div class="panel-heading">Words frequency</div>
	<div class="panel-body" style="padding: 0">

		<div style="background: #eee; border-bottom: 1px solid #aaa; padding: 5px;">
			<form method="GET" action="index.php">
				<input type="hidden" name="page" value="{$page}"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<div class="filter" style="margin-top: 3px">
				<b>Filters:</b>
				</div>

				<div class="filter" style="margin-top: 3px">
				<span>Part of speech:</span>
				<select name="ctag" style="vertical-align: middle;">
					<option value="">all</a>
					{foreach from=$classes item=class}
						<option value="{$class}" {if $class==$ctag}selected="selected"{/if}>{$class}</a>
					{/foreach}
				</select>
				</div>

				<div class="filter" style="margin-top: 3px">
				<span>Subcorpus:</span>
				<select name="subcorpus_id" style="vertical-align: middle;">
					<option value="">all</a>
					{foreach from=$subcorpora item=s}
						<option value="{$s.subcorpus_id}" {if $s.subcorpus_id==$subcorpus_id}selected="selected"{/if}>{$s.name}</a>
					{/foreach}
				</select>
				</div>

				<div class="filter" style="margin-top: 3px">
				<span style="margin-left: 20px;">Phrase:</span>
				<input type="text" name="phrase" value="{$phrase}"/>
				</div>

				<div class="filter" style="padding: 0; margin-top: 7px;">
					<input type="submit" class="btn btn-primary btn-xs" value="Apply"/>
				</div>
				<div style="clear: both;"></div>
			</form>
		</div>

		<div class="container-fluid" style="margin-top: 5px;">
			<div class="row">
				<div class="col-md-3">
					<div id="words_frequency" style="">
						<div class="panel panel-default">
							<div class="panel-heading">Words frequency</div>
							<div class="panel-body" style="padding: 5px">

								<div class="flexigrid">
								<table id="words_frequences">
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
					<div id="words_distribution">
						<div class="panel panel-default">
							<div class="panel-heading">
								<div id="countby">Count: <a href="#" class="active words" type="words">words</a>/<a href="#" class="documents" type="documents">documents</a></div>
								Annotation distribution across subcorpora
							</div>
							<div class="panel-body" style="padding: 5px">
								<div id="words_per_subcorpus">There are no words to display</div>
								<div id="chart_link" target="_blank" style="text-align: right"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel-footer">
		<input type="button" id="export_selected" value="Export current frequency list to a CSV file" class="btn btn-primary btn-xs"/>
        <input type="button" id="export_by_subcorpora" value="Export frequency distribution to a CSV file" class="btn btn-primary btn-xs"/>
    </div>
</div>

{include file="inc_footer.tpl"}
