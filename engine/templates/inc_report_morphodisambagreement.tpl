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

<div class="col-main col-md-{bootstrap_column_width default=8 flags=$flags_active config=$config_active} scrollingWrapper" id="col-main">
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
						<p>Please choose annotators to compare. <br>
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
					<div class="col-sm-2 token-card card-main">
						<div class="token-card-content">
							<h4 class="morpho-token text-center">Token</h4>
							<div class="row">
								<div class="col-xs-4 col-tag-list-annotators text-center">
                                    {if empty($annotatorAName)}
                                        {assign var='annotatorAName' value='annotator A'}
                                    {/if}
									<i>{ $annotatorAName }</i>
									<ul class="possible-tags-list annotator" data-annotator="a">
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
									</ul>
								</div>
								<div class="col-xs-4 col-tag-list-annotators text-center">
									<i>Final Decision</i>
									<ul class="possible-tags-list">
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
										<li>lemma tag:1:2:3</li>
									</ul>
								</div>
								<div class="col-xs-4 col-tag-list-annotators text-center">
                                    {if empty($annotatorBName)}
                                        {assign var='annotatorBName' value='annotator B'}
                                    {/if}
									<i>{ $annotatorBName }</i>
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
						<button id='next' type="button" class="btn btn-secondary btn-side-morpho"><span class="glyphicon glyphicon-chevron-right"></span></button>
					</div>
				</div>
			<div style="clear:both"></div>
		</div>
	</div>
</div>

<div id="col-config" class="col-md-3 scrollingWrapper" {if !$config_active}style="display: none"{/if}>
	<div class="panel panel-info">
		<div class="panel-heading">View configuration</div>
		<div class="panel-body" style="padding: 0">
			<div class="scrolling">
                {include file="inc_widget_user_selection_a_b.tpl"}
			</div>
		</div>
		<div class="panel-footer">
			<form method="GET">
                {* The information about selected annotation sets, subsets and types is passed through URL parameters *}
				<input type="hidden" name="page" value="report"/>
				<input type="hidden" name="corpus" value="{$corpus.id}"/>
				<input type="hidden" name="subpage" value="morphodisambagreement"/>
				<input type="hidden" name="id" value="{$report.id}"/>
				<input class="btn btn-primary" type="submit" value="Apply configuration" id="apply"/>
			</form>
		</div>
	</div>
</div>


<script>
    {literal}

    setupUserSelectionAB(true);

    $(function () {
        var morphoTokenTags = {/literal}{$tokensTags|@json_encode};{literal}
		var finalDecision = {/literal}{$finalTagsDecision|@json_encode};{literal}
        var annotatorADecisions = {/literal}{$tokensTagsAnnotatorA|@json_encode};{literal}
        var annotatorBDecisions = {/literal}{$tokensTagsAnnotatorB|@json_encode};{literal}


        var morphoModuleAgree = new MorphoTaggerAgree($('#morpho-tagger'), $('span.token'), morphoTokenTags, $('#editable-select'), finalDecision, annotatorADecisions, annotatorBDecisions);
    });


	{/literal}
</script>

