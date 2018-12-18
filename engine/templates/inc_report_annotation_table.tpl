{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-content" class="col-main {if $flags_active && $config_active}col-md-11{elseif $flags_active}col-md-11{elseif $config_active}col-md-12{else}col-md-12{/if} scrollingWrapper">
	<div class="panel panel-default">
		<div class="panel-heading">Annotations</div>
		<div class="panel-body" style="padding: 0">
			<div id="content">
				<div id="leftContent" style="width: 100%; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
					<table class="table table-striped" id="public" cellspacing="1">
						<thead>
						<tr>
							<th style="text-align: left;">Text phrase</th>
							<th style="text-align: left;">Lemma</th>
							<th style="text-align: left">Category</th>
							<th style="text-align: right; width: 50px"></th>
						</tr>
						</thead>
						<tbody id="public_corpora_table">
						{foreach from=$anns item=ann}
							<tr title="{$ann.id}">
								<td>{$ann.text}</td>
								<td>{$ann.lemma}</td>
								<td>{$ann.type}</td>
								<td></td>
							</tr>
						{/foreach}
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>