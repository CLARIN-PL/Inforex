{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
{include file="inc_header2.tpl"}

<div class="container-fluid admin_tables corpus-stats-page">
	<div class="row corpus-stats-grid">

		<div class="col-md-9 corpus-stats-column">
			<div class="panel administration-content-panel corpus-stats-panel">
				<div class="panel-heading administration-content-heading corpus-stats-heading">
                    <span class="administration-content-heading-icon"><i class="fa fa-bar-chart" aria-hidden="true"></i></span>
                    <span>Corpus statistics</span>
                </div>
				<div class="panel-body corpus-stats-panel-body">
                    <div class="corpus-stats-note">
                        Number of words in accepted documents.
                        <br/>
                        Word is a sequence of characters matching regex
                        <em><code>(\pL|\pM|\pN)+</code></em>
                        according to
                        <a href="http://www.regular-expressions.info/unicode.html" target="_blank" rel="noopener noreferrer">regular-expressions.info</a>.
                    </div>

					<div class="administration-table-wrapper corpus-stats-table-wrapper">
						<table cellspacing="1" class="table table-striped table-hover administration-table corpus-stats-table">
							<thead>
							<tr>
								<th class="corpus-stats-subcorpus-column">Subcorpus</th>
								<th class="corpus-stats-number-column">Documents <br/><small>only accepted</small></th>
								<th class="corpus-stats-number-column">Words</th>
								<th class="corpus-stats-number-column">Characters <br/><small>(no whitespaces)</small></th>
								<th class="corpus-stats-number-column">Tokens</th>
							</tr>
							</thead>
							<tbody>
							  {foreach from=$stats item=item key=key}
								{if $key eq "summary" }
									{capture name=summary}
									<tr class="corpus-stats-summary-row">
										<th>TOTAL</th>
										<th class="td-right">{$item.documents|number_format:0:",":"."}</th>
										<th class="td-right">{$item.words|number_format:0:",":"."}</th>
										<th class="td-right">{$item.chars|number_format:0:",":"."}</th>
										<th class="td-right">{$item.tokens|number_format:0:",":"."}</th>
									</tr>
									{/capture}
								{else}
								<tr>
									<th>{$item.name}</th>
									<td class="td-right">{$item.documents|number_format:0:",":"."}</td>
									<td class="td-right">{$item.words|number_format:0:",":"."}</td>
									<td class="td-right">{$item.chars|number_format:0:",":"."}</td>
									<td class="td-right">{$item.tokens|number_format:0:",":"."}</td>
								</tr>
								{/if}
							  {/foreach}
							</tbody>
							<tfoot>
							   {$smarty.capture.summary}
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 corpus-stats-column">
            <div class="corpus-stats-filter-wrapper">
                {include file="inc_metadata_filter.tpl"}
            </div>
		</div>
	</div>
</div>

{include file="inc_footer.tpl"}
