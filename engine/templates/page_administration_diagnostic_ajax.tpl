{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

{assign var="error_count" value=0}

<div class="container-fluid admin_tables administration-diagnostic-access administration-diagnostic-ajax">
	<div class="panel panel-primary administration-content-panel administration-diagnostic-panel">
		<div class="panel-heading administration-content-heading">
			<span class="administration-content-heading-icon"><i class="fa fa-exchange"></i></span>
			<span>Ajax usage and access rules</span>
			<span class="administration-diagnostic-counter">
				<span id="administration-diagnostic-ajax-visible-count">{$items|@count}</span> / {$items|@count}
			</span>
		</div>
		<div class="panel-body">
			<div class="administration-diagnostic-toolbar">
				<label for="administration-diagnostic-ajax-filter">Filter</label>
				<input class="form-control" id="administration-diagnostic-ajax-filter" type="text" placeholder="Search by ajax class, page, role...">
			</div>
			<div class="administration-diagnostic-table-wrapper">
				<table id="administration-diagnostic-ajax-table" class="table table-striped table-hover sortable administration-table administration-diagnostic-table administration-diagnostic-ajax-table">
					<thead>
						<tr>
							<th>Ajax class</th>
							<th>Parent</th>
							<th>Used in JS files</th>
							<th>Pages</th>
							<th>Ajax system roles</th>
							<th>Ajax corpus roles</th>
							<th>Relation</th>
							<th>Page system roles</th>
							<th>Page corpus roles</th>
							<th>Access</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$items key = name item = elements}
						<tr class="diagnostic-ajax-row {if $elements.access_problem}diagnostic-ajax-row-error{/if}">
							<td>
								<strong>{$name}</strong>
								{if $elements.description}
									<span class="administration-description-preview" title="{$elements.description|escape}">{$elements.description}</span>
								{/if}
							</td>
							<td><code>{$elements.parentClassName}</code></td>
							<td>
								{if !empty($elements.files)}
									<ol class="administration-diagnostic-list">
										{foreach from = $elements.files key=page item=line_num}<li><code>{$page}:{$line_num}</code></li>{/foreach}
									</ol>
								{else}
									<span class="administration-diagnostic-empty">Not found</span>
								{/if}
							</td>
							<td>
								{if !empty($elements.CPages)}
									<ol class="administration-diagnostic-list">
										{foreach from = $elements.CPages item = page}<li><code>{$page->className}</code></li>{/foreach}
									</ol>
								{else}
									<span class="administration-diagnostic-empty">No pages</span>
								{/if}
							</td>
							<td>
								<div class="administration-diagnostic-badges">
									{foreach from=$elements.anyAjaxSystemRole item=r}
										<span class="administration-diagnostic-badge {if $r=="public_user"}administration-diagnostic-badge-success{else}administration-diagnostic-badge-info{/if}" title="{$r|escape}">{$r}</span>
									{/foreach}
								</div>
							</td>
							<td>
								<div class="administration-diagnostic-badges">
									{foreach from=$elements.anyAjaxCorpusRole item=r}
										<span class="administration-diagnostic-badge {if $r=="corpus_role_is_public"}administration-diagnostic-badge-warning{else}administration-diagnostic-badge-info{/if}" title="{$r|escape}">{$r}</span>
									{/foreach}
								</div>
							</td>
							<td class="administration-diagnostic-relation">⊆</td>
							<td>
								<div class="administration-diagnostic-badges">
									{foreach from=$elements.anyPageSystemRole item=r}
										<span class="administration-diagnostic-badge {if $r=="public_user"}administration-diagnostic-badge-success{else}administration-diagnostic-badge-info{/if}" title="{$r|escape}">{$r}</span>
									{/foreach}
								</div>
							</td>
							<td>
								<div class="administration-diagnostic-badges">
									{foreach from=$elements.anyPageCorpusRole item=r}
										<span class="administration-diagnostic-badge {if $r=="corpus_role_is_public"}administration-diagnostic-badge-warning{else}administration-diagnostic-badge-info{/if}" title="{$r|escape}">{$r}</span>
									{/foreach}
								</div>
							</td>
							<td class="text-center">
								{if $elements.access_problem}
									<span class="administration-diagnostic-status administration-diagnostic-status-error" title="Possible access error">
										<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
										<span>Issue</span>
										{* Hack - allows the list to be sortable by this column *}
										<span style="display: none">access-error</span>
									</span>
									{assign var="error_count" value=$error_count+1}
								{else}
									<span class="administration-diagnostic-status administration-diagnostic-status-valid" title="Access valid">
										<i class="fa fa-check" aria-hidden="true"></i>
										<span>Valid</span>
										{* Hack - allows the list to be sortable by this column *}
										<span style="display: none">access-valid</span>
									</span>
								{/if}
							</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer administration-content-footer administration-diagnostic-footer">
			<span>Possible access errors</span>
			<span class="administration-diagnostic-error-count {if $error_count>0}has-errors{/if}">{$error_count}</span>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
