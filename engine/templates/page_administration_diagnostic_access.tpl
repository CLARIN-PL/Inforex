{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-diagnostic-access">
	<div class="panel panel-primary administration-content-panel administration-diagnostic-panel">
		<div class="panel-heading administration-content-heading">
			<span class="administration-content-heading-icon"><i class="fa fa-stethoscope"></i></span>
			<span>Page and ajax access rules</span>
			<span class="administration-diagnostic-counter">
				<span id="administration-diagnostic-access-visible-count">{$items|@count}</span> / {$items|@count}
			</span>
		</div>
		<div class="panel-body">
			<div class="administration-diagnostic-toolbar">
				<label for="administration-diagnostic-access-filter">Filter</label>
				<input class="form-control" id="administration-diagnostic-access-filter" type="text" placeholder="Search by class, role, description...">
			</div>
			<div class="administration-diagnostic-table-wrapper">
				<table id="administration-diagnostic-access-table" class="table table-striped table-hover sortable administration-table administration-diagnostic-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Class</th>
							<th>Parent</th>
							<th>System roles</th>
							<th>Corpus roles</th>
							<th>checkPermission</th>
							<th>Description</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$items item=item}
						<tr class="diagnostic-access-row">
							<td>
								<strong>{$item->name}</strong>
								{if $item->filename}
									<span class="administration-diagnostic-file">{$item->filename}</span>
								{/if}
							</td>
							<td><code>{$item->className}</code></td>
							<td><code>{$item->parentClassName}</code></td>
							<td>
								<div class="administration-diagnostic-badges">
									{foreach from=$item->anySystemRole item=r}
										<span class="administration-diagnostic-badge {if $r=="public_user"}administration-diagnostic-badge-success{else}administration-diagnostic-badge-danger{/if}" title="{$r|escape}">{$r}</span>
									{/foreach}
								</div>
							</td>
							<td>
								<div class="administration-diagnostic-badges">
									{foreach from=$item->anyCorpusRole item=r}
										<span class="administration-diagnostic-badge {if $r=="corpus_role_is_public"}administration-diagnostic-badge-warning{else}administration-diagnostic-badge-danger{/if}" title="{$r|escape}">{$r}</span>
									{/foreach}
								</div>
							</td>
							<td>
								{if $item->checkPermissionBody}
									<pre class="administration-diagnostic-code">{$item->checkPermissionBody}</pre>
								{else}
									<span class="administration-diagnostic-empty">Default</span>
								{/if}
							</td>
							<td>
								{if $item->description}
									<span class="administration-description-preview" title="{$item->description|escape}">{$item->description}</span>
								{else}
									<span class="administration-diagnostic-empty">No description</span>
								{/if}
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
