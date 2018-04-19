{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="panel panel-primary scrollingWrapper">
	<div class="panel-heading">Page and ajax access rules</div>
	<div class="panel-body">
		<input class="form-control" id="administration-diagnostic-ajax-filter" type="text" placeholder="Filter..">
		<div class="scrolling">
			<table id="administration-diagnostic-ajax-table" class="table table-striped table-hover sortable">
				<thead>
					<th>Name</th>
					<th>Used in</th>
				</thead>
				<tbody>
				{foreach from=$items key = name item = pages}
					<tr>
						<td>{$name}</td>
						<td>
							{assign var = "counter" value = 1}
							{foreach from = $pages key = page item = none}
								<strong>{$counter}.</strong> {$page}<br>
								{assign var = "counter" value = $counter+1}
							{/foreach}
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}