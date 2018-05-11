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
					<th>Ajax class</th>
					<th>Parent class name</th>
					<th>Used in JS files</th>
					<th>Pages</th>
					<th></th>
					<th>Ajax system roles</th>
					<th>Ajax corpus roles</th>
					<th></th>
					<th>Page system roles</th>
					<th>Page corpus roles</th>
					<th></th>
					<th>Access</th>
				</thead>
				<tbody>
				{foreach from=$items key = name item = elements}
					<tr>
						<td>{$name}</td>
						<td>{$elements.parentClassName}</td>
						<td>
							{if !empty($elements.files)}
								<ol>
                                {foreach from = $elements.files key=page item=line_num}<li>{$page}:{$line_num}</li>{/foreach}
								</ol>
							{/if}
						</td>
						<td>
                            {if !empty($elements.CPages)}
								<ol>
                                {foreach from = $elements.CPages item = page}<li>{$page->className}</li>{/foreach}
								</ol>
                            {/if}
						</td>
						<td>{ldelim}</td>
						<td>{foreach from=$elements.anyAjaxSystemRole item=r}<button type="button" class="btn {if $r=="public_user"}btn-success{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{foreach from=$elements.anyAjaxCorpusRole item=r}<button type="button" class="btn {if $r=="corpus_role_is_public"}btn-warning{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>}&nbsp;⊆&nbsp;{ldelim}</td>
						<td>{foreach from=$elements.anyPageSystemRole item=r}<button type="button" class="btn {if $r=="public_user"}btn-success{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{foreach from=$elements.anyPageCorpusRole item=r}<button type="button" class="btn {if $r=="corpus_role_is_public"}btn-warning{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>}</td>
						<td class = "text-center">
							{if $elements.access_problem}
								<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true" style="color: red">
									{* Hack - allows the list to be sortable by this column *}
									<div style = "display: none">1</div>
								</span>
							{else}
								<span class="glyphicon glyphicon-ok" aria-hidden="true" style="color: dodgerblue">
									{* Hack - allows the list to be sortable by this column *}
									<div style = "display: none">2</div>
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