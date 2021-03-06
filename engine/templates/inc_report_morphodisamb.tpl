{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
 
<div id="dialog" title="Błąd" style="display: none;">
	<p>
		<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>
		<span class="message"></span>
	</p>
	<p><i><a href="">Refresh page.</a></i></p>
</div>

<div class="col-main {if $flags_active}col-md-11{else}col-md-12{/if} scrollingWrapper" id="col-main">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div id="widget_text" class="panel-body column" style="padding: 0">
			<div id="content">
				<div id="leftContent" style="float:left; width: {if $showRight}50%; border-right: 1px solid #E0CFC2{else}100%;{/if}" class="annotations scrolling content">
				  <div style="margin: 5px" class="contentBox {$report.format}">{$content|format_annotations}</div>
				</div>
				<div style="clear:both"></div>
			</div>
		</div>
	</div>
	<div class="panel panel-primary">
		<div class="panel-heading">Morphological disambiguation</div>
		<div id="widget_text" class="panel-body column" style="padding: 0">
				<div id="morpho-tagger" class="row">
					<div class="overlay" data-module-id="overlay">
						<p data-module-id="overlay-text">Please choose annotators to compare. <br>
							(Press <i class="fa fa-cog fa-4" aria-hidden="true"></i> icon in top right corner)
						</p>
					</div>
					<div class="col-sm-1">
						<button id='prev' type="button" class="btn btn-secondary btn-side-morpho"><span class="glyphicon glyphicon-chevron-left"></span></button>
					</div>

					<div class="col-sm-2 token-card">
						<div class="token-card-content">
							<h4 class="morpho-token text-center">Token</h4>
							<ul class="possible-tags-list">
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
							</ul>
						</div>
					</div>
					<div class="col-sm-2 token-card">
						<div class="token-card-content">
							<h4 class="morpho-token text-center">Token</h4>
							<ul class="possible-tags-list">
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
							</ul>
						</div>
					</div>
					<div class="col-sm-2 token-card card-main">
						<div class="token-card-content">
							<h4 class="morpho-token text-center">Token</h4>
							<ul class="possible-tags-list">
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
							</ul>
							<hr>

							<div class="form-inline" id="editable-select-container">
								<input id='lemma-base' type="text" class="form-control" placeholder="base">
								<select id="editable-select" class="form-control" placeholder="tag"></select>
							</div>
							<button type="button" id="add-tag" class="btn btn-primary btn-block"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>&nbsp;Add interpretation option</button>
						</div>
					</div>
					<div class="col-sm-2 token-card">
						<div class="token-card-content">
							<h4 class="morpho-token text-center">Token</h4>
							<ul class="possible-tags-list">
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
							</ul>
						</div>
					</div>
					<div class="col-sm-2 token-card">
						<div class="token-card-content">
							<h4 class="morpho-token text-center">Token</h4>
							<ul class="possible-tags-list">
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
								<li>lemma tag:1:2:3</li>
							</ul>
						</div>
					</div>

					<div class="col-sm-1">
						<button id='next' type="button" class="btn btn-secondary btn-side-morpho"><span class="glyphicon glyphicon-chevron-right"></span></button>
					</div>
				</div>
			<div style="clear:both"></div>
		</div>
	</div>

	<div class="panel-group" role="tablist" aria-multiselectable="true">
		<div class="panel panel-default">
			<div class="panel-heading" role="tab">
				<h4 class="panel-title">
					<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseInstructions" aria-expanded="false" aria-controls="collapseInstructions">
						See instructions <i class="fa fa-caret-down" aria-hidden="true"></i>
					</a>
				</h4>
			</div>
			<div id="collapseInstructions" class="panel-collapse collapse " role="tabpanel" aria-labelledby="headingOne">
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-12">
							<ul>
								<li>
									Choose tags using up <i class="fa fa-caret-up" aria-hidden="true"></i> or down <i class="fa fa-caret-down" aria-hidden="true"></i> arrow keys and pressing space bar or by clicking on the selected tag.
								</li>
								<li>
									By holding CTLR button you can select multiple tags.
								</li>
								<li>
									Tags marked with <i class="fa fa-check-circle" aria-hidden="true"></i> icon and blue background will be saved as your decision.
								</li>
								<li>
									In order to save the chosen tag for token in the middle card move to the next card. This is accomplished by clicking on the arrow buttons on the left and right or pressing left <i class="fa fa-caret-left" aria-hidden="true"></i> or right <i class="fa fa-caret-right" aria-hidden="true"></i> arrow keys.
								</li>
								<li>
									To add missing tag fill the 'base' and 'tag' inputs and press "<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add interpretation option" (note that both 'base' and 'tag' are required).
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

</div>

{literal}
<script>
    $(function () {
        var morphoTokenTags = {/literal}{$tokensTags|@json_encode};{literal}
        var morphoModule = new MorphoTagger($('#morpho-tagger'), $('span.token'), morphoTokenTags, $('#editable-select'));
    });
</script>
{/literal}
