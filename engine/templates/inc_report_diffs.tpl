{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-agreement" class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper report-history-content-column">
	<div class="panel panel-primary administration-content-panel report-history-panel">
		<div class="panel-heading administration-content-heading report-history-heading">
			<span class="administration-content-heading-icon report-history-heading-icon"><i class="fa fa-history" aria-hidden="true"></i></span>
			<span>History of document content modifications</span>
		</div>
		<div class="panel-body report-history-body">

			<div class="scrolling report-history-scroll">
				<ul id="diffs">
				{foreach from=$diffs item=diff}
					<li class="diff">
						<div class="panel panel-default report-history-item">
							<div class="panel-heading report-history-item-heading">
								<span class="report-history-item-icon"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
								<span>Modified on <b>{$diff.datetime}</b> by <em>{$diff.screename}</em></span>
							</div>
							<div class="panel-body report-history-item-body">

								<div class="header"></div>

								{if $diff.comment|trim != ""}
								<div class="comment">
								  <div class="subheader report-history-subheader"><i class="fa fa-comment-o" aria-hidden="true"></i> Comment</div>
								  <div class="content report-history-comment">{$diff.comment}</div>
								</div>
								{/if}

								<div class="subheader2 report-history-subheader"><i class="fa fa-code-fork" aria-hidden="true"></i> Changes</div>
								<div class="diff report-history-diff">
								   {if $diff.diff_raw|strip_tags|trim != ""}
									  <pre>{$diff.diff_raw}</pre>
								   {else}
									  <i class="report-history-empty-inline">no changes</i>
								   {/if}
								</div>
							</div>
						</div>
					</li>
				{foreachelse}
					<li class="report-history-empty"><i class="fa fa-info-circle" aria-hidden="true"></i> There were no changes.</li>
				{/foreach}
				</ul>
			</div>

		</div>
	</div>
</div>
