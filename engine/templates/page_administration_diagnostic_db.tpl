{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}
{include file="inc_administration_top.tpl"}

<div class="container-fluid admin_tables administration-diagnostic-access administration-diagnostic-db">
	<div class="panel panel-primary administration-content-panel administration-diagnostic-panel">
		<div class="panel-heading administration-content-heading">
			<span class="administration-content-heading-icon"><i class="fa fa-database"></i></span>
			<span>Database status</span>
			<span class="administration-diagnostic-counter">{$variables|@count} variables</span>
		</div>
		<div class="panel-body">
			<div class="administration-diagnostic-table-wrapper administration-diagnostic-db-table-wrapper">
				<table class="tablesorter table table-striped table-hover administration-table administration-diagnostic-table administration-diagnostic-db-table" cellspacing="1">
					<thead>
						<tr>
							<th>Variable</th>
							<th>Value</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$variables item=v}
						<tr>
							<td><code>{$v.name}</code></td>
							<td><span class="administration-diagnostic-db-value">{$v.value}</span></td>
						</tr>
					{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
