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
							<div class="form-control" id="editable-select-container">
								<select id="editable-select"></select>
							</div>
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

<script>
	var morphoTokenTags = {$tokensTags|@json_encode};
</script>

