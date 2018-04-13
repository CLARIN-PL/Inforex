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
		<input class="form-control" id="administration-diagnostic-access-filter" type="text" placeholder="Filter..">
		<div class="scrolling">
			<table id="administration-diagnostic-access-table" class="table table-striped table-hover sortable">
				<thead>
					<th>Name</th>

					<th>Class name</th>
					<th>Parent class name</th>
					<th>System roles</th>
					<th>Corpus roles</th>
					<th>checkPermission</th>
				</thead>
				<tbody>
				{foreach from=$items item=item}
					<tr>
						<td>{$item->name}</td>
						<td>{$item->className}</td>
						<td>{$item->parentClassName}</td>
						<td>{foreach from=$item->anySystemRole item=r}<button type="button" class="btn {if $r=="public_user"}btn-success{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{foreach from=$item->anyCorpusRole item=r}<button type="button" class="btn {if $r=="corpus_role_is_public"}btn-warning{else}btn-danger{/if} btn-xs" style="margin: 3px">{$r}</button>{/foreach}</td>
						<td>{if $item->checkPermissionBody}<pre style="width: 700px; overflow: auto">{$item->checkPermissionBody}</pre>{/if}</td>
						<td>{$item->description}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}