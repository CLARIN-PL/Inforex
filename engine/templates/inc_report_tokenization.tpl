{*
 * Part of the Inforex project
 * Copyright (C) 2013 Michał Marcińczuk, Jan Kocoń, Marcin Ptak
 * Wrocław University of Technology
 * See LICENCE 
 *}
<div id="col-content" class="col-main {if $flags_active}col-md-8{else}col-md-9{/if} scrollingWrapper">
	<div class="panel panel-primary">
		<div class="panel-heading">Document content</div>
		<div class="panel-body" style="padding: 0">
			<div id="leftContent" style="float:left; width: {if $showRight}50%{else}100%{/if}; border-right: 1px solid #E0CFC2" class="annotations scrolling content">
				<div style="margin: 5px" class="contentBox {$report.format}">{$content_inline|format_annotations}</div>
			</div>
		</div>
	</div>
</div>

<div class = "col-md-3 scrollingWrapper">
	<div class = "panel panel-primary">
		<div class = "panel-heading">Tokenization</div>
		<div class = "panel-body scrolling">
			<div class = "panel panel-default">
				<div class = "panel-heading">Using Web Service</div>
				<div class = "panel-body">
					<h4>Polish</h4>
					{*
					<div class="radio">
						<label><input type="radio" name="task" id="nlprest2-morphodita"/> Morphodita</label>
					</div>
					*}
					<div class="radio">
						<label><input {if $report.lang == "pol" || !$report.lang}checked {/if}type="radio" name="task" id="nlprest2-wcrft2-morfeusz1"/> Wcrft2 (Morfeusz1)</label>
					</div>
					<div class="radio">
						<label><input type="radio" name="task" id="nlprest2-wcrft2-morfeusz2"/> Wcrft2 (Morfeusz2)</label>
					</div>
					<h4>English</h4>
					<div class="radio">
						<label><input {if $report.lang == "eng"}checked {/if}type="radio" name="task" id="nlprest2-en"/> spaCy English</label>
					</div>
					<h4>German</h4>
					<div class="radio">
						<label><input {if $report.lang == "ger"}checked {/if}type="radio" name="task" id="nlprest2-de"/> spaCy German</label>
					</div>
					<h4>Russian</h4>
					<div class="radio">
						<label><input {if $report.lang == "rus"}checked {/if}type="radio" name="task" id="nlprest2-ru"/> UDPipe Russian</label>
					</div>
					<h4>Hebrew</h4>
					<div class="radio">
						<label><input {if $report.lang == "heb"}checked {/if}type="radio" name="task" id="nlprest2-he"/> UDPipe Hebrew</label>
					</div>
				</div>
				<div class="panel-footer">
					<div class="form-group">
						<button class="btn btn-primary" id="tokenizeText">Tokenize</button>
					</div>
					<div class="form-group">
						<div id="process_status" class="alert alert-info" style="display: none;">
							<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
							<label for = "status">Status:</label>
							<span id = "status">Queued</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>