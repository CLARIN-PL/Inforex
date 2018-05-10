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
					<th>Access problem</th>
					<th>Name</th>
					<th>Used in JS files</th>
					<th>Line number</th>
					<th>Pages</th>
					<th>Parent class name</th>
					<th>Ajax system roles</th>
					<th>Page system roles</th>
					<th>Ajax corpus roles</th>
					<th>Page corpus roles</th>
				</thead>
				<tbody>
				{foreach from=$items key = name item = elements}
					<tr>
						<td class = "text-center">
                            {if $elements.access_problem}
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true">
									{* Hack - allows the list to be sortable by this column *}
									<div style = "display: none">1</div>
								</span>
                            {/if}
						</td>
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
						<td>
                            {if !empty($elements.CPages)}
                                {assign var = "counter" value = 1}
                                {foreach from = $elements.CPages item = page}
									<strong>{$counter}.</strong> {$page->className}<br>
                                    {assign var = "counter" value = $counter+1}
                                {/foreach}
                            {else}
								- not found -
                            {/if}
						</td>
						<td>{$elements.parentClassName}</td>
						<td>{foreach from=$elements.anyAjaxSystemRole item=r}<button type="button" class="btn {if $r=="public_user"}btn-success{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{foreach from=$elements.anyPageSystemRole item=r}<button type="button" class="btn {if $r=="public_user"}btn-success{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{foreach from=$elements.anyAjaxCorpusRole item=r}<button type="button" class="btn {if $r=="corpus_role_is_public"}btn-warning{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{foreach from=$elements.anyPageCorpusRole item=r}<button type="button" class="btn {if $r=="corpus_role_is_public"}btn-warning{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}