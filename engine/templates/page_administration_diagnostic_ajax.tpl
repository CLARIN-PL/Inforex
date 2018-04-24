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
					<th>Used in JS files</th>
					<th>Line number</th>
					<th>Parent class name</th>
					<th>System roles</th>
					<th>Corpus roles</th>
					<th>Access problem</th>
				</thead>
				<tbody>
				{foreach from=$items key = name item = elements}
					<tr>
						<td>{$name}</td>
						<td>
							{if !empty($elements.files)}
                                {assign var = "counter" value = 1}
                                {foreach from = $elements.files key = page item = line_num}
									<strong>{$counter}.</strong> {$page}<br>
                                    {assign var = "counter" value = $counter+1}
                                {/foreach}
							{else}
								- not found -
							{/if}
						</td>
						<td>
                            {if !empty($elements.files)}
                                {foreach from = $elements.files item = line_num}
									{$line_num}<br>
                                {/foreach}
                            {/if}
						</td>
						<td>{$elements.parentClassName}</td>
						<td>{foreach from=$elements.anySystemRole item=r}<button type="button" class="btn {if $r=="public_user"}btn-success{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{foreach from=$elements.anyCorpusRole item=r}<button type="button" class="btn {if $r=="corpus_role_is_public"}btn-warning{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td class = "text-center">
							{if $elements.access_problem}
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true">
									{* Hack - allows the list to be sortable by this column *}
									<div style = "display: none">1</div>
								</span>
							{/if}
						</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}