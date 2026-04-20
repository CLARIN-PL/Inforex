{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE
 *}

{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables corpus-dashboard-page">
	<div class="panel administration-content-panel corpus-dashboard-main-panel">
		<div class="panel-heading administration-content-heading">
			<span class="administration-content-heading-icon"><i class="fa fa-dashboard" aria-hidden="true"></i></span>
			<span>Corpus dashboard</span>
		</div>
		<div class="panel-body">
			<div class="row corpus-dashboard-grid">
				<div class="col-sm-4 corpus-dashboard-column">
					<div class="panel administration-content-panel corpus-dashboard-card">
						<div class="panel-heading administration-content-heading corpus-dashboard-card-heading">
							<span class="administration-content-heading-icon"><i class="fa fa-pie-chart" aria-hidden="true"></i></span>
							<span>Corpus structure</span>
							<span class="corpus-dashboard-counter">{$subcorpora|@count}</span>
							<div class="corpus-dashboard-heading-actions">
								{if "manager"|has_corpus_role_or_owner}
									<a class="btn corpus-dashboard-action corpus-dashboard-manage-action" title="Subcorpora" href="index.php?page=corpus_settings&amp;corpus={$corpus.id}&amp;subpage=subcorpora">
										<i class="fa fa-sitemap" aria-hidden="true"></i>
										<span class="corpus-dashboard-action-label">Subcorpora</span>
									</a>
								{/if}
								{if "add_document"|has_corpus_role_or_owner}
									<a class="btn corpus-dashboard-action corpus-dashboard-add-document" title="Add document" href="index.php?page=corpus_document_add&amp;corpus={$corpus.id}">
										<i class="fa fa-plus" aria-hidden="true"></i>
										<span class="corpus-dashboard-action-label">Add document</span>
									</a>
								{/if}
							</div>
						</div>
						<div class="panel-body">
							{if $subcorpora|@count == 0}
								<div class="corpus-dashboard-empty">
									<i class="fa fa-folder-open-o" aria-hidden="true"></i>
									<span>This corpus does not have any documents.</span>
								</div>
							{else}
								<div class="corpus-dashboard-chart-shell">
									<div class="corpus-dashboard-chart" id="piechart"></div>
									<div class="corpus-dashboard-chart-legend" id="piechart_legend"></div>
								</div>
								<script type="text/javascript">
									var chartDataSubcorpora = [
										['Subcorpus', 'documents']
										{foreach from=$subcorpora item=subcorpus}
											,['{$subcorpus.name|escape:'javascript'}', {$subcorpus.count}]
										{/foreach}
									];
								</script>
							{/if}
						</div>
					</div>
				</div>

				<div class="col-sm-8 corpus-dashboard-column">
					<div class="panel administration-content-panel corpus-dashboard-card">
						<div class="panel-heading administration-content-heading corpus-dashboard-card-heading">
							<span class="administration-content-heading-icon"><i class="fa fa-flag" aria-hidden="true"></i></span>
							<span>Document flags</span>
							<span class="corpus-dashboard-counter">{$flags|@count}</span>
							{if "manager"|has_corpus_role_or_owner}
								<div class="corpus-dashboard-heading-actions">
									<a class="btn corpus-dashboard-action corpus-dashboard-manage-action" title="Manage flags" href="index.php?page=corpus_settings&amp;corpus={$corpus.id}&amp;subpage=flags">
										<i class="fa fa-flag" aria-hidden="true"></i>
										<span class="corpus-dashboard-action-label">Manage flags</span>
									</a>
								</div>
							{/if}
						</div>
						<div class="panel-body">
							<div class="corpus-dashboard-filter">
								<label for="document_flags_filter">Filter</label>
								<div class="input-group corpus-dashboard-filter-search">
									<span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span>
									<input id="document_flags_filter" class="form-control" type="text" placeholder="Search flag, short or description..." autocomplete="off">
								</div>
							</div>
							<div class="administration-table-wrapper corpus-dashboard-table-wrapper">
								<table class="table table-striped table-hover administration-table corpus-dashboard-table" cellspacing="1">
									<thead>
										<tr>
											<th>Flag</th>
											<th class="td-center corpus-dashboard-short-column">Short</th>
											<th class="td-right corpus-dashboard-flag-count-column"><img title="not ready" src="gfx/flag_-1.png" alt="not ready"></th>
											<th class="td-right corpus-dashboard-flag-count-column"><img title="ready" src="gfx/flag_1.png" alt="ready"></th>
											<th class="td-right corpus-dashboard-flag-count-column"><img title="in progress" src="gfx/flag_2.png" alt="in progress"></th>
											<th class="td-right corpus-dashboard-flag-count-column"><img title="finished" src="gfx/flag_3.png" alt="finished"></th>
											<th class="td-right corpus-dashboard-flag-count-column"><img title="done" src="gfx/flag_4.png" alt="done"></th>
											<th class="td-right corpus-dashboard-flag-count-column"><img title="error" src="gfx/flag_5.png" alt="error"></th>
											<th class="corpus-dashboard-progress-column">Progress</th>
											<th>Description</th>
										</tr>
									</thead>
									<tbody>
										{foreach from=$flags item=flag}
											<tr>
												<td title="{$flag.description|escape}">
													<span class="corpus-dashboard-flag-name">{$flag.name}</span>
												</td>
												<td class="td-center" title="{$flag.name|escape}">
													<span class="corpus-dashboard-short-badge" title="{$flag.name|escape}">{$flag.short}</span>
												</td>
												<td class="td-right">
													{if $flag.f0==0}<span class="corpus-dashboard-zero corpus-dashboard-zero-not-ready">-</span>{else}<a class="corpus-dashboard-count-link corpus-dashboard-count-not-ready" href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.corpora_flag_id}=0&filter_order=flag_{$flag.corpora_flag_id}">{$flag.f0}</a>{/if}
												</td>
												<td class="td-right">
													{if $flag.f1==0}<span class="corpus-dashboard-zero corpus-dashboard-zero-ready">-</span>{else}<a class="corpus-dashboard-count-link corpus-dashboard-count-ready" href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.corpora_flag_id}=1&filter_order=flag_{$flag.corpora_flag_id}">{$flag.f1}</a>{/if}
												</td>
												<td class="td-right">
													{if $flag.f2==0}<span class="corpus-dashboard-zero corpus-dashboard-zero-progress">-</span>{else}<a class="corpus-dashboard-count-link corpus-dashboard-count-progress" href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.corpora_flag_id}=2&filter_order=flag_{$flag.corpora_flag_id}">{$flag.f2}</a>{/if}
												</td>
												<td class="td-right">
													{if $flag.f3==0}<span class="corpus-dashboard-zero corpus-dashboard-zero-finished">-</span>{else}<a class="corpus-dashboard-count-link corpus-dashboard-count-finished" href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.corpora_flag_id}=3&filter_order=flag_{$flag.corpora_flag_id}">{$flag.f3}</a>{/if}
												</td>
												<td class="td-right">
													{if $flag.f4==0}<span class="corpus-dashboard-zero corpus-dashboard-zero-done">-</span>{else}<a class="corpus-dashboard-count-link corpus-dashboard-count-done" href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.corpora_flag_id}=4&filter_order=flag_{$flag.corpora_flag_id}">{$flag.f4}</a>{/if}
												</td>
												<td class="td-right">
													{if $flag.f5==0}<span class="corpus-dashboard-zero corpus-dashboard-zero-error">-</span>{else}<a class="corpus-dashboard-count-link corpus-dashboard-count-error" href="index.php?page=corpus_documents&amp;corpus={$corpus.id}&amp;reset=1&amp;flag_{$flag.corpora_flag_id}=5&filter_order=flag_{$flag.corpora_flag_id}">{$flag.f5}</a>{/if}
												</td>
												<td>
													{math assign="total" equation='f1+f2+f3+f4+f5' f0=$flag.f0|intval f1=$flag.f1|intval f2=$flag.f2|intval f3=$flag.f3|intval f4=$flag.f4|intval f5=$flag.f5|intval}
													<div class="corpus-dashboard-progressbar" title="Flag distribution">
														<div class="corpus-dashboard-progress-ready" style="width: {if $total==0}0{else}{$flag.f1*100/$total}{/if}%"></div>
														<div class="corpus-dashboard-progress-progress" style="width: {if $total==0}0{else}{$flag.f2*100/$total}{/if}%"></div>
														<div class="corpus-dashboard-progress-finished" style="width: {if $total==0}0{else}{$flag.f3*100/$total}{/if}%"></div>
														<div class="corpus-dashboard-progress-done" style="width: {if $total==0}0{else}{$flag.f4*100/$total}{/if}%"></div>
														<div class="corpus-dashboard-progress-error" style="width: {if $total==0}0{else}{$flag.f5*100/$total}{/if}%"></div>
													</div>
												</td>
												<td>
													<div class="corpus-dashboard-description" title="{$flag.description|escape}">{if $flag.description}{$flag.description}{else}n/a{/if}</div>
												</td>
											</tr>
										{/foreach}
										{if $flags|@count == 0}
											<tr>
												<td colspan="10" class="corpus-dashboard-empty-row">This corpus does not have any flags defined.</td>
											</tr>
										{/if}
									</tbody>
								</table>
							</div>
							<div class="corpus-dashboard-pagination" id="document_flags_pagination">
								<div class="corpus-dashboard-pagination-info" id="document_flags_pagination_info"></div>
								<div class="corpus-dashboard-pagination-controls" id="document_flags_pagination_controls"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
