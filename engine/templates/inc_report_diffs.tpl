{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}

<div id="col-agreement" class="col-md-11 scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">History of document content modifications</div>
		<div class="panel-body" style="padding: 5px">

			<div class="scrolling">
				<ul id="diffs">
				{foreach from=$diffs item=diff}
					<li class="diff">
						<div class="panel panel-default">
							<div class="panel-heading">Modified on <b>{$diff.datetime}</b> by <em>{$diff.screename}</em></div>
							<div class="panel-body" style="padding: 0">

								<div class="header"></div>

								{if $diff.comment|trim != ""}
								<div class="comment">
								  <div class="subheader">Comment</div>
								  <div class="content">{$diff.comment}</div>
								</div>
								{/if}

								<div class="subheader2">Changes</div>
								<div class="diff">
								   {if $diff.diff_raw|strip_tags|trim != ""}
									  <pre style="border:1px solid #555; background: white; white-space: pre-wrap;" >{$diff.diff_raw}</pre>
								   {else}
									  <i>no changes</i>
								   {/if}
								</div>
							</div>
						</div>
					</li>
				{foreachelse}
					<li><i>There were no changes.</i></li>
				{/foreach}
				</ul>
			</div>

		</div>
	</div>
</div>