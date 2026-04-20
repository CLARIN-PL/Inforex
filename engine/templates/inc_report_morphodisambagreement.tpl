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

<div class="col-main col-md-{bootstrap_column_width default=8 flags=$flags_active config=$config_active} scrollingWrapper report-morpho-agreement-main-column" id="col-main">
	<div class="panel panel-primary administration-content-panel report-morpho-agreement-panel report-morpho-agreement-content-panel">
		<div class="panel-heading administration-content-heading report-morpho-agreement-heading">
			<span class="administration-content-heading-icon report-morpho-agreement-heading-icon"><i class="fa fa-file-text-o" aria-hidden="true"></i></span>
			<span>Document content</span>
		</div>
		<div id="widget_text" class="panel-body column report-morpho-agreement-content-body">
			<div id="content">
				<div id="leftContent" style="float:left; width: {if $showRight}50%;{else}100%;{/if}" class="annotations scrolling content report-morpho-agreement-document-content">
				  <div class="contentBox {$report.format} report-morpho-agreement-content-box">{$content|format_annotations}</div>
				</div>
				<div style="clear:both"></div>
			</div>
		</div>
	</div>
	<div class="panel panel-primary administration-content-panel report-morpho-agreement-panel report-morpho-agreement-tagger-panel">

		<div class="panel-heading administration-content-heading report-morpho-agreement-heading">
			<span class="administration-content-heading-icon report-morpho-agreement-heading-icon"><i class="fa fa-check-square-o" aria-hidden="true"></i></span>
			<span>Morphological disambiguation</span>
		</div>

		<div id="widget_text" class="panel-body column report-morpho-agreement-tagger-body">
				<div id="morpho-tagger" class="morpho-tagger-agreement row report-morpho-agreement-tagger">
					<div class="overlay" data-module-id="overlay">
						<p data-module-id="overlay-text"></p>
					</div>
					<div class="col-sm-1">
						<button id='prev' type="button" class="btn btn-secondary btn-side-morpho report-morpho-agreement-nav-button" title="Previous token"><span class="glyphicon glyphicon-chevron-left"></span></button>
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
							<div class="row">
								<div class="col-xs-4 col-tag-list-annotators text-center">
									<i>Annotator A</i><br>
									<i><b>{ $annotatorAName }</b></i>
									<ul class="possible-tags-list annotator" data-annotator="a">
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
									</ul>
								</div>
								<div class="col-xs-4 col-tag-list-annotators text-center">
									<i>Final <br> Decision</i>
									<ul class="possible-tags-list">
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
									</ul>
								</div>
								<div class="col-xs-4 col-tag-list-annotators text-center">
									<i>Annotator B</i> <br>
									<i><b>{ $annotatorBName }</b></i>
									<ul class="possible-tags-list annotator" data-annotator="b">
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
									</ul>
								</div>
							</div>

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
					<div class="col-sm-1">
						<button id='next' type="button" class="btn btn-secondary btn-side-morpho report-morpho-agreement-nav-button" title="Next token"><span class="glyphicon glyphicon-chevron-right"></span></button>
					</div>
				</div>
			<div style="clear:both"></div>
		</div>
	</div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper report-morpho-agreement-config-column" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info administration-content-panel report-morpho-agreement-panel report-morpho-agreement-config-panel">
		<div class="panel-heading administration-content-heading report-config-heading report-morpho-agreement-heading">
			<span class="administration-content-heading-icon report-morpho-agreement-heading-icon"><i class="fa fa-cog" aria-hidden="true"></i></span>
			<span>View configuration</span>
		</div>
		<div class="panel-body report-morpho-agreement-config-body">
			<div class="scrolling report-morpho-agreement-config-scroll">
                {include file="inc_widget_user_selection_a_b.tpl"}
			</div>
		</div>
		<div class="panel-footer report-morpho-agreement-config-footer">
			<form method="GET">
                {* The information about selected annotation sets, subsets and types is passed through URL parameters *}
				<input type="hidden" name="page" value="report"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="hidden" name="subpage" value="morphodisambagreement"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<button class="btn btn-primary report-morpho-agreement-apply-button" type="submit" id="apply">
					<i class="fa fa-check" aria-hidden="true"></i>
					<span>Apply configuration</span>
				</button>
			</form>
		</div>
	</div>
</div>


<script>

    setupUserSelectionAB("morpho");

    $(function () {ldelim}
        var morphoTokenTags = {$tokensTags|@json_encode};
	var finalDecision = {$finalTagsDecision|@json_encode};
        var annotatorADecisions = {$tokensTagsAnnotatorA|@json_encode};
        var annotatorBDecisions = {$tokensTagsAnnotatorB|@json_encode};


        var morphoModuleAgree = new MorphoTaggerAgree($('#morpho-tagger'), $('span.token'), morphoTokenTags, $('#editable-select'), finalDecision, annotatorADecisions, annotatorBDecisions);
    {rdelim});

</script>
